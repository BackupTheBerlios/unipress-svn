<?php
/*
 * $Id$
 *
 * UniPress  Installer
 * - create database
 * - create user
 * - create tables
 */
/* Fehlermeldungen unterdrücken */
error_reporting(//E_ALL);//  -> Alle Fehler (debugging)
	E_ERROR | E_PARSE); // -> wichtige Fehler.

require_once ("../include/init.inc.php");
/* Previligierte Datenbankverbindung
 * Dieser "root" Benutzer ist notwendig, um den nicht-previligierten Nutzer
 * und die neue Datenbank anlegen zu lassen.
 * Andernfalls muss $db2[create] und $create auf false gesetzt werden  
 */
$db['user'] = "mpnq";
$db['pass'] = "elo6dir";
$db['dbg']	= 0; 		// 0=silent (debug mode of mysql-class) 

/* database stuff */
$db['server']	= "localhost";
$db['dbase']	= "presse"; // Datenbank, die zu testzwecken benutzt wird (keine Schreiboperationen)

/* Daten des unpreviligierten DB Nutzers */
$db2 = $db; 			// kopiere Rahmendaten (wie Server...)
// drop old user, if exist (for this db)
$drop_olduser= init("drop_user","r",false);			
// create this user (for this db)
$create		 = init("create_user","r",false);		

/* Zugangsdaten unprev. Benutzer */
$db2['user'] = init("user","r",$db['user']); //'"unipressuserd");
$db2['pass'] = init("pass","r",$db['pass']); //"up9283");

$db2['dbase'] = init("dbase","r","presse");// name of database I should use or create
$db2['create']= init("create_db","r",false);  // create database (false, if it already exists)
$prefix		  = "";			// table prefix
$db2['create_T']=init("create_t","r",false);	// create tables, true is recommented if no backup should be restored
$db2['create_AU']=init("create_a","r",false);// create Adminuser (user. admin, pass: adminpass)

// full path to installation
if (eregi("^win", PHP_OS)) {
	// Windows found
	// automatic path determination
	$fullpath	=	substr(__FILE__, 0, strrpos( __FILE__, "\\") - 6);		// Windows Server
} else {
	// non Windows, *NIX
	$fullpath = substr($_SERVER["SCRIPT_FILENAME"],0, strrpos($_SERVER["SCRIPT_FILENAME"], "/") - 7);
}


$min['php'] = "440";
/***************************************************************************/
/*                     do not modify anything below!                       */
/***************************************************************************/

define("GOOD","<span style='color:green;font-weight:bold;'>OK</span>");
define("OK","<span style='color:black;background-color:yellow;'>OK</span>");
define("BAD","<span style='color:red;font-weight:bold;'>Schlecht!</span>");
define("CONFIGFILE","../configs/main.conf.ini");
/* ------------------------------------------------------------------------*/
echo "<html><head><title>Installer V0.1 - Unipress</title>" .
	"<link rel=\"stylesheet\" type=\"text/css\" href=\"mycss.css\" />".
	"<body>";
		
if(!(array_key_exists("installation",$_REQUEST) && $_REQUEST['installation']=="start")) {
	echo "<br><br><b>Prüfe Abhängigkeiten für die Installation:</b><pre>";
	/* OS Test */
	$STOPP = false;
	$stopp_array = array();
	$Cos = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? OK . " (".PHP_OS.")" : GOOD. " (".PHP_OS.")";
	echo "\nBetriebssystem\t\t\t\t\t" . $Cos;
			
	/* PHP Version */
	$ver = (int) str_replace(".","",substr(phpversion(),0,5));
	if ($ver >= $min['php']){
		$Cphp = GOOD . " (".phpversion().") ";
		
	} else {
// E10
		$Cphp = BAD  . " (".phpversion().") " . " \n\t <span class='hint'>Bitte installieren Sie eine neuere <a href='http://www.php.net'>PHP Version</a>!</span>\n";
		$STOPP =true;
		array_push($stopp_array, "php version (".phpversion().") too old [E10]");
	}
	
	echo "\nPHP-Version\t\t\t\t\t" . $Cphp;
	
	/* FileSystem Test*/
	require_once("../include/check_writeable.inc.php");
	
	$Clogdir = check_writeable($fullpath, "logs",false)==true ? GOOD : "Nein, " .BAD . " \n\t <span class='hint'>Ändern mit: chmod 777 logs/</span>\n";
	$Cuploaddir = check_writeable($fullpath, "uploaded",false)==true ? GOOD : "Nein, " .BAD. " \n\t <span class='hint'>Ändern mit: chmod 777 uploaded/</span>\n";
	$Csettings = is_writable(CONFIGFILE)==true ? GOOD : "Nein, " .BAD. " \n\t <span class='hint'>Ändern mit: chmod 777 configs/main.conf.ini/</span>\n";
// E20
	if($Clogdir!=GOOD || $Cuploaddir!=GOOD ||  $Csettings!=GOOD) { 
		$STOPP = true;
		 array_push($stopp_array, "file/dir not writable [E20]");
	}
	echo "\n\nDateisystem" .
			"\n\tInstallationspfad\t\t\t".$fullpath.
			"\n\tlog-Verzeichnis ist beschreibbar?\t".$Clogdir .
			"\n\tupload-Verzeichnis ist beschreibbar?\t".$Cuploaddir.
			"\n\tEinstellungsdatei ist beschreibbar?\t".$Csettings;
	
	/* MYSQL Test */
	require_once("../include/cbmysql.class.php");
	
	// MySQL Module detector
	check_mysql_interface("LOAD_MYSQLI");
	echo "\n\nDatenbankverbindung" . 
			"\n\tPHP-Modul\t\t\t\t";
	
	if(defined('LOAD_MYSQLI')) echo GOOD . " (MySQL<b>I</b>)"; 
	else {
		echo OK . " (MySQL)";
// W10		
		echo "\n\t <span class='hint'>Dies ist nicht die beste Variante, aber funktioniert. " .
				"\n\t Besser, man benutzt MySQL4.1 und " .
				"PHP5 mit <a href=\"http://php.net/mysqli\">mysql<b>i</b></a> " .
				"als MySQL-Handler. [W01]</span>\n";
	}	
	
	$SQL = new MySQL($db);
// E30
	$Csqlc = ($SQL->error_no!=0) ? BAD . "\n\t <span class='hint'>Prüfe die Konfiguration in ".__FILE__." ! </span>\n " : GOOD; 
	if($Csqlc!=GOOD) {
		$STOPP = true;
		array_push($stopp_array, "DB conf is false (E30)");
	}
	
	echo "\n\tVerbindung (Konfigurationsdaten)\t" . $Csqlc;
	
	if( $Csqlc==GOOD ) {
		$found=false;
		$databasename =        $db2['dbase'];
		$ret = $SQL->query("Show databases");
		$possible_dbs = ""; $tmp="";
		while (list(,$a)=each($ret))
		{
			$possible_dbs .= $a[0].$tmp; $tmp = ", ";
			if($a[0]==$databasename) 
				$found=true;	
		}
			
		if ($db2['create']==true) {
			if($found) {
// E33
				$Cdbc = BAD . "\n\t <pan class='hint'>Datenbank exisitert bereits. Anlegen unm&ouml;glich!</span>\n";
				$STOPP = true;
				array_push($stopp_array, "DB conf is false, db existing (E33)");
			} else
				$Cdbc = OK . " \n\t <span class='hint'>Datenbank wird angelegt.</span>\n";
		} else {
			if (!$found) {	
// E35
				$Cdbc = BAD . " \n\t <span class='hint'>Prüfe die Konfiguration in ".__FILE__."!" .
						"\n\t DB '".$db2['dbase']."' soll nicht angelegt werden, existiert aber auch nicht.\n" .
						"\t Zugriff besteht auf: ".$possible_dbs."</span>\n";
				$STOPP = true;
				array_push($stopp_array, "DB conf is false, db not existing, no creation (E35)");
			} else
				$Cdbc = OK;
		}
				
	} else {
// W20
		$Cdbc = BAD . " <span class='hint'>Übersprungen! [W20]</span>";
	}
	
	echo "\n\tDatenbankstatus\t\t\t\t" . $Cdbc;
	
	unset($SQL);
	$SQL = new MySQL($db2);
// E40
	if ($SQL->CONN==false && $create == false) {
		$Csql2c =  BAD . "\n\t <span class='hint'>2. Benutzer existiert nicht, soll aber auch nicht angelegt werden! </span>\n";
		$STOPP=true;
		array_push($stopp_array, "do not create 2nd user, but not existing [E40]");
	} elseif($SQL->CONN==true && $create == true) {
// E45
		$Csql2c =  BAD . "\n\t <span class='hint'>2. Benutzer soll angelegt werden, existier aber bereits. </span>\n";
		$STOPP=true;	
		array_push($stopp_array, "create 2nd user, but existing [E45]");
	} else {
		$Csql2c = GOOD; 
	}
	if($SQL->CONN==false && $create==true) {
		$hint="\n\t <span class='hint'>Benutzer wird angelegt.</span>";
	}
	echo "\n\tDatenbankbenutzer, Status \t\t" . $Csql2c.$hint;

	
	/* Installation Options */
// E50	
	if ($db2['create']==true && $Cdbc==GOOD ) {
		 $Copt_cd =  BAD . "\n\t <span class='hint'>Die Datenbank exisitert bereits und muss nicht angelegt werden. " .
		 		" \n\t Für eine Neuinstallation, lösche die alte." .
		 		"\n\t In ".__FILE__." \n\t setze db2[create] auf false oder \"DROP database\"</span>";
		 $STOPP = true;
		 array_push($stopp_array, "existing database [E50]");
	} else {
		$Copt_cd = GOOD;
	}  
	
	echo "\n\nInstallationsoptionen" .
			"\n\tErstelle Datenbank\t\t\t" . $Copt_cd;
	
	echo "\n\n<b>Zusammenfassung</b>\t\t\t\t\t";
	if ($STOPP==true) {
		echo BAD ."\n\t <span class='hint'>Es gab STOPP-Fehler. Löse diese und lade diese Seite " .
				"<a href'".$PHP_SELF."'>erneut.</a>\n\n\t Dump:\n<blockquote>";
		var_dump($stopp_array);
		die("</blockquote></span></pre></body></html>"); 
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
	if ($SQL->error_no!=0) die(BAD . " failed! -&gt; ".$SQL->error_msg); else echo GOOD;
} else {
	echo "<br>Testing Database " . $db2['dbase']. " ... ";
	$databasename =        $db2['dbase'];
	$ret = $SQL->query("Show databases");
	
	$found=false;
	while (list(,$a)=each($ret))
		if($a[0]==$databasename) $found=true;
	if ($found==false)
		die(BAD . " database could not be found. Check your config!");	
}

if ($create) {
	echo "<br>Creating User " . $db2['user'] . " ... ";
	$databasename 	=       $db2['dbase'];
	$username		=		$db2['user'];
	$userpass		=		$db2['pass'];
	include "create_user.php";
	if ($SQL->error_no!=0) die(BAD . " failed! -&gt; ".$SQL->error_msg); else echo GOOD;
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
else echo GOOD;

if($db2['create_T']){
	echo "<br>Creating Tables ... ";
		
		include "database.php";
		while(list(,$q)=each($table)) {
		#	echo "<br>".$q;
			if(!$SQL->query($q)) die( "<br>Error while doing: " . $q . " -&gt; ".$SQL->error_msg);
		}
	echo GOOD;
}
// Create Admin User
if($db2['create_AU']) {
	echo "<br>Creating Admin User with (admin and adminpass) ... "; 
	$SQL->insert("INSERT INTO `".$prefix."press_user` ( `id` , `name` , `pass` , `counter` , `session` , `auth` ) VALUES ('', 'admin', '".sha1("adminpass")."', '0', '',  '0');");
	$SQL->insert("INSERT INTO `".$prefix."press_admins` ( `id`  ) VALUES ('1');");
	echo GOOD;
}

// writing mysql_zugangsdaten into file..
	echo "<br>Schreibe Konfigurationsdatei '".CONFIGFILE."' ... ";
$fh = fopen(CONFIGFILE,"w");
if(fwrite($fh, "[db]\n" .
		"user=\"".$db2['user']."\"\n" .
		"pass=\"".$db2['pass']."\"\n" .
		"dbase=\"".$db2['dbase']."\"\n" .
		"tableprefix=\"".$prefix."\"\n" .
		"server=\"".$db2['server']."\"\n" .
		"dbg=0\n" .
		"[tables]\n" . 
		"entries	=	\"entries\""
		)) echo GOOD; else echo BAD;
fclose($fh);
echo "<br><b>Installation done so far.</b><p>" .
		"Go to <a href='../'>homepage</a> to start.";

/* _________________________ functions ______________________*/
echo "</body></html>";

?>