<?php 

if (isset($categoryData)) {
	$categoryDataId = $categoryData['catid'];
}

echo "<form method='POST' action='./includes/handlers/admin-process.php'>
	<input id='categoryId' name='catid' type='hidden'";
	if (isset($categoryDataId)) { echo " value='" . $categoryDataId . "'"; }
			echo ">

	<div class='form-group'>
		<label for='categoryName'>Name of Category</label>
		<input type='text' name='categoryName' class='form-control' id='categoryName' placeholder='Enter category name'"; 
			if (isset($categoryData)) { echo " value='" . oStringSan($categoryData['name']) . "'"; }
			echo " required>
	</div>
	<input type='hidden'";

	if (isset($categoryData)) { 
		echo " name='updateCategoryNonce-" . $categoryDataId . "' value='" . csrf_getNonce('updateCategory-' . $categoryDataId) . "'";
	} else {
		echo " name='addCategoryNonce' value='" . csrf_getNonce('addCategory') . "'";
	}
	
	echo ">
	<button type='submit' name='" . $formName . "' class='btn btn-sm btn-primary'>Submit</button>
</form>";
?>