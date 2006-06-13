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
error_reporting(//E_ALL);// E_ALL -> All Errorm, hint, warnings ...
E_ERROR | E_PARSE);

/* Master Connection - root? database access, privileged user
 * This USER must exist!
 */
$db['user'] = "root";
$db['pass'] = "721921";
$db['dbg'] = 0; 		// 0=silent (debug mode of mysql-class) 

/* database stuff */
$db['server'] = "localhost";
$db['dbase'] = "mysql"; // name of database I should use for testing (only read-access)

/* USER Connection - unipress stuff, unprivileged user */
$db2 = $db; 				// copy
//$override	 = true;		// try to go on, if an error occurs
$create		 = false;		// create this user (for this db)
$db2['user'] = "unipressuser";
$db2['pass'] = "up9283";

$db2['dbase'] = "unipress"; // name of database I should use or create
$db2['create']= true; 		// create database (false, if it already exists)
$prefix		  = "";			// table prefix
$db2['create_T']=false;		// create tables, true is recommented if no backup should be restored
$db2['create_AU']=false;	// create Adminuser (user. admin, pass: adminpass)

$fullpath = "/home/cb/workspace/unipress/"; // full path to installation
/***************************************************************************/
/* do not modify anything below! */
define("GOOD","<span style='color:green;font-weight:bold;'>GOOD</span>");
define("OK","<span style='color:black;background-color:yellow;'>OK</span>");
define("BAD","<span style='color:red;font-weight:bold;'>BAD!</span>");
/* -----------------------------------------------------------------*/
echo "<html><head><title>Installer V0.1alpha - Unipress</title>" .
	"<link rel=\"stylesheet\" type=\"text/css\" href=\"mycss.css\" />".
	"<body>".
		"<br><br><b>Checking for installation-dependencies:</b><pre>";
if(!(array_key_exists("installation",$_REQUEST) && $_REQUEST['installation']=="start")) {
	/* OS Test */
	$STOPP = false;
	$Cos = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? OK . " (Windows)" : GOOD. " (Linux or other)";
	echo "\nOperation System\t\t\t" . $Cos;
			
	
	/* FileSystem Test*/
	require_once("../include/check_writeable.inc.php");
	
	$Clogdir = check_writeable($fullpath, "logs",false)==true ? GOOD : BAD . " \n\t <span class='hint'>try: chmod 777 logs/</span>\n";
	$Cuploaddir = check_writeable($fullpath, "uploaded",false)==true ? GOOD : BAD. " \n\t <span class='hint'>try: chmod 777 uploaded/</span>\n";
	if($Clogdir!=GOOD || $Cuploaddir!=GOOD) { $STOPP = true; }
	echo "\n\nFilesystem" .
			"\n\tlog-dir is writeable\t\t".$Clogdir .
			"\n\tupload-dir is writeable\t\t".$Cuploaddir;
	
	/* MYSQL Test */
	require_once("../include/cbmysql.class.php");
	
	// MySQL Module detector
	check_mysql_interface("LOAD_MYSQLI");
	echo "\n\nDatabase-Connection" . 
			"\n\tPHP-Module\t\t\t";
	
	if(defined('LOAD_MYSQLI')) echo GOOD . " (MySQL<b>I</b>)"; 
	else {
		echo OK . " (MySQL)";
		echo "\n\t <span class='hint'>This is not the best way, but it works. " .
				"\n\t You should upgrade to MySQL4.1 and " .
				"PHP5 with <a href=\"http://php.net/mysqli\">mysql<b>i</b></a> " .
				"as MySQL handler.</span>\n";
	}	
	
	$SQL = new MySQL($db);
	$Csqlc = ($SQL->error_no!=0) ? BAD . "\n\t <span class='hint'>Check your config in ".__FILE__." !</span>\n " : GOOD; 
	if($Csqlc!=GOOD) {$STOPP = true;}
	
	echo "\n\tConnection (userdata by config)\t" . $Csqlc;
	
	if( $Csqlc==GOOD ) {
		$databasename =        $db2['dbase'];
		$ret = $SQL->query("Show databases");
		$found=false;
		if ($db2['create']==true) {
			$Cdbc = OK . " \n\t <span class='hint'>Database will be created.</span>\n";
		} else {
			$Cdbc = BAD . " \n\t <span class='hint'>Check config at ".__FILE__."!</span>\n";
			$STOPP = true;
		}
		while (list(,$a)=each($ret))
			if($a[0]==$databasename) $Cdbc=GOOD;
				
	} else {
		$Cdbc = BAD . " <span class='hint'>Check skipped</span>";
	}
	
	echo "\n\tDatabase status\t\t\t" . $Cdbc;
	
	$SQL = new MySQL($db2);
	$Csql2c = ($SQL->error_no!=0 && $create != true) ? BAD . "\n\t <span class='hint'>2nd Users doesn't exists and it shouldn't created.</span>\n" : GOOD;
	$Csql2c = ($SQL->error_no==0 && $create == true) ? BAD . "\n\t <span class='hint'>2nd Users exists and it should created.</span>\n" : GOOD;
	if($Csql2c!=GOOD) {$STOPP=true;}
	echo "\n\tDatabase-User status\t\t" . $Csql2c;
	
	/* Installation Options */
	if ($db2['create']==true && $Cdbc==GOOD ) {
		 $Copt_cd =  BAD . "\n\t <span class='hint'>There is no need to create the database, " .
		 		"it already exists. \n\t For a new installation, drop old database." .
		 		"\n\t Go to ".__FILE__." \n\t and set db2[create] to false or DROP database</span>";
		 $STOPP = true;
	} else {
		$Copt_cd = GOOD;
	}  
	
	echo "\n\nInstallation Options" .
			"\n\tCreate Database\t\t\t" . $Copt_cd;
	
	echo "\n\n<b>Summary</b>\t\t\t\t\t";
	if ($STOPP==true) {
		echo BAD ."\n\t <span class'hint'>There ware STOP-Errors, try to solve them. Then <a href'".$PHP_SELF."'>reload</a>";
		die("</span></pre></body></html>"); 
	} else {
		echo GOOD . "\n\t Go on with installation. <a href='?installation=start'>Click here</a>";
		die("</pre></body></html>"); 	
	}
	
	// died end.
}

/* -----------------------------------------------------------------*/

echo "<br><br><b>Starting installation:</b>";
require_once("../include/cbmysql.class.php");
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
	$SQL->insert("INSERT INTO `".$prefix."press_user` ( `id` , `name` , `pass` , `counter` , `session` , `auth` ) VALUES ('', 'admin', '".sha1("adminpass")."', '0', '',  '0');");
	$SQL->insert("INSERT INTO `".$prefix."press_admins` ( `id`  ) VALUES ('1');");
	echo " done.";
}
echo "<br><b>Installation done so far.</b><p>" .
		"Go to <a href='../'>homepage</a> to start.";

/* _________________________ functions ______________________*/
echo "</body></html>";

?>