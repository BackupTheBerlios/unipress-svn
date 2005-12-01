<?php

/**
 *
 * init - file
 * <br>Initiates environment
 * 
 * @version 0.2.0 (09'2004, 11'2004, 06'2005)
 * @copyright 2004 Christoph Becker <cbecker@nachtwach.de>
 * 
 * $Id$
 **/
 
// already defined?
if (isset($INITSTART)) {
	// close SQL, if open
	if (isset($SQL)) {
		$SQL->close();	
	}
	// stop debugger
	if (isset($DBG)) {
		$DBG->quit();
	}
	die();
}

// define, what the script should do
$TODO = array("functions",     // set path to functions
			"include",         // set include path
			"config",         // load config
			"mysql",           // load and start mysql class
		#	"check_tables",		// check mysql tables
		#	"smarty",         // load an start smarty
			"debug",			// load and start/stop debugger
			"definitions",		// load ssome other defines (html etc)
			);

/* define localhosts IP for extra config-file*/
define("LOCALHOSTIP","127.0.0.1");

/* define paths */
define("F_PATH", 'functions/');					// functions path

define("I_PATH", 'include/');					// include path
define("MYSQLFILE", 'cbmysql.class.php');			// mysql.class.file
define("DEBUGGERFILE", 'cbdebug.class.php');	// debug.class.file
define("DEBUGGER", 1);							// logfile Debugger 1=on/0=off
define("INITFILE", 'init.inc.php');				// initializer for variables
define("DEFFILE", 'def_html.inc.php');			// difinitions (html)

define("C_PATH", 'configs/');					// config path

define("TABLESFILE", 'tables.php');				// table check dump

define("S_PATH", 'include/smarty/');			// smarty path
define("SMARTYFILE", 'libs/Smarty.class.php');	// smarty class file

define("T_PATH", 'templates/');					// old templates path


/* load main config */
if ($_SERVER["SERVER_ADDR"]==LOCALHOSTIP) {
	// localhost
	define("CONFIGFILE", 'localhost.conf.ini');			// main config file		
} else {
	define("CONFIGFILE", 'main.conf.ini');			// main config file
}

if (in_array("config", $TODO)) {
	if (!file_exists(C_PATH.CONFIGFILE)) {
		// config doesnt exists
		die("missing config file -> ".C_PATH.CONFIGFILE);
	}
	$VAR	=	parse_ini_file(C_PATH.CONFIGFILE, 1);
}

/* load template engine class */
if (in_array("smarty", $TODO)) {
	if (!file_exists(S_PATH.SMARTYFILE)) {
		// smarty missing
		die("template engine smarty not found -> ".S_PATH.SMARTYFILE);
	}
	require S_PATH.SMARTYFILE;
}

/* Path estimation */
// check OS
if (eregi("^win", PHP_OS)) {
	// Windows found
	// automatic path determination
	$PATH	=	substr(__FILE__, 0, strrpos( __FILE__, "\\")+1);		// Windows Server
} else {
	// non Windows, *NIX
	$PATH = substr($_SERVER["SCRIPT_FILENAME"],0, strrpos($_SERVER["SCRIPT_FILENAME"], "/"));
	//$PATH	=	"";
}

#echo $PATH;

// check path
if ($PATH=="") {
	// empty path is impossible
	die("path is empty, please fill in absolute server path in init.php, line ".(__LINE__-5));
}

/* start smarty */
if (in_array("smarty", $TODO)) {
	$smarty = new Smarty;
	
	/* set smarty dirs */
	$smarty->template_dir	= $PATH.'templates/';
	$smarty->compile_dir	= $PATH.'templates_c/';
	$smarty->config_dir		= $PATH.'configs/';
	$smarty->cache_dir		= $PATH.'cache/';
}

/* load and set mysql */
if (in_array("mysql", $TODO)) {
	if (!file_exists(I_PATH.MYSQLFILE)) {
	    // MySQL Class missing
		die("MySQL class not found -> ".I_PATH.MYSQLFILE);
	}
	require I_PATH.MYSQLFILE;
	
	$db	 = &$VAR['db'];												// point to config
	if (!$SQL = new MySQL($db)) {           // create object
	    die ("<span style=\"color:red;\">database connection could not be etablished!<br>"
			."Please check main config in file '".C_PATH.MYSQLFILE."' section '[db]'</span>");
	}//	access data 
	#echo "<br>MySQL Version:".$SQL->VERSION."<br>";
	
	/* --------- press ---------------*/
	// SQL
	require(I_PATH.MYSQLFILE);
	if (!$SQL = new MySQL($db)) {           // create object
	    die ("<span style=\"color:red;\">database connection could not be etablished!<br>"
			."Please check main config in file '".C_PATH.MYSQLFILE."' section '[db]'</span>");
	}//	access data 
/*
	// PUSER
	if (!$PUSER = new press_user($db)) {           // create object
    die ("<span style=\"color:red;\">database connection could not be etablished!<br>"
		."Please check main config in file '".C_PATH.MYSQLFILE."' section '[db]'</span>");
	}//	access data 
	// PSITES
	if (!$PSITES = new press_sites($SQL)) {           // create object
    die ("<span style=\"color:red;\">database connection could not be etablished!<br>"
		."Please check main config in file '".C_PATH.MYSQLFILE."' section '[db]'</span>");
	}//	access data 
	*/
}

/* check database, all tables */
if (in_array("check_tables", $TODO)) {
	if (!in_array("mysql", $TODO)) {
	    // should check, but db-connection is missing
		echo "<span style=\"color:red;\">Why should I check tables, when I don't have to create database connection?</span>"; 
	} else {
	     // check
		if (file_exists(C_PATH.TABLESFILE) && !array_key_exists("override", $_GET)) {
			$error_table	=	$and	=	"";
			require_once	C_PATH.TABLESFILE;							// table definitions
			while(list($key, $val)=each($VAR['tables'])){				//$table_definition_
				// check, table description exists
				if (array_key_exists($key,$table_definition_)!=false) {
					// check description
					$sql	=	"DESCRIBE ".$val;				// catch description
					$res	=	$SQL->query($sql);
					
			// uncomment this line to get a database structure dump 
					echo "<pre>".$VAR['tables'][$key]."\n\n";var_export($res);echo "\n\n</pre>";
			
					if ($table_definition_[$key]!=$res) {
					    $error_table	.=	$and.$VAR['tables'][$key]." [".$key."]"; $and = " and ";
					}
				} else {
					echo "<br><span style=\"color:red; font-weight:bold\">Notice:There is not Table description"
					." for Table '$val' [$key]</span><br>";
				}
			} // while
			if ($error_table!="") {
				die("<span style=\"color:red; font-weight:bold\">database structure in table(s) '$error_table' is/are invalid!</span>"
					."<br><span style=\"color:green\">Would you like to create a new database with sample data, "
					."just click <a href=\"?m=11&localfile=data.startup&override=true\">here</a>.</span>");
			}
		}
	 else {  // check, but override
		if (array_key_exists("override", $_GET)) {
		    echo "<span style=\"color:green\">I'm not allowed to check table structure! Override in progress</span>";
		} else {
			echo "<span style=\"color:red\">Could not check table structure!</span>";
		}
	}
	}
}
//*/

/* load and init debugger 
*  this debugger writes a logfile to /logs
*/
if (in_array("debug", $TODO)) {
	if (!file_exists(I_PATH.DEBUGGERFILE)) {
	    // MySQL Class missing
		die("Debug class not found -> ".I_PATH.DEBUGGERFILE);
	}
	require I_PATH.DEBUGGERFILE;
	
	$DBG = new Debug;				
	$DBG->debugger			= DEBUGGER;	//	0-debugger off / 1- debugger on
	$DBG->filelink			= 1;
	$DBG->onefile4oneip  	= false;
	$DBG->init();					//	debugger start
	

}

/* load variable initialization */
if (!file_exists(I_PATH.INITFILE) && function_exists("init")) {
    // init-function missing
	die("Neither init function file nor the function itself could be foung -> ".I_PATH.INITFILE);
}
require I_PATH.INITFILE;		// init function for variables $m = init("m", "PG", 0); 

if (in_array("definitions", $TODO)) {
	if (!file_exists(I_PATH.DEFFILE)) {
	    // file missing
		die("Definition file not found -> ".I_PATH.DEFFILE);
	}
	require I_PATH.DEFFILE;
}
	
	
// ok, initscript started
$INITSTART=true;
?>
