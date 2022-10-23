function displayLoginFormErrorMessage(errorMessage) {
	$('#loginFormErrorMessage').html($('#loginFormErrorMessage').html() + "<div class='alert alert-warning' role='alert'>" + errorMessage + "</div>");
}

function displaySignUpFormErrorMessage(errorMessage) {
	$('#signUpFormErrorMessage').html($('#signUpFormErrorMessage').html() + "<div class='alert alert-warning' role='alert'>" + errorMessage + "</div>");
}

$(document).ready(function() {

	$('#signUpForm').hide();
	$('#signUpFormErrorMessage').hide();

	$('#goToSignUp').click(function() {
		$('#authFormTitle').text('Sign Up Form');
		$('#loginForm').hide();
		$('#loginFormErrorMessage').hide();
		$('#signUpForm').show();
		$('#signUpFormErrorMessage').show();
	});

	$('#goToLogin').click(function() {
		$('#authFormTitle').text('Login Form');
		$('#loginForm').show();
		$('#loginFormErrorMessage').show();
		$('#signUpForm').hide();
		$('#signUpFormErrorMessage').hide();
	});

	$('#loginButton').click(function() {
		$('#loginFormErrorMessage').html("");
		var loginEmail = $('#loginEmail').val();
		var loginPassword = $('#loginPassword').val();
		var loginNonce = document.querySelector('#loginNonce').value;
		
		if(loginEmail != '' && loginPassword != '') {
			$.ajax({
				url: "./includes/handlers/auth-handlers.php",
				method: "POST",
				data: {userLoginForm: true, loginEmail: loginEmail, loginPassword: loginPassword, loginNonce: loginNonce},
				success: function(data) {
					try {
						var jsonData = JSON.parse(data);
						if (jsonData.length != 1) {
							for (var i = 0; i < jsonData.length - 1; i++) {
								displayLoginFormErrorMessage(jsonData[i]);
							}
						} else {
							if(jsonData[0] == "1") {
								window.location.replace("admin.php");
							} else {
								window.location.replace("index.php");
							}
						}
					} catch (e) {
						displayLoginFormErrorMessage("There was a problem connecting to the server. Please try again later.");
					}
				}
			});
		} else {
			displayLoginFormErrorMessage("All fields are required");
		}
	});
	
	$('#signUpButton').click(function() {
		$('#signUpFormErrorMessage').html("");
		var signUpEmail = $('#signUpEmail').val();
		var signUpPassword = $('#signUpPassword').val();
		var signUpConfirmPassword = $('#signUpConfirmPassword').val();
		var signUpAdminCode = $('#signUpAdminCode').val();
		var signUpNonce = document.querySelector('#signUpNonce').value;

		if(signUpEmail != '' && signUpPassword != '' && signUpConfirmPassword != '') {
			$clientSideCheck = true;
			if (signUpPassword != signUpConfirmPassword) {
				displaySignUpFormErrorMessage("Your passwords don't match");
				$clientSideCheck = false;
			}
			if (/[^A-Za-z0-9]/.test(signUpPassword)) {
				displaySignUpFormErrorMessage("Your password can only contain numbers and letters");
				$clientSideCheck = false;
			}
			if (signUpPassword.length > 30 || signUpPassword.length < 5) {
				displaySignUpFormErrorMessage("Your password must be between 5 and 30 characters");
				$clientSideCheck = false;
			}
			if ($clientSideCheck) {
				$.ajax({
					url: "./includes/handlers/auth-handlers.php",
					method: "POST",
					data: {userSignupForm: true, signUpEmail: signUpEmail, signUpPassword: signUpPassword, signUpConfirmPassword: signUpConfirmPassword, signUpAdminCode: signUpAdminCode, signUpNonce: signUpNonce },
					success: function(data) {
						try {
							var jsonData = JSON.parse(data);       						
							if (jsonData.length != 1) {
								for (var i = 0; i < jsonData.length - 1; i++) {
									displaySignUpFormErrorMessage(jsonData[i]);
								}
							} else {
								if(jsonData[0] == "1") {
									window.location.replace("admin.php");
								} else {
									window.location.replace("index.php");
								}
							}								
						} catch (e) {
							displaySignUpFormErrorMessage("There was a problem connecting to the server. Please try again later.");
						}
					}
				});
			}
		} else {
			displaySignUpFormErrorMessage("All fields except admin code are required");
		}
	});
})

// Google Sign-In
function handleCredentialResponse(response) {
	// Useful data for your client-side scripts:
	const responsePayload = jwt_decode(response.credential);
	var googleSignInEmail = responsePayload.email;

	$.ajax({
		url: "./includes/handlers/auth-handlers.php",
		method: "POST",
		data: {googleSignIn: true, googleSignInEmail: googleSignInEmail},
		success: function(data) {
			try {
				var jsonData = JSON.parse(data);       						
				if(jsonData[0] == "1") {
					window.location.replace("admin.php");
				} else {
					window.location.replace("index.php");
				}								
			} catch (e) {
				displayLoginFormErrorMessage("There was a problem connecting to the server. Please try again later.");
			}
		}
	})
}

window.fbAsyncInit = function() {
    FB.init({
		appId      : '816197963150617',
		cookie     : true,  // enable cookies to allow the server to access 
		                  	// the session
		xfbml      : true,  // parse social plugins on this page
		version    : 'v2.8' // use graph api version 2.8
    });

    FB.Event.subscribe('auth.authResponseChange', function(response) {
     	if (response.status === 'connected') {
     		console.log(response);
	        FB.api('/me', { locale: 'zh_HK', fields: 'name, email' },
	          	function(response) {
		            $.ajax({
						url: "./includes/handlers/auth-handlers.php",
						method: "POST",
						data: {facebookSignIn: true, facebookSignInEmail: response.email},
						success: function(data) {
							try {
								var jsonData = JSON.parse(data);       						
								if(jsonData[0] == "1") {
									window.location.replace("admin.php");
								} else {
									window.location.replace("index.php");
								}								
							} catch (e) {
								displayLoginFormErrorMessage("There was a problem connecting to the server. Please try again later.");
							}
						}
					})
	        	}
        	);
    	} else if (response.status === 'not_authorized') {
        	displayLoginFormErrorMessage("You did not grant us the necessary privilege. Please logout and try again later.");
    	} else {
        	displayLoginFormErrorMessage("There was a problem connecting to the facebook server. Please try again later.");
    	}
    }); 
};


var shoppingListPID;
var shoppingListQuantity;

if (localStorage.getItem("pidList") === null) {
	shoppingListPID = new Array();
	shoppingListQuantity = new Array();
} else {
	shoppingListPID = JSON.parse(localStorage.getItem("pidList"));
	shoppingListQuantity = JSON.parse(localStorage.getItem("quantityList"));
}

function storeToLocalStorage() {
	localStorage.setItem("pidList", JSON.stringify(shoppingListPID));
	localStorage.setItem("quantityList", JSON.stringify(shoppingListQuantity));
}

async function loadShoppingList() {
	document.querySelector("#checkoutButton").disabled = true;
	document.querySelector("#AJAXShoppingList").innerHTML = "";
	for (var i = 0; i < shoppingListPID.length; i++) {
		if (i == 0) {
			document.querySelector("#checkoutButton").disabled = false;
		}
		var tempPID = shoppingListPID[i];
		await $.post("includes/handlers/get-productData.php",
    		{
		        pid: tempPID
    		},
		    function(data){
		        document.querySelector("#AJAXShoppingList").innerHTML += `<tr>
								<td scope="row" style="cursor:pointer; color:darkgrey; vertical-align: middle;" onclick="deleteItem('` + tempPID + `');">x</td>
								<td style="vertical-align: middle;">` + JSON.parse(data)[0] + `</td>
								<td><input type="number" min="1" max="99" onchange="updateQuantity(` + tempPID + `, this.value);" id="inputBox-` + tempPID + `" class="inputBox" value="` + shoppingListQuantity[tempPID] + `"></td>
								<td style="vertical-align: middle;">$` + JSON.parse(data)[1] + `</td>
							</tr>`;
		});
	}
}

async function updateTotal() {
	var subTotal = 0.00;
	for (var i = 0; i < shoppingListPID.length; i++) {
		var pid = shoppingListPID[i];
		var data = await $.post("includes/handlers/get-productData.php",
    		{ pid: shoppingListPID[i] },
		    function(data){
		    	return data;
		    });
		var price = await JSON.parse(data)[1];
		subTotal += (price * shoppingListQuantity[shoppingListPID[i]]);
	}
	document.querySelector("#AJAXShoppingListTotal").innerText = (subTotal == 0) ? 0 : subTotal.toFixed(2);
	document.querySelector("#subtotal").innerText = "$" + ((subTotal == 0) ? 0 : subTotal.toFixed(2));
}

loadShoppingList();
updateTotal();

function addItemToShoppingList(pid) {
	var productId = pid;
	$.post("includes/handlers/get-productData.php",
    	{
	        pid: pid
		},
    	function(data){
        	document.querySelector("#AJAXShoppingList").innerHTML += `<tr>
								<td scope="row" style="cursor:pointer; color:darkgrey; vertical-align: middle;" onclick="deleteItem('` + productId + `');">x</td>
								<td style="vertical-align: middle;">` + JSON.parse(data)[0] + `</td>
								<td><input type="number" min="1" max="99" onchange="updateQuantity(` + productId + `, this.value);" id="inputBox-` + productId + `" class="inputBox" value="` + shoppingListQuantity[productId] + `"></td>
								<td style="vertical-align: middle;">$` + JSON.parse(data)[1] + `</td>
							</tr>`;
    	});
	document.querySelector("#checkoutButton").disabled = false;
}

async function deleteItem(pid) {
	var index = shoppingListPID.indexOf(pid);
	if (index > -1) {
  		await shoppingListPID.splice(index, 1);
  		shoppingListQuantity[pid] = null;
	}
	await storeToLocalStorage();
	await loadShoppingList();
	await updateTotal();
}

async function updateQuantity(pid, value) {
	if(value.trim().length === 0 || value.trim() < 1){
        value = 1;
    } else if (value.trim() > 99) {
    	value = 99;
    }
    document.querySelector("#inputBox-" + pid).setAttribute("value", parseInt(value));
    shoppingListQuantity[pid] = parseInt(value);
	await storeToLocalStorage();
	await updateTotal();
}

function addToCartButtonClicked(pid) {
	if(shoppingListPID.indexOf(pid) === -1) {
		shoppingListPID.push(pid);
		shoppingListQuantity[pid] = 1;
		storeToLocalStorage();
		addItemToShoppingList(pid);
		updateTotal();
	} else {
		updateQuantity(pid, (shoppingListQuantity[pid] + 1).toString());
	}
}

function removeLocalStorage() {
	localStorage.removeItem('pidList');
	localStorage.removeItem('quantityList');
	shoppingListPID = new Array();
	shoppingListQuantity = new Array();
}

function ui_cart_submit(event, form) {
	event.preventDefault();
	var pidArray = [];
	var quantityArray = [];
	pidArray = JSON.parse(localStorage.getItem("pidList"));
	quantityArray = JSON.parse(localStorage.getItem("quantityList"));
	$.post("includes/handlers/checkout-process.php",
    	{
	        pidArray: pidArray,
	        quantityArray: quantityArray,
	        checkoutNonce: form.checkoutNonce.value
		},
    	function(data){
			console.log(data);
    		data = JSON.parse(data);
    		document.querySelector("#checkoutFormErrorMessage").innerHTML = "";
    		if (typeof data["error"] !== 'undefined' && data["error"].length > 0) {
    			for(var i = 0; i < data["error"].length; i++) {
    				document.querySelector("#checkoutFormErrorMessage").innerHTML += "<div class='alert alert-warning' role='alert'>" + data["error"][i] + "</div>";
    			}
			} else {
				form.business.value = data["merchantEmail"];
	    		form.currency_code.value = data["currency"];
	    		form.invoice.value = data["invoice"];
	        	form.custom.value = data["digest"];

	        	for (var i = 0; i < data["productPid"].length; i++) {
	        		var itemNumber = document.createElement("input");
	        		itemNumber.type = "hidden";
	        		itemNumber.name = "item_number_" + (i + 1);
	        		itemNumber.value = data["productPid"][i];
	        		var itemName = document.createElement("input");
	        		itemName.type = "hidden";
	        		itemName.name = "item_name_" + (i + 1);
	        		itemName.value = data["productName"][i];
	        		var itemQuantity = document.createElement("input");
	        		itemQuantity.type = "hidden";
	        		itemQuantity.name = "quantity_" + (i + 1);
	        		itemQuantity.value = data["productQuantity"][data["productPid"][i]];
	        		var itemAmount = document.createElement("input");
	        		itemAmount.type = "hidden";
	        		itemAmount.name = "amount_" + (i + 1);
	        		itemAmount.value = data["productPrice"][i];
	        		form.appendChild(itemNumber);
	        		form.appendChild(itemName);
	        		form.appendChild(itemQuantity);
	        		form.appendChild(itemAmount);
	        	}

	        	form.submit();

	        	removeLocalStorage();
	        	loadShoppingList();
	        	updateTotal();
			}
    });
}