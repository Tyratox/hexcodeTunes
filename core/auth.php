<?php
/** Hashes a password **/
function hashPassword($pw){
	return password_hash($pw, PASSWORD_DEFAULT);
}
/** Verify password **/
function verifyPassword($pw, $hash){
	return password_verify($pw, $hash);
}
/** Checks if a user exists **/
function user_exists($username, $mail){
	global $db;
	$array = $db->getAllInformationFrom('users', array('username'), array($username));
	if(is_array($array)&&!empty($array)&&isset($array[0])&&is_array($array[0])&&!empty($array[0])){
		return true;
	}else{
		$array = $db->getAllInformationFrom('users', array('mail'), array($mail));
		if(is_array($array)&&!empty($array)&&isset($array[0])&&is_array($array[0])&&!empty($array[0])){
			return true;
		}else{
			return false;
		}
	}
}
/** Get user from username or email**/
function getUser($usernameOrEmail){
	global $db;
	
	if(!filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)){
		$user = $db->getAllInformationFrom('users', array('username'), array($usernameOrEmail))[0];
	}else{
		$user = $db->getAllInformationFrom('users', array('username'), array($usernameOrEmail))[0];
	}
	return $user;
}
/** Register a user **/
function registerUser($username, $password, $mail){
	if(!user_exists($username, $mail)){
		
		global $db;
		
		$password = hashPassword($password);
		$uuid = uniqid('', true);
		$expireTime = time() + 7200;/* +2h */
		$meta=$username.";".$password.";".$mail;
			
		$bool=$db->addToDatabase('mailTokens', array('tokenContent','tokenMeta','tokenMail','tokenType','tokenIP','tokenExpireTime'), array($uuid,$meta,$mail,0,$_SERVER['REMOTE_ADDR'],date('Y-m-d H:i:s',$expireTime)));
		$msg = _("Hi")." ".$username.", \r\n"._("Click"). " <a href='http://".$_SERVER["SERVER_NAME"]."/mail.php?token=".$uuid."&mail=".$mail."'>".sanitizeOutput(_("here"))."</a> ".sanitizeOutput(_("to verify your account."));
		if(sendHTMLMail($mail, "registration@".$_SERVER["SERVER_NAME"], $_SERVER["SERVER_NAME"], _("Registration"), $msg)&&$bool){
			return true;
		}else{
			echo sanitizeOutput(_("Error while sending mail! Please report this to me@tyratox.ch"));
			return false;
		}
	}else{
		return false;
	}
}
/** Reset password of a user **/
function resetPassword($user){
	global $db;
	$mail=$user['mail'];
	$uuid = uniqid('', true);
	$expireTime = time() + 7200;/* +2h */
	$bool=$this->addToDatabase('mailTokens', array('tokenContent','tokenMeta','tokenMail','tokenType','tokenIP','tokenExpireTime'), array($uuid,$user['id'],$mail,1,$_SERVER['REMOTE_ADDR'],date('Y-m-d H:i:s',$expireTime)));
	if(sendHTMLMail($mail, "resetPassword@tunes.hexcode.ch", "hexcode Tunes", _("hexcode Tunes: password reset"), sanitizeOutput(_("The reset of your password was requested! If you didn't request it, ignore this mail. If you want to reset your password click"))." <a href='https://".$_SERVER["SERVER_NAME"]."/mail.php?token=".$uuid."&mail=".$mail."'>".sanitizeOutput(_("here"))."</a>.")){
		
	}
}
/** Login a user **/
function loginUser($username, $password){
	global $db;
	if(!isset($db)){return false;}
	$users = $db->getAllInformationFrom('users', array('username'), array($username));
	foreach($users as $user){
		if(verifyPassword($password, $user['password'])){
			$uuid=generateToken($user['id']);
			if($uuid!=null){
				$_SESSION['_loginToken'] = $uuid;
				return true;
			}
		}
	}
	return false;
}
/** Checks if a user is logged in**/
function userIsLoggedIn(){
	if(!isset($_SESSION['_loginToken'])){return false;};
	global $user,$db;
	if(!isset($user)||empty($user)){
		$tokens = $db->getAllInformationFrom('tokens', array("tokenContent"), array($_SESSION['_loginToken']));
		if(count($tokens)>1){
			$db->removeFromDatabase('tokens', array("tokenContent"), array($_SESSION['_loginToken']));
			return false;
		}else if(count($tokens)==1){
			$token = $tokens[0];
			$user = $db->getAllInformationFrom('users', array('id'), array($token['tokenUser']))[0];
			if(!empty($user)){
				return true;
			}
		}
	}else{
		return true;
	}
	return false;
}
/** Sends a mail **/
function sendHTMLMail($to, $fromMail, $fromName, $subject, $message){
	$header  = 'MIME-Version: 1.0' . "\r\n";
	$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$header .= 'From: '.$fromName.' <'.$fromMail.'>' . "\r\n";
	return mail($to, $subject, $message, $header);
}
/** Generate token */
function generateToken($userID){
	global $db;
	$db->removeFromDatabase('tokens', array('tokenUser'), array($userID));
	$uuid = uniqid('', true);
	while(count($db->getAllInformationFrom('tokens', array("tokenContent"), array($uuid)))>0){
		$uuid = uniqid('', true);
	}
	$expireTime = time() + 7200;
	$eDate = date('Y-m-d H:i:s',$expireTime);
	$ip = $_SERVER['REMOTE_ADDR'];
	$db->addToDatabase('tokens',  array('tokenContent', 'tokenUser', 'tokenIP', 'tokenExpireTime'),  array($uuid, $userID, $ip, $eDate));

	return $uuid;
}
function getCurrentUser(){
	global $user,$db;
	if(!isset($user)||empty($user)){
		$tokens = $db->getAllInformationFrom('tokens', array("tokenContent"), array($_SESSION['_loginToken']));
		if(count($tokens)>1){
			$db->removeFromDatabase('tokens', array("tokenContent"), array($_SESSION['_loginToken']));
		}else if(count($tokens)==1){
			$token = $tokens[0];
			$user = $db->getAllInformationFrom('users', array('id'), array($token['tokenUser']))[0];
			return $user;
		}
	}else{
		return $user;
	}
}
?>