<?php require_once("core/core.php");
if(isset($_POST['username'])){
	resetPassword(getUser($_POST['username']));
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
			<h1><?php echo sanitizeOutput(_("Reset Password"));?></h1>
			<br/>
			<form action="/reset.php" method="POST">
				<label for="username"><?php echo sanitizeOutput(_("Username or E-Mail")); ?></label>
				<br/>
				<input type="text" id="username" name="username" />
				
				<br/><br/>
				<input type="submit" value="<?php echo sanitizeOutput(_("Reset Password"));?>" />
			</form>
		</div>
	</body>
</html>