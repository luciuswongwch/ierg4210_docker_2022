<?php

include("../config.php");
include("../security/adminAuthentication.php");
include("../security/csrf.php");
include("../security/inputSanitization.php");

function uploadProductImage() {
	$fileDir = "images/products";
	$tempFilename = $_FILES['productImageFile']['name'];
	$ext = pathinfo($tempFilename, PATHINFO_EXTENSION);
	$uniqueId = uniqid();
	$filePath = $fileDir . "/" . $uniqueId . "." . $ext;

	$uploadOk = 1;
	
	// Check if image file is an image
	$check = getimagesize($_FILES["productImageFile"]["tmp_name"]);
	if($check !== false) {
    	$uploadOk = 1;
	} else {
    	$uploadOk = 0;
	}

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    return null;
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["productImageFile"]["tmp_name"], "../../" . $filePath)) {
	        return $filePath;
	    } else {
	        return null;
	    }
	}
}

function deleteOldImage($p1, $productId) {
	$p1->execute(array($productId));
	$p1Data = $p1->fetch();
	$productImagePath = $p1Data['imagePath'];

	unlink("../../" . $productImagePath);
}

if(isset($_POST['addCategoryForm'])) {
	if (csrf_verifyNonce('addCategory', $_POST['addCategoryNonce'])) {
		$add_CategoryName = sanitizeFormString($_POST['categoryName']);

		echo "<script>console.log('" . $add_CategoryName . "')</script>";

		$c2->execute(array($add_CategoryName));

		header("Location: ../../admin.php");
	} else {
		echo "Potential CSRF security risk detected. Please try again later.";
	}
}

if(isset($_POST['updateCategoryForm'])) {
	$tempAction = "updateCategory-" . $_POST['catid'];
	$tempNonceName = "updateCategoryNonce-" . $_POST['catid'];
	$tempNonce = $_POST[$tempNonceName];
	if (csrf_verifyNonce($tempAction, $tempNonce)) {
		$update_CategoryId = $_POST['catid'];
		$update_CategoryName = sanitizeFormString($_POST['categoryName']);

		echo "<script>console.log('" . $update_CategoryId . "')</script>";
		echo "<script>console.log('" . $update_CategoryName . "')</script>";

		$c3->execute(array($update_CategoryName, $update_CategoryId));

		header("Location: ../../admin.php");
	} else {
		echo "Potential CSRF security risk detected. Please try again later.";
	}
}

if(isset($_POST['addProductForm'])) {
	if (csrf_verifyNonce('addProduct', $_POST['addProductNonce'])) {
		$add_CategoryId = preg_replace("/( - ).*/", "", $_POST['categoryId']);
		$add_productName = sanitizeFormString($_POST['productName']);
		$add_productPrice = $_POST['productPrice'];
		$add_productDescription = sanitizeFormString($_POST['productDescription']);

		echo "<script>console.log('" . $add_CategoryId . "')</script>";
		echo "<script>console.log('" . $add_productName . "')</script>";
		echo "<script>console.log('" . $add_productPrice . "')</script>";
		echo "<script>console.log('" . $add_productDescription . "')</script>";

		$add_productImagePath = uploadProductImage();

		$p2->execute(array($add_CategoryId, $add_productName, $add_productPrice, $add_productDescription, $add_productImagePath));

		echo "<script>console.log('product details has been inserted')</script>";

		header("Location: ../../admin.php");
	} else {
		echo "Potential CSRF security risk detected. Please try again later.";
	}
}

if(isset($_POST['updateProductForm'])) {
	$tempAction = "updateProduct-" . $_POST['pid'];
	$tempNonceName = "updateProductNonce-" . $_POST['pid'];
	$tempNonce = $_POST[$tempNonceName];
	if (csrf_verifyNonce($tempAction, $tempNonce)) {

		$update_ProductId = $_POST['pid'];
		$update_CategoryId = preg_replace("/( - ).*/", "", $_POST['categoryId']);
		$update_productName = sanitizeFormString($_POST['productName']);
		$update_productPrice = $_POST['productPrice'];
		$update_productDescription = sanitizeFormString($_POST['productDescription']);

		echo "<script>console.log('" . $update_ProductId . "')</script>";
		echo "<script>console.log('" . $update_CategoryId . "')</script>";
		echo "<script>console.log('" . $update_productName . "')</script>";
		echo "<script>console.log('" . $update_productPrice . "')</script>";
		echo "<script>console.log('" . $update_productDescription . "')</script>";

		if($_FILES['productImageFile']['name'] != null) {
			$update_productImagePath = uploadProductImage();
			if($update_productImagePath != null) {
				deleteOldImage($p1, $update_ProductId);
				$p3->execute(array($update_CategoryId, $update_productName, $update_productPrice, $update_productDescription, $update_productImagePath, $update_ProductId));
			}
		} else {
			$p3a->execute(array($update_CategoryId, $update_productName, $update_productPrice, $update_productDescription, $update_ProductId));
		}

		header("Location: ../../admin.php");
	} else {
		echo "Potential CSRF security risk detected. Please try again later.";
	}
}

?>