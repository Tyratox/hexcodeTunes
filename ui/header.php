<?php if(!userIsLoggedIn()){header("Location: /login.php");exit;} ?>
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
		<div id="page" >