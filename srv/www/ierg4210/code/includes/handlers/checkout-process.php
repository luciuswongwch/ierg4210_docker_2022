<?php

include("../config.php");
include("../security/csrf.php");
include("../security/inputSanitization.php");

$returnValueArray = array();
$returnValueArray["error"] = array();
$returnValueArray["productName"] = array();
$returnValueArray["productPrice"] = array();

function getUserEmail() {
	if(!empty($_SESSION['auth'])) {
		return $_SESSION['auth']['email'];
	}
	if(!empty($_COOKIE['auth'])) {
		if($decoded_json = json_decode(stripcslashes($_COOKIE['auth']), true)) {
			if(time() > $decoded_json['expire']) {
				$dateTime = new DateTime();
				return "guest_" . $dateTime->format('Y-m-d_H:i:s');
			} else {
				return $decoded_json['email'];
			}
		}
	}
	$dateTime = new DateTime();
	return "guest_" . $dateTime->format('Y-m-d_H:i:s');
}

if(isset($_POST['pidArray']) && isset($_POST['quantityArray'])) {
	if (csrf_verifyNonce('checkout', $_POST['checkoutNonce'])) {
		$error = false;
		$pidArray = $_POST['pidArray'];
		$quantityArray = $_POST['quantityArray'];
		$productNameArray = array();
		$productPriceArray = array();
		for ($i = 0; $i < sizeOf($pidArray); $i++) {
			$p1->execute(array($pidArray[$i]));
			if($quantityArray[$pidArray[$i]] != 0 && $shoppingListData = $p1->fetch()) {
				array_push($productNameArray, $shoppingListData['name']);
				array_push($productPriceArray, $shoppingListData['price']);
			} else {
				$error = true;
				array_push($returnValueArray["error"], "The shopping cart data is incorrect. Please clear the shopping cart and try again later.");
				break;
			}
		}

		if (!$error) {
			$totalPrice = 0;
			for ($i = 0; $i < sizeOf($pidArray); $i++) {
				$totalPrice += $quantityArray[$pidArray[$i]] * $productPriceArray[$i];
			}
			
			$merchantEmail = "sb-s8tzs21136414@business.example.com";
			$currency = "CAD";

			$user = getUserEmail();
			$salt = uniqid();

			$shoppingCartInformation = json_encode(array(
				'pidArray' => $pidArray,
				'quantityArray' => $quantityArray,
				'productPriceArray' => $productPriceArray
			));

			$digest = sha1($currency.$merchantEmail.$salt.$shoppingCartInformation.$totalPrice);

			$o2->execute(array($digest, $salt, $user, $shoppingCartInformation));
			$invoice = $connect->lastInsertId();

			$returnValueArray["merchantEmail"] = $merchantEmail;
			$returnValueArray["currency"] = $currency;
			$returnValueArray["invoice"] = $invoice;
			$returnValueArray["digest"] = $digest;

			$returnValueArray["productPid"] = $pidArray;
			$returnValueArray["productQuantity"] = $quantityArray;
			$returnValueArray["productName"] = $productNameArray;
			$returnValueArray["productPrice"] = $productPriceArray;
		}
	} else {
		array_push($returnValueArray["error"], "There was a temporary security issue. Please try again later.");
	}

} else {
	array_push($returnValueArray["error"], "Shopping cart data is not passed to the server correctly");
}

echo json_encode($returnValueArray);

?>