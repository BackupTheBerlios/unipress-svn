<?php
/**
 * check  a var if it's set, if not, set it to default
 *
 * 
 * @copyright 2003 Christoph Becker <cbecker@nachtwach.de>
 * changes:
 *  2005-09-08 to 1.1 cb
 * 	 + debug support if $DBG is an instance of cbDebug/Debug
 *   + "r" for _REQUEST as new default
 *   + "f" for _FILES
 **/
 
/**
 * init() - check a var it's set in one of the superglobalized arrays
 * 
 * @version 1.1
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