<?php

include("includes/config.php");
include("includes/security/outputSanitization.php");
include("includes/partials/header_simple.php");

function getUserEmail() {
	if(!empty($_SESSION['auth'])) {
		return $_SESSION['auth']['email'];
	}
	if(!empty($_COOKIE['auth'])) {
		if($decoded_json = json_decode(stripcslashes($_COOKIE['auth']), true)) {
			if(time() > $decoded_json['expire']) {
				return false;
			} else {
				return $decoded_json['email'];
			}
		}
	}
}

function adminAuthentication($u1a) {
	if(!empty($_SESSION['auth'])) {
		return $_SESSION['auth']['isAdmin'];
	}
	if(!empty($_COOKIE['auth'])) {
		if($decoded_json = json_decode(stripcslashes($_COOKIE['auth']), true)) {
			if(time() > $decoded_json['expire']) {
				return false;
			} else {
				$u1a->execute(array(1));
				while($u1aData = $u1a->fetch()) {
					if ($decoded_json['hashedPassword'] == md5($u1aData['password'].$u1aData['salt'])) {
						$_SESSION['auth'] = $decoded_json;
						return $decoded_json['isAdmin'];
					};
				}
				return false;
			}
		}
	}
}

$userEmail = getUserEmail();

?>

<div class="container-fluid">
	<section class="twentyFiveLeft" id="listOfCategory">
		<div class="list-group">
			<span role="link" tabindex="0" class="list-group-item list-group-item-action portal-item active" id="userDetailsButton">User Details</span>
			<span role="link" tabindex="0" class="list-group-item list-group-item-action portal-item" id="recentOrderButton">Recent Orders</span>
			<span role="link" tabindex="0" class="list-group-item list-group-item-action portal-item" id="changePasswordButton">Change Password</span>
			<span role="link" tabindex="0" class="list-group-item list-group-item-action portal-item" data-toggle="modal" data-target="#logoutModal">Logout</span>
		</div>
	</section>

	<section class="seventyFiveRight">
		<div id="userDetails">
			<div class='card card-body'>
				<h5 class="card-title">User Information</h5>
				<hr>
				<p><em>Email Address:&ensp;</em><?php echo oStringSan($userEmail); ?></p>
				<?php if(adminAuthentication($u1a)) { echo "<p><em>Admin Access:&ensp;</em>True&ensp;<a href='admin.php'>(Go to Admin Page)</a></p>"; } ?>
			</div>
		</div>
		<div id="recentOrder" style="display:none">
			<div class='card card-body'>
				<h5 class="card-title">Items Purchased in Recent Orders</h5>
				<table class="table table-hover table-bordered">
				<thead>
				<tr>
				<th scope="col">Order Number</th>
				<th scope="col">Items</th>
				<th scope="col">Item Price</th>
				<th scope="col">Quantity</th>
				<th scope="col">Item Total</th>
				</tr>
				</thead>
				<tbody>
				<?php 
				if($userEmail) {
					$o1b->execute(array($userEmail));
					while($orderData = $o1b->fetch()) {
						if ($orderData["txn_id"] != "toBeUpdated") {
							$orderDisplay = "portalPage";
							include("includes/partials/orders_table.php");
						}
					}
				}
				?>
				</tbody>
				</table>
			</div>
		</div>
		<tr>
		<div id="unpaidOrder" style="display:none">
			<div class='card card-body'>
				<h5 class="card-title">Unpaid Orders</h5>
				<table class="table table-hover table-bordered">
				<thead>
				<tr>
				<th scope="col">Order Number</th>
				<th scope="col">Items</th>
				<th scope="col">Item Price</th>
				<th scope="col">Quantity</th>
				<th scope="col">Item Total</th>
				</tr>
				</thead>
				<tbody>
				<?php 
				if($userEmail) {
					$o1b->execute(array($userEmail));
					while($orderData = $o1b->fetch()) {
						if ($orderData["txn_id"] == "toBeUpdated") {
							$orderDisplay = "portalPage";
							include("includes/partials/orders_table.php");
						}
					}
				}
				?>
				</tbody>
				</table>
			</div>
		</div>
		<div id="changePassword" style="display:none">
			<div id="changePasswordErrorMessage"></div>
			<div class="card card-body">
				<form method="POST">
					<h5 class="card-title">Change Password Form</h5>
					<hr>
					<div class="form-group">
						<label for="currentPassword">Current Password</label>
						<input type="password" class="form-control" name="currentPassword" id="currentPassword" placeholder="Enter current password" required>
					</div>
					<div class="form-group">
						<label for="newPassword">New Password</label>
						<input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="New password" required>
					</div>
					<button type="submit" class="btn btn-primary" name="changePasswordForm">Submit</button>
				</form>
			</div>
		</div>
	</section>

	<div class="clearFloat"></div>

	<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Confirm Logout</h5>
					<button type="button" class="close" data-dismiss="modal">
						<span>&times;</span>
					</button>
				</div>
				<div class="modal-body">Are you sure you want to sign out of your account?</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<form method="POST">
						<button type="submit" class="btn btn-primary" name="logout" onclick="localStorage.removeItem('pidList');localStorage.removeItem('quantityList');signOut();facebookSignout();">Logout</button>
					</form>
				</div>
			</div>
		</div>
	</div>

</div>

<script>
	// Google Sign-Out
	function signOut() {
		var auth2 = gapi.auth2.getAuthInstance();
		auth2.signOut().then(function () {
			console.log('User signed out.');
		});
	}

	function onLoad() {
		gapi.load('auth2', function() {
			gapi.auth2.init();
		});
    }
    onLoad();

    // Facebook Sign-Out
    function facebookSignout() {
    	FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
				FB.logout(function(response) {
					// Person is now logged out
				});
			}
    	});
    }
</script>

<?php

function validateNewPassword($pw) {
	if(preg_match('/[^A-Za-z0-9]/', $pw)) {
		return "invalidCharacters";
	}
	if(strlen($pw) > 30 || strlen($pw) < 5) {
		return "invalidLength";
	}
	return true;
}

function validateCurrentPassword($u1b, $pw) {
	if(!empty($_SESSION['auth'])) {
		$userId = $_SESSION['auth']['userId'];

		$u1b->execute(array($userId));
		if($currentPasswordData = $u1b->fetch()) {
			if(md5($pw.$currentPasswordData['salt']) == $currentPasswordData['password']) {
				return $currentPasswordData['salt'];
			} else {
				return "wrongPassword";
			}
		} else {
			return "serverError";
		}
	} else {
		return "noSession";
	}
}

function logout() {
	setcookie("auth", "", time()-3600, "/");
	session_destroy();
	header("Location: index.php");
}

function displayErrorMessage($errorMessage) {
	echo "<script>$('#changePasswordErrorMessage').html($('#changePasswordErrorMessage').html() + \"<div class='alert alert-warning' role='alert'>" . $errorMessage . "</div>\");</script>";
}

if (isset($_POST['changePasswordForm'])) {
	echo "<script>
		$('#userDetailsButton').removeClass('active');
		$('#recentOrderButton').removeClass('active');
		$('#changePasswordButton').addClass('active');
		$('#userDetails').css('display', 'none');
		$('#recentOrder').css('display', 'none');
		$('#changePassword').css('display', 'block');
	</script>";

	// validate new password
	$newPasswordCheck = validateNewPassword($_POST['newPassword']);
	if ($newPasswordCheck === "invalidCharacters") {
		displayErrorMessage("Your password can only contain numbers and letters");
	} else if ($newPasswordCheck === "invalidLength") {
		displayErrorMessage("Your password must be between 5 and 30 characters");
	} else if ($newPasswordCheck) {
		// validate current password if new password can be accepted
		$tempSalt = validateCurrentPassword($u1b, $_POST['currentPassword']);
		if ($tempSalt == "noSession") {
			displayErrorMessage("Your login has been expired. Please log in again before changing your password.");
		} else if ($tempSalt == "serverError") {
			displayErrorMessage("There was a problem connecting to the server. Please try again later.");
		} else if ($tempSalt == "wrongPassword") {
			displayErrorMessage("The current password is incorrect");
		} else if ($tempSalt) {
			if(!empty($_SESSION['auth'])) {
				$userId = $_SESSION['auth']['userId'];
				$encrytedNewPassword = md5($_POST['newPassword'].$tempSalt);
				$u3->execute(array($encrytedNewPassword, $userId));
				if($u3) {
					logout();
				} else {
					displayErrorMessage("There was a problem connecting to the server. Please try again later.");
				}
			} else {
				displayErrorMessage("Your login has been expired. Please log in again before changing your password.");
			}
		} else {
			displayErrorMessage("There was a problem connecting to the server. Please try again later.");
		}
	}
}

if (isset($_POST['logout'])) {
	logout();
}

?>

</div>

	<footer>
		<p>Ecommerce</p>
		<p>2018 © Copyright</p>
	</footer>
	<script src="js/lib/jquery-3.3.1.min.js"></script>
	<script src="js/lib/popper.min.js"></script>
	<script src="js/lib/bootstrap.min.js"></script>
	<script src="js/body_script.js"></script>
	<script src="js/portal_script.js"></script>
</body>
</html>