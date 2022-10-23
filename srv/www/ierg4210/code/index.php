<?php
	include("includes/config.php");
	include("includes/security/outputSanitization.php");
	
	// Check if page is reloaded by ajax
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		
	} else {
		include("includes/partials/header.php");
	}

	// Retrive catid from URL
	if (isset($_GET['catid'])) {
		$catid = $_GET['catid'];
	} else {
		$catid = 1;
	}

	// Get category name according to catid
	$c1->execute(array($catid));
	$categoryName = $c1->fetch()['name'];
?>

<div class="container-fluid">
	<section class="twentyFiveLeft" id="listOfCategory">
		<div class="list-group">
			<?php
				$c0->execute(array());
				while($categoryData = $c0->fetch()){
				    echo "<span style='cursor:pointer' role='link' tabindex='0' onclick='openPage(\"./index.php?catid=" . $categoryData['catid'] . "\")' class='list-group-item list-group-item-action ";
				    if ($categoryData['catid'] == $catid) {
				    	echo "active";
				    }
				    echo "'>"
				    	. oStringSan($categoryData['name']) . "</span>";
				}
			?>
		</div>
	</section>

	<div class="seventyFiveRight">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a class="custom-link" onclick="openPage('./index.php')">Home</a></li>
				<li class="breadcrumb-item active" aria-current="page"><?php echo oStringSan($categoryName); ?></li>
			</ol>
		</nav>

		<section id="listOfProduct">
			<?php
				$p1a->execute(array($catid));
				while($productData = $p1a->fetch()){
				    echo "<div class='card productCard'>
							<span style='cursor:pointer' role='link' tabindex='0' onclick='openPage(\"./product.php?pid=" . $productData['pid'] . "\")'><img class='card-img-top' src='" . $productData['imagePath'] . "' alt='" . oStringSan($productData['name']) . "'></span>
							<div class='card-body'>
								<span style='cursor:pointer' role='link' tabindex='0' onclick='openPage(\"./product.php?pid=" . $productData['pid'] . "\")'><h5 class='card-title'>" . nameTruncate(oStringSan($productData['name'])) . "</h5></span>
										<p class='card-text'>$" . $productData['price'] . "</p>
								<span style='cursor:pointer' role='link' tabindex='0' class='btn btn-primary' onclick='addToCartButtonClicked(\"" . $productData['pid'] . "\");'>Add To Cart</span>
							</div>
						</div>";
				}
			?>
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