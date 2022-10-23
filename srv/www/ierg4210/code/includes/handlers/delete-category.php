<?php
include("../config.php");

if(isset($_POST['catid'])) {
	$categoryId = $_POST['catid'];

	$c4->execute(array($categoryId));
}

?>