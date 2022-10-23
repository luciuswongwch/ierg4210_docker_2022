<?php

function oStringSan($input) {
	return htmlspecialchars($input);
}

function oUrlSan($url) {
	return urlencode($url);
}

function nameTruncate($string) {
	if (strlen($string) > 55) {
		return substr($string, 0, 55) . "...";
	} else {
		return $string;
	}
}

?>