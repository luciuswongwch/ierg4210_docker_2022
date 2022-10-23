<?php 

if (isset($productData)) {
	$productDataId = $productData['pid'];
}

echo "<form method='POST' action='./includes/handlers/admin-process.php' enctype='multipart/form-data'>";
	
	if (isset($productDataId)) { echo "
		<input id='productId' name='pid' type='hidden'
	 		value='" . $productDataId . "'>"; }
			 
  	echo "<div class='form-group'>
		<label for='categoryId'>Category Id</label>
	    <select class='form-control' name='categoryId' id='categoryId' required>
	    	<option disabled selected value> -- Select an Option -- </option>";
	    	$c0->execute(array());
			while($c0Data = $c0->fetch()){
				echo "<option ";
				if (isset($productData)) {
					if ($productData["catid"] == $c0Data["catid"]) {
						echo "selected='selected'";
					}
				}
				echo ">" . $c0Data["catid"] . " - " . oStringSan($c0Data["name"]) . "</option>";
			}
	    echo "</select>
		<small id='categoryIdHelp' class='form-text text-muted'>Select the category id according to the category name.</small>
	</div>

	<div class='form-group'>
    	<label for='productName'>Name of Product</label>
    	<input type='text' name='productName' class='form-control' id='productName' placeholder='Enter Product Name'"; 
			if (isset($productData)) { echo " value='" . oStringSan($productData['name']) . "'"; }
			echo " required>
  	</div>

  	<div class='form-group'>
    	<label for='productPrice'>Price of Product</label>
    	<input type='number' name='productPrice' step='0.01' onchange='setTwoNumberDecimal' class='form-control' id='productPrice' placeholder='Enter Product Price'"; 
			if (isset($productData)) { echo " value='" . $productData['price'] . "'"; }
			echo " required>
  	</div>

	<div class='form-group'>
	    <label for='productDescription'>Product Description</label>
	    <textarea class='form-control' name='productDescription' id='productDescription' rows='2' required>"; 
			if (isset($productData)) { echo oStringSan($productData['description']); }
			echo "</textarea>
	</div>

	<div class='form-group'>
	    <label>Product Image</label>
		<div class='dropArea'>
			<p>Upload file with the file dialog or by dragging and dropping image onto the dashed region</p>
			<input type='file' name='productImageFile' class='productImageFile' accept='image/x-png,image/gif,image/jpeg'";
			if ($formName == "addProductForm") {
				echo "required";
			}
			echo ">
			<div class='imagePreview'></div>
		</div>
	</div>
	<input type='hidden'";

	if (isset($productDataId)) { 
		echo " name='updateProductNonce-" . $productDataId . "' value='" . csrf_getNonce('updateProduct-' . $productDataId) . "'";
	} else {
		echo " name='addProductNonce' value='" . csrf_getNonce('addProduct') . "'";
	}

	echo ">
	<button type='submit' name='" . $formName . "' class='btn btn-sm btn-primary'>Submit</button>
</form>";
?>