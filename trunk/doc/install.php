<?php
/*
 * $Id$
 *
 * UniPress  Installer
 */
/* show only runtime errors*/
error_reporting(E_ERROR | E_PARSE);

/* root? database access, privileged user */
$db['user'] = "root";
$db['pass'] = "thEO0603";

/* database stuff */
$db['server'] = "localhost";
$db['dbase'] = "mysql"; // name of database I should use or create

/* unipress stuff, unprivileged user */
$db2 = $db; 				// copy
$create		 = true;		// create this user
$db2['user'] = "unipressuser";
$db2['pass'] = "up9283";

$db2['dbase'] = "unipress"; // name of database I should use or create
$db2['create']= true; 		// create database

/***************************************************************************/
/* do not modify anything below! */
require_once("../include/cbmysql.class.php");

// MySQL Module detector
check_mysql_interface("LOAD_MYSQLI");
if(defined('LOAD_MYSQLI')) echo "using MySQL<b>I</b>"; 
else {
	echo "using <b>MySQL</b>";
	echo "<br>This is not the best way. You should upgrade to MySQL4.1 and " .
			"PHP5 with <a href=\"http://php.net/mysqli\">mysql<b>i</b></a> " .
			"as MySQL handler.";
	
}	
echo "<br><br>Starting installation:";

// root init
if (!$SQL = new MySQL($db)) {           // create object
	    die ("<span style=\"color:red;\">database connection could not be " .
	    		"etablished!<br>"
			."Please check your config in ".__FILE__." !</span>");
	}//	access data

if ($db2['create']) {
	echo "<br>Creating Database " . $db2['dbase'];
	$databasename =        $db2['dbase'];
	include "create_database.php";
	if ($SQL->error_no!=0) echo " failed!"; else echo "ok";
}

if ($create) {
	echo "<br>Creating User " . $db2['user'] . " ... ";
	$databasename 	=       $db2['dbase'];
	$username		=		$db2['user'];
	$userpass		=		$db2['pass'];
	include "create_user.php";
	if ($SQL->error_no!=0) echo " failed!"; else echo "ok";
}

if(defined('LOAD_MYSQL') && $create) echo "If there occured an custom error without " .
		"errornumber obove, ignore it. It seems to be a bug :("; 
	
$SQL->close();

echo "<br>Connecting as User ... ";
if (!$SQL = new MySQL($db2)) {           // create object
	    die ("<span style=\"color:red;\">User connection to database could " .
	    		"not be etablished!<br>"
			."Please check your config in ".__FILE__." !</span>");
	}//	access data
if (is_object($SQL)) echo "ok"; 

echo "<br>Creating Tables ... ";
// string<-file (bsc.sql)
// explode ;
// query array


?>
