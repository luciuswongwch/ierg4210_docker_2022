<?php

function sanitizeFormString($inputText) {
	$StrippedText = strip_tags($inputText);
	return $StrippedText;
}

function sanitizeEmail($inputEmail) {
	$SanitizedEmail = filter_var($inputEmail, FILTER_SANITIZE_EMAIL);
	return $SanitizedEmail;
}

?>