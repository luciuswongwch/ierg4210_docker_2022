function resize() {
	if($(window).width() <= 480 - 17) {
		var buttonList = document.getElementsByClassName("btn");
		for (var i = 0; i < buttonList.length; i++) {
			buttonList[i].classList.add("btn-sm");
		}

		$("#loginForm > .row > div:nth-child(1)").removeClass("col-4");
		$("#loginForm > .row > div:nth-child(1)").addClass("col-12");

		$("#loginForm > .row > div:nth-child(2)").removeClass("col-8");
		$("#loginForm > .row > div:nth-child(2)").addClass("col-12");
		$("#loginForm > .row > div:nth-child(2)").addClass("add15pxMarginTop");
		
	} else {
		var buttonList =  document.getElementsByClassName("btn");
		for (var i = 0; i < buttonList.length; i++) {
			buttonList[i].classList.remove("btn-sm");
		}

		$("#loginForm > .row > div:nth-child(1)").removeClass("col-12");
		$("#loginForm > .row > div:nth-child(1)").addClass("col-4");
		
		$("#loginForm > .row > div:nth-child(2)").removeClass("col-12");
		$("#loginForm > .row > div:nth-child(2)").addClass("col-8");
		$("#loginForm > .row > div:nth-child(2)").removeClass("add15pxMarginTop");
		
	}

	if($(window).width() <= 768 - 17) {
		if ($("#collapse_listOfCategory").length){
  			$("#collapse_listOfCategory").removeClass("show");
		}
	} else {
		if ($("#collapse_listOfCategory").length){
  			$("#collapse_listOfCategory").addClass("show");
		}
	}
}

function openPage(url) {
	$("#mainContent").load(url);
	$("body").scrollTop(0);
	history.pushState(null, null, url);
	setTimeout(function () {
  		resize();
	}, 50);
}

window.addEventListener('resize', function(event){
	resize();
});

resize();