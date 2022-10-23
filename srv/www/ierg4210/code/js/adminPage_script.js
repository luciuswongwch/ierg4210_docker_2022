$(".deleteCategoryButton").click(function() {
	$categoryId = $(this).data("catid");
	$categoryName = $(this).data("name");
	deleteCategory($categoryId, $categoryName);
});

$(".deleteProductButton").click(function() {
	$productId = $(this).data("pid");
	$productName = $(this).data("name");
	deleteProduct($productId, $productName);
});

function deleteCategory(catid, catname) {
	var prompt = confirm("Are you sure you want to delete category \"" + catname + "\"?");

	if(prompt == true) {
		$.post("includes/handlers/delete-category.php", { catid: catid })
		.done(function(error) {
			if(error != "") {
				alert(error);
				return;
			}
			location.reload();
		});
	}
}

function deleteProduct(pid, pname) {
	var prompt = confirm("Are you sure you want to delete product \"" + pname + "\"?");

	if(prompt == true) {
		$.post("includes/handlers/delete-product.php", { pid: pid })
		.done(function(error) {
			if(error != "") {
				alert(error);
				return;
			}
			location.reload();
		});
	}
}

function setTwoNumberDecimal(event) {
	console.log("setTwoNumberDecimal is fired");
    this.value = parseFloat(this.value).toFixed(2);
}

$(".dropArea").on('dragenter', function(e) {
	e.preventDefault();
	e.stopPropagation();
	$(this).addClass('highlight');
});

$(".dropArea").on('dragover', function(e) {
	e.preventDefault();
	e.stopPropagation();
	$(this).addClass('highlight');
});

$(".dropArea").on('dragleave', function(e) {
	e.preventDefault();
	e.stopPropagation();
	$(this).removeClass('highlight');
});

$(".dropArea").on('drop', function(e) {
	e.preventDefault();
	e.stopPropagation();
	$(this).removeClass('highlight');
});

var dropAreaList = document.getElementsByClassName("dropArea");
var productImageFileList = document.getElementsByClassName("productImageFile");
var imagePreviewList = document.getElementsByClassName("imagePreview");

for (let i = 0; i < dropAreaList.length; i++) {
	(function () {
		dropAreaList[i].addEventListener("drop", function(event) {
			var fileType = event.dataTransfer.files[event.dataTransfer.files.length - 1].type;
			if (fileType == "image/jpeg" || fileType == "image/png" || fileType == "image/gif") {
				productImageFileList[i].files = event.dataTransfer.files;
				handleFiles(event.dataTransfer.files, i);
			}
		});
	}());
}

function handleFiles(files, i) {
	previewFile(files[files.length - 1], i);
}

function previewFile(file, i) {
	var reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onloadend = function() {
		imagePreviewList[i].innerHTML = "";
		var img = document.createElement('img');
		img.src = reader.result;
		imagePreviewList[i].appendChild(img);
	}
}