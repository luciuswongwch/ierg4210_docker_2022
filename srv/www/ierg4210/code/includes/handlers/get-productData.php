<?php
include("../config.php");

if(isset($_POST['pid'])) {
	$p1->execute(array($_POST['pid']));
	while($productData = $p1->fetch()) {
		echo json_encode([$productData['name'], $productData['price']]);
	}
}

?>