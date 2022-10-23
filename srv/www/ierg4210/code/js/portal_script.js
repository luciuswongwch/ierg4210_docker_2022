$('#userDetailsButton').click(function() {
	$('#userDetailsButton').addClass("active");
	$('#recentOrderButton').removeClass("active");
	$('#changePasswordButton').removeClass("active");
	$('#userDetails').css('display', 'block');
	$('#recentOrder').css('display', 'none');
	$('#unpaidOrder').css('display', 'none');
	$('#changePassword').css('display', 'none');
});
$('#recentOrderButton').click(function() {
	$('#userDetailsButton').removeClass("active");
	$('#recentOrderButton').addClass("active");
	$('#changePasswordButton').removeClass("active");
	$('#userDetails').css('display', 'none');
	$('#recentOrder').css('display', 'block');
	$('#unpaidOrder').css('display', 'block');
	$('#changePassword').css('display', 'none');
});
$('#changePasswordButton').click(function() {
	$('#userDetailsButton').removeClass("active");
	$('#recentOrderButton').removeClass("active");
	$('#changePasswordButton').addClass("active");
	$('#userDetails').css('display', 'none');
	$('#recentOrder').css('display', 'none');
	$('#unpaidOrder').css('display', 'none');
	$('#changePassword').css('display', 'block');
});