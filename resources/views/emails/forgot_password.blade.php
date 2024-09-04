
<!DOCTYPE html>
<html>
<head>
	<title>Forgot Password</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f1f1f1;
			margin: 0;
			padding: 0;
		}
		.welcome-container {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}
		.welcome-message {
			background-color: white;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			text-align: center;
		}
		.welcome-message h1 {
			font-size: 36px;
			margin-top: 0;
			margin-bottom: 20px;
			color: #333;
		}
		.welcome-message p {
			font-size: 18px;
			color: #666;
			margin-bottom: 30px;
		}
	</style>
</head>
<body>
	<div class="welcome-container">
		<div class="welcome-message">
            <h1>Hello {{ $user->username }}</h1>
			<h1>Welcome to our app!</h1>
			<p>This is your code to verify your account</p>
			<p>{{ $user->verify_code }}</p>
		</div>
	</div>
</body>
</html>