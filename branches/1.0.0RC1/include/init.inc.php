<?php
/**
 * init() - check a var it's set in one of the superglobalized arrays
 * 
 * 
 * @copyright 2003 Christoph Becker <cbecker@nachtwach.de>
 * original from toolbox: init.inc.php 19 2005-12-01 15:58:32Z tuergeist
 * @param string $var		name of the var
 * @param string $get_from	optional, describes the array (p=POST, g=GET, s=SESSION, c=COOKIE, e=ENV)
 * @param integer $set_to	optional, if var ist not set, set to this
 * @return true
 **/
function init($var, $get_from="pg", $set_to=0)
{
	GLOBAL $DBG;
	$ld = false;
	if(is_object($DBG)) {
		$DBG->enter_method();
		$ld = true; // local debug
		$varname = $var;
	}
	
	$get_from = strtolower($get_from);
	switch($get_from){
		default:
		case "r": 
			$var = (isset($_REQUEST["$var"])==true ?  $_REQUEST["$var"] : $set_to);
			break;
		case "pg": 
			//	look @ Posts the @ Gets or set to zero
			$var = isset($_POST[$var])==true ?  $_POST[$var] : (isset($_GET[$var])==true ? $_GET[$var] : $set_to);
			break;
		case "p": 
			$var = (isset($_POST["$var"])==true ?  $_POST["$var"] : $set_to);
			break;
		case "g": 
			$var = (isset($_GET["$var"])==true ?  $_GET["$var"] : $set_to);
			break;
		case "c": 
			$var = (isset($_COOKIE["$var"])==true ?  $_COOKIE["$var"] : $set_to);
			break;
		case "s": 
			$var = (isset($_SESSION["$var"])==true ?  $_SESSION["$var"] : $set_to);
			break;
		case "e": 
			$var = (isset($_ENV["$var"])==true ?  $_ENV["$var"] : $set_to);
			break;
		case "f": 
			$var = (isset($_FILES["$var"])==true ?  $_FILES["$var"] : $set_to);
			break;

	} // switch
	
	if ($ld) { 
		$DBG->watch_var($varname . " from " . $get_from, $var);
		$DBG->leave_method(); 
	}
	return $var;
}

?>