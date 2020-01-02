<?php require_once("core/core.php");
if(isset($_POST['username'])&&isset($_POST['password'])){
	if(loginUser($_POST['username'], $_POST['password'])){
		header("Location: /");
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>hexcode tunes</title>
		<?php echo getStyles(); ?>
		<meta name="viewport" content="user-scalable=no" />
		<link href='https://fonts.googleapis.com/css?family=Lato:100,300,400' rel='stylesheet' type='text/css'>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	</head>
	<body>
		<div id="menuBar">
			<div class="title">hexcode tunes</div>
		</div>
		<div id="loginWindow" >
			<h1><?php echo sanitizeOutput(_("Login"));?></h1>
			<br/>
			<form action="/login.php" method="POST">
				<label for="username"><?php echo sanitizeOutput(_("Username")); ?></label>
				<br/>
				<input type="text" id="username" name="username" />
				
				<label for="password"><?php echo sanitizeOutput(_("Password")); ?></label>
				<br/>
				<input type="password" id="password" name="password" />
				<a href="/reset.php"><p class="inputDesc pointer"><?php echo sanitizeOutput(_("Forgot Password?"));?></p></a>
				<br/><br/>
				<input type="submit" value="<?php echo sanitizeOutput(_("Login"));?>" />
			</form>
		</div>
	</body>
</html>