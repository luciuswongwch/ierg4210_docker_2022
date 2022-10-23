<?php

if(isset($_SESSION['auth'])) {
	$isLoggedIn = $_SESSION['auth']['userId'];
} else {
	if(!empty($_COOKIE['auth'])) {
		if($decoded_json = json_decode(stripcslashes($_COOKIE['auth']), true)) {
			if(time() > $decoded_json['expire']) {
				$isLoggedIn = false;
			} else {
				$isLoggedIn = $decoded_json['userId'];
			}
		} else {
			$isLoggedIn = false;
		}
	} else {
		$isLoggedIn = false;
	}
}

include("includes/security/csrf.php");

?>

<!DOCTYPE html>
<html>
<head>
	<title>Ecommerce</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" type="image/png" href="images/favicon.png"/>
	<link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
	<!-- Google Login -->
	<script src="https://accounts.google.com/gsi/client" async defer></script>
	<!-- Facebook Sign-In -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = 'https://connect.facebook.net/zh_HK/sdk.js#xfbml=1&version=v3.2&appId=816197963150617&autoLogAppEvents=1';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<header>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="./"><img class="logo" src="images/logo.png"></a>
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
	        		
	      		</li>
	      	</ul>
	      	<?php if($isLoggedIn){echo "<a href='./portal.php'>";} ?><button type="button" class="btn btn-secondary" <?php if(!$isLoggedIn) { echo "data-toggle='modal' data-target='#modalCenter'"; } ?>>Hello! <?php if (!$isLoggedIn) { echo "Guest"; } else { echo "user-" . $isLoggedIn; } ?></button><?php if($isLoggedIn){echo "</a>";} ?>
			<div id="shoppingList" class="navbar-nav ml-auto">
				<div id="shoppingTotalDisplay">
					<img class="icon" src="images/icons/icon_shoppingCart.png">
					<span id="subtotal">$0</span>
				</div>
				<div id="shoppingListCollapse">
					<div id="checkoutFormErrorMessage"></div>
					<table class="table table-sm">
						<thead>
							<tr>
								<th scope="col"></th>
								<th scope="col">Product</th>
								<th scope="col">Quantity</th>
								<th scope="col">Price</th>
							</tr>
						</thead>
						<tbody id="AJAXShoppingList">
							
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th>Total</th>
								<th></th>
								<th>$<span id="AJAXShoppingListTotal">0</span></th>
							</tr>
						</tfoot>
					</table>

					<form method="POST" action="https://www.sandbox.paypal.com/cgi-bin/webscr" onsubmit="return ui_cart_submit(event, this)">
						<input type='hidden' name='checkoutNonce' id='checkoutNonce' value=<?php echo "'" . csrf_getNonce('checkout') . "'"; ?>>
						<input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="upload" value="1">
						<input type="hidden" name="business" value="sb-s8tzs21136414@business.example.com">
						<input type="hidden" name="charset" value="utf-8">
						<input type="hidden" name="currency_code" value="CAD">
						<input type="hidden" name="invoice" value="0">
						<input type="hidden" name="custom" value="0">
						<input type="submit" id="checkoutButton" class="btn btn-primary" value="Checkout">
					</form>
				</div>
			</div>
		</nav>
	</header>

	<?php include("./includes/partials/auth_form.php"); ?>

	<div id="mainContent">