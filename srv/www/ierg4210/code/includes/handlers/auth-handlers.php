<?php

include("../config.php");
include("../security/csrf.php");
include("../security/inputSanitization.php");

$dataAndErrorArray = array();
$isAdmin = 0;

function validateEmail($u1, $em) {
	global $dataAndErrorArray;
	if(!filter_var($em, FILTER_VALIDATE_EMAIL)) {
		array_push($dataAndErrorArray, "Email is invalid");
		return;
	}
	$u1->execute(array($em));
	if ($data = $u1->fetch()) {
		array_push($dataAndErrorArray, "This email has been registered");
	}
}

function validatePassword($pd, $confirmPd) {
	global $dataAndErrorArray;
	if($pd != $confirmPd) {
		array_push($dataAndErrorArray, "Your passwords don't match");
		return;
	}
	if(preg_match('/[^A-Za-z0-9]/', $pd)) {
		array_push($dataAndErrorArray, "Your password can only contain numbers and letters");
		return;
	}
	if(strlen($pd) > 30 || strlen($pd) < 5) {
		array_push($dataAndErrorArray, "Your password must be between 5 and 30 characters");
		return;
	}
}

function cookiesAndSession($data) {
	$expire = time() + 3600*24*3;
	$token = array(
		'userId' => $data['userid'],
		'email' => $data['email'],
		'isAdmin' => $data['isAdmin'],
		'expire' => $expire,
		'hashedPassword' => md5($data['password'].$data['salt'])
	);
	setcookie("auth", json_encode($token), $expire, "/", "", true, true);
	$_SESSION["auth"] = $token;
}

if(isset($_POST['userLoginForm'])) {
	if (csrf_verifyNonce('login', $_POST['loginNonce'])) {
		$login_email = sanitizeEmail(sanitizeFormString($_POST['loginEmail']));
		$login_password = sanitizeFormString($_POST['loginPassword']);
		$u1->execute(array($login_email));
		if($u1Data = $u1->fetch()) {
			$saltedPassword = $u1Data['password'];
			$salt = $u1Data['salt'];
			$isAdmin = $u1Data['isAdmin'];
			if (md5($login_password.$salt) == $saltedPassword) {
				cookiesAndSession($u1Data);
				session_regenerate_id();
			} else {
				array_push($dataAndErrorArray, "There was an error with your E-Mail/Password combination. Please try again.");
			}
		} else {
			array_push($dataAndErrorArray, "There was an error with your E-Mail/Password combination. Please try again.");
		}
	} else {
		array_push($dataAndErrorArray, "There was a temporary security issue. Please try again later.");
	}
	array_push($dataAndErrorArray, $isAdmin);
	echo json_encode($dataAndErrorArray);
}

if(isset($_POST['userSignupForm'])) {
	if (csrf_verifyNonce('signUp', $_POST['signUpNonce'])) {
		$signup_email = sanitizeEmail(sanitizeFormString($_POST['signUpEmail']));
		$signup_password = sanitizeFormString($_POST['signUpPassword']);
		$signup_confirmPassword = sanitizeFormString($_POST['signUpConfirmPassword']);
		$signup_adminCode = sanitizeFormString($_POST['signUpAdminCode']);

		validateEmail($u1, $signup_email);
		validatePassword($signup_password, $signup_confirmPassword);

		if ($signup_adminCode != "") {
			$cred1->execute(array(1));
			$adminCode = $cred1->fetch()['adminCode'];
			if ($signup_adminCode == $adminCode) {
				$isAdmin = 1;
			}
		}
		if(empty($dataAndErrorArray) == true) {
			$salt = uniqid();
			$encryptedPw = md5($signup_password.$salt);
			$result = $u2->execute(array($signup_email, $encryptedPw, $isAdmin, $salt));
			if ($result) {
				$u1->execute(array($signup_email));
				cookiesAndSession($u1->fetch());
				session_regenerate_id();
			}
		}
	} else {
		array_push($dataAndErrorArray, "There was a temporary security issue. Please try again later.");
	}
	array_push($dataAndErrorArray, $isAdmin);
	echo json_encode($dataAndErrorArray);
}


if(isset($_POST['googleSignIn'])) {

	$googleSignIn_email = "googleSignIn_" . sanitizeEmail(sanitizeFormString($_POST['googleSignInEmail']));
	$googleSignIn_password = md5($googleSignIn_email);
	$googleSignIn_salt = md5($googleSignIn_password);

	$googleSignIn_encryptedPassword = md5($googleSignIn_password.$googleSignIn_salt);

	$u1->execute(array($googleSignIn_email));
	if($u1Data = $u1->fetch()) {
		// this is not the first time login
	} else {
		// For first time login, write the user to database
		$result = $u2->execute(array($googleSignIn_email, $googleSignIn_encryptedPassword, $isAdmin, $googleSignIn_salt));
	}
	
	$u1->execute(array($googleSignIn_email));
	cookiesAndSession($u1->fetch());
	session_regenerate_id();

	array_push($dataAndErrorArray, $isAdmin);
	echo json_encode($dataAndErrorArray);
}

if(isset($_POST['facebookSignIn'])) {

	$facebookSignIn_email = "facebookSignIn_" . sanitizeEmail(sanitizeFormString($_POST['facebookSignInEmail']));
	$facebookSignIn_password = md5($facebookSignIn_email);
	$facebookSignIn_salt = md5($facebookSignIn_password);

	$facebookSignIn_encryptedPassword = md5($facebookSignIn_password.$facebookSignIn_salt);

	$u1->execute(array($facebookSignIn_email));
	if($u1Data = $u1->fetch()) {
		// this is not the first time login
	} else {
		// For first time login, write the user to database
		$result = $u2->execute(array($facebookSignIn_email, $facebookSignIn_encryptedPassword, $isAdmin, $facebookSignIn_salt));
	}
	
	$u1->execute(array($facebookSignIn_email));
	cookiesAndSession($u1->fetch());
	session_regenerate_id();

	array_push($dataAndErrorArray, $isAdmin);
	echo json_encode($dataAndErrorArray);
}



?> 