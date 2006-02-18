<?php
/*
 * $Id$
 *
 * UniPress  Installer
 * - create database
 * - create user
 * - create tables
 */
/* show only runtime errors*/
error_reporting(//E_ALL);//
E_ERROR | E_PARSE);

/* Master Connection - root? database access, privileged user */
$db['user'] = "root";
$db['pass'] = "721921";
$db['dbg'] = 0; // silent 

/* database stuff */
$db['server'] = "localhost";
$db['dbase'] = "mysql"; // name of database I should use for testing (only read-access)

/* USER Connection - unipress stuff, unprivileged user */
$db2 = $db; 				// copy
$create		 = false;		// create this user
$db2['user'] = "unipressuser";
$db2['pass'] = "up9283";

$db2['dbase'] = "unipress"; // name of database I should use or create
$db2['create']= true; 		// create database
$prefix		  = "";			// table prefix
$db2['create_T']=true;		// create tables
$db2['create_AU']=true;		// create Adminuser (user. admin, pass: adminpass)

$fullpath = "/home/cb/workspace/unipress/"; // full path to installation
/***************************************************************************/
/* do not modify anything below! */
/* FileSystem */
check_writeable($fullpath, "logs");
check_writeable($fullpath, "uploaded");

/* MYSQL */
require_once("../include/cbmysql.class.php");

// MySQL Module detector



check_mysql_interface("LOAD_MYSQLI");
if(defined('LOAD_MYSQLI')) echo "using MySQL<b>I</b>"; 
else {
	echo "using <b>MySQL</b>";
	echo "<br>&nbsp;Hint: This is not the best way, but it works. You should upgrade to MySQL4.1 and " .
			"PHP5 with <a href=\"http://php.net/mysqli\">mysql<b>i</b></a> " .
			"as MySQL handler.";
	
}	
echo "<br><br>Starting installation:";

// root init!
$SQL = new MySQL($db);
if ($SQL->error_no!=0) {           // create object
	    die ("<span style=\"color:red;\">database connection could not be " .
	    		"etablished!<br>"
			."Please check your config in ".__FILE__." !</span>");
	}//	access data

if ($db2['create']) {
	echo "<br>Creating Database " . $db2['dbase'] . " ... ";
	$databasename =        $db2['dbase'];
	include "create_database.php";
	if ($SQL->error_no!=0) die(" failed! -&gt; ".$SQL->error_msg); else echo "ok";
} else {
	echo "<br>Testing Database " . $db2['dbase']. " ... ";
	$databasename =        $db2['dbase'];
	$ret = $SQL->query("Show databases");
	
	$found=false;
	while (list(,$a)=each($ret))
		if($a[0]==$databasename) $found=true;
	if ($found==false)
		die(" database could not be found. Check your config!");	
}

if ($create) {
	echo "<br>Creating User " . $db2['user'] . " ... ";
	$databasename 	=       $db2['dbase'];
	$username		=		$db2['user'];
	$userpass		=		$db2['pass'];
	include "create_user.php";
	if ($SQL->error_no!=0) die(" failed! -&gt; ".$SQL->error_msg); else echo "ok";
}

if(defined('LOAD_MYSQL') && $create) echo "<br>If there occured an custom error without " .
		"errornumber obove, ignore it. It seems to be a bug :("; 
	
$SQL->close();

echo "<br>Connecting as User ... ";
$SQL = new MySQL($db2);
if ($SQL->error_no!=0) 
    die ("<span style=\"color:red;\">User connection to database could " .
	    		"not be etablished!<br>"
			."Please check your config in ".__FILE__." !</span>");
else echo "ok";

if($db2['create_T']){
	echo "<br>Creating Tables ... ";
		
		include "database.php";
		while(list(,$q)=each($table)) {
		#	echo "<br>".$q;
			if(!$SQL->query($q)) die( "<br>Error while doing: " . $q . " -&gt; ".$SQL->error_msg);
		}
	echo "ok";
}
// Create Admin User
if($db2['create_AU']) {
	echo "<br>Creating Admin User with (admin and adminpass) ... "; 
	$SQL->insert("INSERT INTO `".$prefix."press_user` ( `id` , `name` , `pass` , `counter` , `session` , `admin` , `ldap` ) VALUES ('', 'admin', 'adminpass', '0', '', '1', '0');");
	echo " done.";
}
echo "<br><b>Installation done so far.</b>";

/* _________________________ functions ______________________*/
function check_writeable($path, $dir) {
	if (!is_dir($path . $dir)) { 
		die ($dir . " does not exists or is not " .
			"readable in '".$path."'");
	}
	if (!is_writable($path . $dir)) { 
		die ($dir . " is not writeable in " .
			"'".$path."'<br>You should run 'chmod 777 ".$dir."'");
	}
}

?>