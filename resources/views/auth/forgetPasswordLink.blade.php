<!DOCTYPE html>
<html lang="en">

<head>
	<title> PASSWORD RESET | {{ env("APP_NAME")  }} </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="/login_assets/images/icons/favicon.ico" />
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/vendor/animate/animate.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/vendor/select2/select2.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/vendor/daterangepicker/daterangepicker.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/login_assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="/login_assets/css/main.css">
	<!--===============================================================================================-->

	<style>
		.loader {
			border: 4px solid #f3f3f3;
			border-top: 4px solid #3498db;
			border-radius: 50%;
			width: 20px;
			height: 20px;
			animation: spin 1s linear infinite;
			display: none;
			/* Initially hide the loader */
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}
	</style>

</head>

<body style="background-color: #666666;">

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form id="loginForm" class="login100-form validate-form" method="POST" action="{{ route('reset.password.post') }}" >
					@csrf
					<span class="login100-form-title p-b-43">
						Reset Password
					</span>
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $userEmail }}">
					<div class="wrap-input100 validate-input mt-1" >
						<input class="input100" type="password" name="password">

						<span class="focus-input100"></span>
						<span class="label-input100">Password</span>
					</div>

                    @if ($errors->has('password'))
                        <div style="text-align: center; margin-top: 15px; margin-bottom: 15px;">
                            <span style="color: red;" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        </div>
                    @endif
					<div class="wrap-input100 validate-input mt-1" >
						<input class="input100" type="password" name="password_confirmation">

						<span class="focus-input100"></span>
						<span class="label-input100">Confirm Password</span>
					</div>

                    @if ($errors->has('password_confirmation'))
                        <div style="text-align: center; margin-top: 15px; margin-bottom: 15px;">
                            <span style="color: red;" role="alert">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        </div>
                    @endif
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" id="loginBtn" onclick="showLoader()">
							<span id="btnText">Reset Password</span>
							<div class="loader" id="loader"></div>
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>



	<script>
		function showLoader() {
			var loader = document.getElementById('loader');
			var btnText = document.getElementById('btnText');

			loader.style.display = 'inline-block';
			btnText.style.display = 'none';

			// Optionally, you can disable the button to prevent multiple submissions
			document.getElementById('loginBtn').disabled = true;

			// Submit the form (you may want to use AJAX for asynchronous form submission)
			document.getElementById('loginForm').submit();
		}
	</script>

	<!--===============================================================================================-->
	<script src="/login_assets/vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
	<script src="/login_assets/vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="/login_assets/vendor/bootstrap/js/popper.js"></script>
	<script src="/login_assets/vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="/login_assets/vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
	<script src="/login_assets/vendor/daterangepicker/moment.min.js"></script>
	<script src="/login_assets/vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="/login_assets/vendor/countdowntime/countdowntime.js"></script>
	<!--===============================================================================================-->
	<script src="/login_assets/js/main.js"></script>

</body>

</html>