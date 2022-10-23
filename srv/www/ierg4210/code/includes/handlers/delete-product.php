<?php
include("../config.php");

if(isset($_POST['pid'])) {
	$productId = $_POST['pid'];

	$p1->execute(array($productId));

	$deleteProductImagePath = $p1->fetch()['imagePath'];

	unlink("../../" . $deleteProductImagePath);

	$p4->execute(array($productId));
}

?>