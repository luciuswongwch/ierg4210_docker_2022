<?php

include("../config.php");
include("../security/inputSanitization.php");

// STEP 1: read POST data
// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
// Instead, read raw POST data from the input stream.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2) {
		$myPost[$keyval[0]] = urldecode($keyval[1]);
	}
}
// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
$req = 'cmd=_notify-validate';
if (function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}


// Step 2: POST IPN data back to PayPal to validate
$ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if ( !($res = curl_exec($ch)) ) {
	error_log("Got " . curl_error($ch) . " when processing IPN data");
	curl_close($ch);
	exit;
}
curl_close($ch);


// inspect IPN validation result and act accordingly
if (strcmp ($res, "VERIFIED") == 0) {
	// The IPN is verified, process it
	$checkoutError = false;
	// check whether the payment_status is Completed
	$payment_status = sanitizeFormString($_POST['payment_status']);
	if ($payment_status != "Completed") {
		$checkoutError = true;
	}
	// check that txn_id has not been previously processed
	$txn_id = sanitizeFormString($_POST['txn_id']);
	$o1a->execute(array($txn_id));
	while ($checkTxnId = $o1a->fetch()) {
		if ($txn_id == $checkTxnId["txn_id"]) {
			$checkoutError = true;
		}
	}
	// check whether the txn_type is cart
	$txn_type = sanitizeFormString($_POST['txn_type']);
	if ($txn_type != "cart") {
		$checkoutError = true;
	}
	// check that receiver_email is your Primary PayPal email
	$receiver_email = sanitizeFormString($_POST['receiver_email']);
	if ($receiver_email != "sb-s8tzs21136414@business.example.com") {
		$checkoutError = true;
	}

	if (!$checkoutError) {
		$pidArray = array();
		$quantityArray = array();
		$productPriceArray = array();

		$maxIndex = 0;
	
		for ($i = 0; $i < $_POST['num_cart_items']; $i++) {
			$pidArray[$i] = $_POST['item_number' . ($i + 1)];
			$productPriceArray[$i] = (string) round($_POST['mc_gross_' . ($i + 1)] / $_POST['quantity' . ($i + 1)], 1);
			if ($pidArray[$i] > $maxIndex) {
				$maxIndex = $pidArray[$i];
			}
		}

		for ($i = 0; $i < $maxIndex; $i++) {	
			$quantityArray[$i] = "";
		}

		for ($i = 0; $i < $_POST['num_cart_items']; $i++) {
			$quantityArray[$pidArray[$i]] = $_POST['quantity' . ($i + 1)];
		}

		$shoppingCartInformation = json_encode(array(
				'pidArray' => $pidArray,
				'quantityArray' => $quantityArray,
				'productPriceArray' => $productPriceArray
		));
	
		$totalPrice = 0;
		for ($i = 0; $i < sizeOf($pidArray); $i++) {
			$totalPrice += $quantityArray[$pidArray[$i]] * $productPriceArray[$i];
		}

		$o1->execute(array($_POST['invoice']));
		$orderData = $o1->fetch();
		$digestCheck = $orderData["digest"];
		$salt = $orderData["salt"];

		$digest = sha1(sanitizeFormString($_POST['mc_currency']).$receiver_email.$salt.$shoppingCartInformation.$totalPrice);

		if ($digestCheck == $digest) {
			$o3->execute(array($txn_id, $shoppingCartInformation, (int) $_POST['invoice']));
		}
	}

	/*
	// IPN message values depend upon the type of notification sent.
  	// To loop through the &_POST array and print the NV pairs to the screen:
  	foreach($_POST as $key => $value) {
    	$allVariables .= $key . " = " . $value . "<br>";
  	}
  	$o2->execute(array($allVariables, "allVariables", "allVariables"));
  	*/
  	
} else if (strcmp ($res, "INVALID") == 0) {
	// IPN invalid, log for manual investigation
	echo "The response from IPN was: <b>" .$res ."</b>";
}

?>