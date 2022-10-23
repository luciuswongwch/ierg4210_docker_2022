<?php
	include("includes/config.php");
	include("includes/security/outputSanitization.php");

	// Check if page is reloaded by ajax
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		
	} else {
		include("includes/partials/header.php");
	}

	if (isset($_GET['pid'])) {
		$pid = $_GET['pid'];
	} else {
		// Redirect to main page if pid is not appended to url
		header("Location: index.php");
	}

	$p1->execute(array($pid));
	$productData = $p1->fetch();

	$c1->execute(array($productData['catid']));
	$categoryName = oStringSan($c1->fetch()['name']);
?>

<div class="container-fluid">
	<section class="twentyFiveLeft" id="listOfCategory">
		<div class="card">
			<div class="card-header productPageCategory" id="headingOne" data-toggle="collapse" data-target="#collapse_listOfCategory">
				Categories
			</div>
			<div id="collapse_listOfCategory" class="collapse show">
				<div class="card-body">
					<?php
						$c0->execute(array());
						while($categoryData = $c0->fetch()){
						    echo "<a style='cursor:pointer' role='link' tabindex='0' onclick='openPage(\"./index.php?catid=" . $categoryData['catid'] . "\")'>" . oStringSan($categoryData['name']) . "</a>
						    	<hr>";
						}
					?>	       
			    </div>
			</div>
		</div>
	</section>

	<div class="seventyFiveRight">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a class="custom-link" onclick="openPage('./index.php')">Home</a></li>
				<li class="breadcrumb-item">
					<a class="custom-link" onclick="openPage('./index.php?catid=' + <?php echo $productData['catid']; ?>)">
						<?php echo $categoryName; ?>
					</a>
				</li>
				<li class="breadcrumb-item active" aria-current="page"><?php echo oStringSan($productData["name"]); ?></li>
			</ol>
		</nav>

		<section id="product">
			<div class="productImage">
				<img src='<?php echo $productData["imagePath"]; ?>' alt='<?php echo $productData["name"]; ?>' class="img-thumbnail">
			</div>
			<div class="productDetails">
				<h4 class="productName"><?php echo oStringSan($productData["name"]); ?></h4>
				<h4 class="productPrice">$<?php echo $productData["price"]; ?></h4>
				<button onclick="addToCartButtonClicked('<?php echo $productData['pid']; ?>');" class="productButton btn btn-primary btn-lg">Add To Cart</button>
				<hr>
				<p class="productDescription"><?php echo oStringSan($productData["description"]); ?></p>
			</div>
		</section>
	</div>
	<div class="clearFloat"></div>
</div>


<?php
// Check if page is reloaded by ajax
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	
} else {
	// Include the footer file
	include("includes/partials/footer.php");
}
?>