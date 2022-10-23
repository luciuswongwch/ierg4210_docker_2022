<?php

$pidArray = json_decode($orderData["productList"], true)["pidArray"];
$quantityArray = json_decode($orderData["productList"], true)["quantityArray"];
$productPriceArray = json_decode($orderData["productList"], true)["productPriceArray"];

$productNameArray = array();
for ($i = 0; $i < sizeOf($pidArray); $i++) {
	$p1->execute(array($pidArray[$i]));
	if($shoppingListData = $p1->fetch()) {
		array_push($productNameArray, $shoppingListData['name']);
	} else {
		array_push($productNameArray, "This item was deleted");
	}
}
													
for($i = 0; $i < sizeOf($pidArray); $i++) {
	echo "<tr>";
	if ($i == 0) {
		echo "<td scope='row' rowspan='" . sizeOf($pidArray) . "'>" . oStringSan($orderData["oid"]) . "</td>";
		if ($orderDisplay == "adminPage") { echo "<td rowspan='" . sizeOf($pidArray) . "'>" . oStringSan($orderData["user"]) . "</td>"; }
	}
	echo "<td scope='row'>" . oStringSan($productNameArray[$i]) . "</td>";
	echo "<td>$" . oStringSan($productPriceArray[$i]) . "</td>";
	echo "<td>" . oStringSan($quantityArray[(int) $pidArray[$i]]) . "</td>"; ?>

	<td <?php if ($i == sizeOf($pidArray) - 1) { echo "class='orderLastItemTotal'"; } ?>><?php echo "$" . $productPriceArray[$i] * $quantityArray[$pidArray[$i]]; ?></td></tr>
<?php								
}

$totalPrice = 0;
for ($i = 0; $i < sizeOf($pidArray); $i++) {
	$totalPrice += $quantityArray[$pidArray[$i]] * $productPriceArray[$i];
}

?>

<tr><td scope='row' colspan=<?php if ($orderDisplay == "adminPage") { echo '5'; } else { echo '4'; } ?> class='orderSubTotalText'><?php echo "Subtotal of Order (#" . oStringSan($orderData["oid"]) . ")"; ?></td><td class='orderSubTotalAmount'><?php echo "$" . oStringSan($totalPrice) ?></td></tr>