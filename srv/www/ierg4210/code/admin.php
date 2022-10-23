<?php
	include("includes/config.php");
	include("includes/security/adminAuthentication.php");
	include("includes/security/csrf.php");
	include("includes/security/outputSanitization.php");

	include("includes/partials/header_simple.php");
?>

	<div class="container-fluid">

		<div id="recentOrder">
			<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#completedOrderCard" aria-expanded="false" aria-controls="completedOrderCard">
				Orders with Completed Payment Status
			</button>
			<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#notCompletedOrderCard" aria-expanded="false" aria-controls="notCompletedOrderCard">
				Orders that are Not Paid by Customers
			</button>
			<div class="collapse" data-parent="#recentOrder" id="completedOrderCard">
				<div class='card card-body'>
					<h5 class="card-title">Orders with Completed Payment Status</h5>
					<table class="table table-hover table-bordered">
					<thead>
					<tr>
					<th scope="col">Order Number</th>
					<th scope="col">User</th>
					<th scope="col">Items</th>
					<th scope="col">Item Price</th>
					<th scope="col">Quantity</th>
					<th scope="col">Item Total</th>
					</tr>
					</thead>
					<tbody>
					<?php 
					$o0->execute(array());
					while($orderData = $o0->fetch()) {
						if ($orderData["txn_id"] != "toBeUpdated") {
							$orderDisplay = "adminPage";
							include("includes/partials/orders_table.php");
						}
					}
					?>
					</tbody>
					</table>
				</div>
			</div>
			<div class="collapse" data-parent="#recentOrder" id="notCompletedOrderCard">
				<div class='card card-body'>
					<h5 class="card-title">Orders that are Not Paid by Customers</h5>
					<table class="table table-hover table-bordered">
					<thead>
					<tr>
					<th scope="col">Order Number</th>
					<th scope="col">User</th>
					<th scope="col">Items</th>
					<th scope="col">Item Price</th>
					<th scope="col">Quantity</th>
					<th scope="col">Item Total</th>
					</tr>
					</thead>
					<tbody>
					<?php 
					$o0->execute(array());
					while($orderData = $o0->fetch()) {
						if ($orderData["txn_id"] == "toBeUpdated") {
							$orderDisplay = "adminPage";
							include("includes/partials/orders_table.php");
						}
					}
					?>
					</tbody>
					</table>
				</div>
			</div>
		</div>

		<hr>

		<div id="additionForm">
		  	<button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#categoryCard" aria-expanded="false" aria-controls="categoryCard">
				Add a Category
			</button>
			<button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#productCard"  aria-expanded="false" aria-controls="productCard">
		    	Add a Product
		  	</button>

			<div id="addCategoryForm">
				<div class="collapse" data-parent="#additionForm" id="categoryCard">
					<div class="card card-body">
						<?php
							$formName = "addCategoryForm";
							include("includes/partials/categories_form.php");
						?>
					</div>
				</div>
			</div>

			<div id="addProductForm">
				<div class="collapse" data-parent="#additionForm" id="productCard">
					<div class="card card-body">
						<?php
							$formName = "addProductForm";
							include("includes/partials/products_form.php");
						?>
					</div>
				</div>
			</div>
		</div>

		<hr>

		<h5>Category List</h5>
		<table class="table table-bordered table-hover" id="categoryTable">
	  		<thead>
			    <tr>
			    	<th scope="col" style="width: 20%; min-width: 135px"></th>
			    	<th scope="col">Category Id</th>
			    	<th scope="col">Name</th>
			    </tr>
	  		</thead>
	  		<tbody>
	  			<?php
	  				$c0->execute(array());
					while($categoryData = $c0->fetch()){
					    echo "<tr>
			    			<th scope='row'>
			    				<button class='btn btn-sm btn-info' type='button' data-toggle='collapse' data-target='#editCategory" . $categoryData['catid'] . "' aria-expanded='false' aria-controls='editCategory'>Edit</button>
			    				<button class='deleteCategoryButton btn btn-sm btn-danger' data-catid='" . $categoryData['catid'] . "' data-name='" . oStringSan($categoryData['name']) . "'>Delete</button>
			    			</th>
							<td>" . $categoryData['catid'] . "</td>
							<td>" . oStringSan($categoryData['name']) . "</td>
			    		</tr>
			    		<tr>
			    			<th style='padding:0' scope='row' colspan='3'>
			    				<div class='collapse' id='editCategory" . $categoryData['catid'] . "'>
									<div class='card card-body'>";
										$formName = "updateCategoryForm";
										include("includes/partials/categories_form.php");
									echo "</div>
								</div>
			    			</th>
			    		</tr>";
					}
				?>
	  		</tbody>
		</table>

		<hr>

		<h5>Product List</h5>
		<div id="productList">
		<?php
			$c0->execute(array());
			while($c0Data = $c0->fetch()){
				echo "<button style=\"margin-right:5px\" class='btn btn-sm btn-primary' type='button' data-toggle='collapse' data-target='#productList" . $c0Data['catid'] . "' aria-expanded='false' aria-controls='productList" . $c0Data['catid'] . "'>
						" . oStringSan($c0Data['name']) . "
					</button>";
			}

			$i = 0;
			$c0Outer->execute(array());
			while($c0OuterData = $c0Outer->fetch()){
				echo "<table style='margin-top:15px' class='table table-bordered table-hover collapse";
				if ($i == 0) { echo " show";}
				echo "' data-parent='#productList' id='productList" . $c0OuterData['catid'] . "'>
			  		<thead>
					    <tr>
					    	<th scope='col' style='width: 20%; min-width: 135px'></th>
					    	<th scope='col'>Product Id</th>
					    	<th scope='col'>Category Id</th>
					    	<th scope='col'>Name</th>
					    	<th scope='col'>Price</th>
					    	<th scope='col'>Description</th>
					    	<th scope='col'>Image</th>
					    </tr>
			  		</thead>
			  		<tbody>";

			  	$p1a->execute(array($c0OuterData['catid']));
				while($productData = $p1a->fetch()){

			    		echo "<tr>
			    			<th scope='row'>
			    				<button class='btn btn-sm btn-info' type='button' data-toggle='collapse' data-target='#editProduct" . $productData['pid'] . "' aria-expanded='false' aria-controls='editProduct" . $productData['pid'] . "'>Edit</button>
			    				<button class='deleteProductButton btn btn-sm btn-danger' data-pid='" . $productData['pid'] . "' data-name='" . oStringSan($productData['name']) . "'>Delete</button>
			    			</th>
							<td>" . $productData['pid'] . "</td>
							<td>" . $productData['catid'] . "</td>
							<td>" . oStringSan($productData['name']) . "</td>
							<td>$" . $productData['price'] . "</td>
							<td>" . oStringSan($productData['description']) . "</td>
							<td><img style='height:100px' src='" . $productData['imagePath'] . "'></td>
			    		</tr>
			    		<tr>
			    			<th style='padding:0' scope='row' colspan='7'>
			    				<div class='collapse' id='editProduct" . $productData['pid'] . "'>
									<div class='card card-body'>";
										$formName = "updateProductForm";
										include("includes/partials/products_form.php");
									echo "</div>
								</div>
			    			</th>
			    		</tr>";
			    }
	  				echo "</tbody>
				</table>";
				$i++;
			}
		?>
		</div>
	</div>
		
	<footer>
		<p>Ecommerce</p>
		<p>2018 © Copyright</p>
	</footer>
	<script src="js/lib/jquery-3.3.1.min.js"></script>
	<script src="js/lib/popper.min.js"></script>
	<script src="js/lib/bootstrap.min.js"></script>
	<script src="js/adminPage_script.js"></script>
</body>
</html>