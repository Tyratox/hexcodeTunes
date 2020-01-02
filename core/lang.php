<?php
function getLang(){
 	$lang = getLanguage(); 
	$locale = "";
	switch ($lang) {
		case "de":
		case "ch":
			$locale = "de_DE";
			break;
		case "en":
			$locale = "en_US";
			break;
		default:
			$locale = "en_US";
			break;
	}
	return $locale.".utf8";
}
function getLanguage(){
	$langs = " ".$_SERVER['HTTP_ACCEPT_LANGUAGE'];
	$languages = array(
		'en',
		'de',
	);
    foreach($languages as $code) {
        $pos = strpos($langs, $code);
        if(intval($pos) != 0) {
            $position[$code] = intval($pos);
        }
    }
    $lang = 'en';
    if(!empty($position)) {
        foreach($languages as $code) {
            if(isset($position[$code]) &&
               $position[$code] == min($position)) {
                    $lang = $code;
            }
        }
    }
    return $lang;
}
function setLang($locale, $domain, $encoding){ 
	putenv("LC_ALL=".$locale);
	setlocale(LC_ALL, $locale);
	bindtextdomain($domain, CORE_DIR."lang/"); 
	bind_textdomain_codeset($domain, $encoding);
	textdomain($domain);
	
	return true;
}
// setLang(getLang(), "cms", "UTF-8");
?>