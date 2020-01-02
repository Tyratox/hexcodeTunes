<?php
require_once('core/core.php');
if(!userIsLoggedIn()){header("Location: /login.php");exit;}
require_once(UI_DIR.'index.php');
?>