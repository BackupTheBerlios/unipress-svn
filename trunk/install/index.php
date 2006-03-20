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

require_once ("../include/init.inc.php");
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
$drop_olduser= init("drop_user","r",true);			// drop old user, if exist (for this db)
$create		 = init("create_user","r",true);		// create this user (for this db)
$db2['user'] = init("user","r","unipressuserd");
$db2['pass'] = init("pass","r","up9283");

$db2['dbase'] = init("dbase","r","unipress"); 		// name of database I should use or create
$db2['create']= init("create_db","r",true); 		// create database (false, if it already exists)
$prefix		  = "";			// table prefix
$db2['create_T']=init("create_t","r",true);		// create tables, true is recommented if no backup should be restored
$db2['create_AU']=init("create_a","r",true);		// create Adminuser (user. admin, pass: adminpass)

$fullpath = "/home/cb/workspace/unipress/"; // full path to installation
/***************************************************************************/
/* do not modify anything below! */
define("GOOD","<span style='color:green;font-weight:bold;'>Gut</span>");
define("OK","<span style='color:black;background-color:yellow;'>OK</span>");
define("BAD","<span style='color:red;font-weight:bold;'>Schlecht!</span>");
/* ------------------------------------------------------------------------*/
echo "<html><head><title>Installer V0.1alpha - Unipress</title>" .
	"<link rel=\"stylesheet\" type=\"text/css\" href=\"mycss.css\" />".
	"<body>";
		
if(!(array_key_exists("installation",$_REQUEST) && $_REQUEST['installation']=="start")) {
	echo "<br><br><b>Prüfe Abhängigkeiten für die Installation:</b><pre>";
	/* OS Test */
	$STOPP = false;
	$Cos = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? OK . " (".PHP_OS.")" : GOOD. " (".PHP_OS.")";
	echo "\nBetriebssystem\t\t\t\t\t" . $Cos;
			
	
	/* FileSystem Test*/
	require_once("../include/check_writeable.inc.php");
	
	$Clogdir = check_writeable($fullpath, "logs",false)==true ? GOOD : BAD . " \n\t <span class='hint'>Ändern mit: chmod 777 logs/</span>\n";
	$Cuploaddir = check_writeable($fullpath, "uploaded",false)==true ? GOOD : BAD. " \n\t <span class='hint'>Ändern mit: chmod 777 uploaded/</span>\n";
	$Csettings = check_writeable($fullpath, "configs/main.conf.ini",false)==true ? GOOD : BAD. " \n\t <span class='hint'>Ändern mit: chmod 777 configs/main.conf.ini/</span>\n";
	if($Clogdir!=GOOD || $Cuploaddir!=GOOD ||  $Csettings!=GOOD) { $STOPP = true; }
	echo "\n\nDateisystem" .
			"\n\tlog-Verzeichnis ist beschreibbar\t".$Clogdir .
			"\n\tupload-Verzeichnis ist beschreibbar\t".$Cuploaddir;
			"\n\\Einstellungsdatei ist beschreibbar\t".$Csettings;
	
	/* MYSQL Test */
	require_once("../include/cbmysql.class.php");
	
	// MySQL Module detector
	check_mysql_interface("LOAD_MYSQLI");
	echo "\n\nDatenbankverbindung" . 
			"\n\tPHP-Modul\t\t\t\t";
	
	if(defined('LOAD_MYSQLI')) echo GOOD . " (MySQL<b>I</b>)"; 
	else {
		echo OK . " (MySQL)";
		echo "\n\t <span class='hint'>Dies ist nicht die beste Variante, aber funktioniert. " .
				"\n\t Besser, man benutzt MySQL4.1 und " .
				"PHP5 mit <a href=\"http://php.net/mysqli\">mysql<b>i</b></a> " .
				"als MySQL-Handler.</span>\n";
	}	
	
	$SQL = new MySQL($db);
	$Csqlc = ($SQL->error_no!=0) ? BAD . "\n\t <span class='hint'>Prüfe die Konfiguration in ".__FILE__." !</span>\n " : GOOD; 
	if($Csqlc!=GOOD) {$STOPP = true;}
	
	echo "\n\tVerbindung (Konfigurationsdaten)\t" . $Csqlc;
	
	if( $Csqlc==GOOD ) {
		$databasename =        $db2['dbase'];
		$ret = $SQL->query("Show databases");
		$found=false;
		if ($db2['create']==true) {
			$Cdbc = OK . " \n\t <span class='hint'>Datenbank wird angelegt.</span>\n";
		} else {
			$Cdbc = BAD . " \n\t <span class='hint'>Prüfe die Konfiguration in ".__FILE__."!</span>\n";
			$STOPP = true;
		}
		while (list(,$a)=each($ret))
			if($a[0]==$databasename) $Cdbc=GOOD;
				
	} else {
		$Cdbc = BAD . " <span class='hint'>Übersprungen!</span>";
	}
	
	echo "\n\tDatenbankstatus\t\t\t\t" . $Cdbc;
	
	unset($SQL);
	$SQL = new MySQL($db2);
	if ($SQL->CONN==false && $create == false) {
		$Csql2c =  BAD . "\n\t <span class='hint'>2. Benutzer existiert nicht, soll aber auch nicht angelegt werden!</span>\n";
		$STOPP=true;
	} elseif($SQL->CONN==true && $create == true) {
		$Csql2c =  BAD . "\n\t <span class='hint'>2. Benutzer soll angelegt werden, existier aber bereits.</span>\n";
		$STOPP=true;	
	} else {
		$Csql2c = GOOD; 
	}
	if($SQL->CONN==false && $create==true) {$hint="\n\t <span class='hint'>Benutzer wird angelegt.</span>";}
	echo "\n\tDatenbankbenutzer, Status \t\t" . $Csql2c.$hint;

	
	/* Installation Options */
	if ($db2['create']==true && $Cdbc==GOOD ) {
		 $Copt_cd =  BAD . "\n\t <span class='hint'>Die Datenbank exisitert bereits und muss nicht angelegt werden. " .
		 		" \n\t Für eine Neuinstallation, lösche die alte." .
		 		"\n\t In ".__FILE__." \n\t setze db2[create] auf false oder \"DROP database\"</span>";
		 $STOPP = true;
	} else {
		$Copt_cd = GOOD;
	}  
	
	echo "\n\nInstallationsoptionen" .
			"\n\tErstelle Datenbank\t\t\t" . $Copt_cd;
	
	echo "\n\n<b>Zusammenfassung</b>\t\t\t\t\t";
	if ($STOPP==true) {
		echo BAD ."\n\t <span class'hint'>Es gab STOPP-Fehler. Löse diese und lade diese Seite <a href'".$PHP_SELF."'>erneut.</a>";
		die("</span></pre></body></html>"); 
	} else {
		echo GOOD . "\n\t Fahre mit der Installation <a href='?installation=start'>fort.</a>";
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
// writing mysql_zugangsdaten into file..
$fh = fopen("configs/main.conf.ini");
/*
[db]
user	=	"cbeckersql2"
pass	=	"321321"
dbase	=	"cbeckersql2"
tableprefix	=	"";
server	=	"localhost"
dbg		=	0

[tables]
entries	=	"entries"

*/
echo "<br><b>Installation done so far.</b><p>" .
		"Go to <a href='../'>homepage</a> to start.";

/* _________________________ functions ______________________*/
echo "</body></html>";

?>