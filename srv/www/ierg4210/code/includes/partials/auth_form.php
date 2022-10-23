<?php 

if(!$isLoggedIn) { echo "<div class='modal fade' id='modalCenter' tabindex='-1' role='dialog'>
	<div class='modal-dialog modal-dialog-centered' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<h5 class='modal-title' id='authFormTitle'>Login Form</h5>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
			</div>
			<div class='modal-body'>
				<div id='loginFormErrorMessage'></div>
				<div id='loginForm'>
					<div class='form-group'>
						<div id='g_id_onload'
							data-client_id='252769525002-a01uch46uvqqf82hfobe4169df2r0jh4.apps.googleusercontent.com'
							data-callback='handleCredentialResponse'>
						</div>
						<div class='g_id_signin' data-type='standard' data-theme='filled_blue'></div>
					</div>
					<div class='form-group'>
						<div class='fb-login-button' size='medium' data-max-rows='1' data-size='large' data-button-type='continue_with' data-show-faces='false' data-auto-logout-link='false' data-use-continue-as='false' data-scope='email'></div>
					</div>
					<hr>
					<div class='form-group'>
						<label for='loginEmail'>Email address</label>
						<input type='email' class='form-control' name='loginEmail' id='loginEmail' placeholder='Enter email' required>
					</div>
					<div class='form-group'>
						<label for='loginPassword'>Password</label>
						<input type='password' class='form-control' name='loginPassword' id='loginPassword' placeholder='Password' required>
					</div>
					<input type='hidden' name='loginNonce' id='loginNonce' value='" . csrf_getNonce('login') . "'>
					<button type='button' name='loginButton' id='loginButton' class='btn btn-primary'>Login</button>
					<hr>
					<a href='#signup' id='goToSignUp'>Don't have an account yet? Signup here.</a>
				</div>
				<div id='signUpFormErrorMessage'></div>
				<div id='signUpForm'>
					<div class='form-group'>
						<label for='signUpEmail'>Email address</label>
						<input type='email' class='form-control' name='signUpEmail' id='signUpEmail' placeholder='Enter email' required>
					</div>
					<div class='form-group'>
						<label for='signUpPassword'>Password</label>
						<input type='password' class='form-control' name='signUpPassword' id='signUpPassword' placeholder='Password' required>
					</div>
					<div class='form-group'>
						<label for='signUpConfirmPassword'>Confirm Password</label>
						<input type='password' class='form-control' name='signUpConfirmPassword' id='signUpConfirmPassword' placeholder='Enter your password again' required>
					</div>
					<div class='form-group'>
						<label for='signUpAdminCode'>Admin Code (Optional)</label>
						<input type='text' class='form-control' name='signUpAdminCode' id='signUpAdminCode' placeholder='Enter admin code to sign up as admin user'>
					</div>
					<input type='hidden' name='signUpNonce' id='signUpNonce' value='" . csrf_getNonce('signUp') . "'>
					<button type='button' name='signUpButton' id='signUpButton' class='btn btn-primary'>Sign Up</button>
					<hr>
					<a href='#login' id='goToLogin'>Already have an account? Log in here.</a>
				</div>
			</div>
		</div>
	</div>
</div>";
}
?>
