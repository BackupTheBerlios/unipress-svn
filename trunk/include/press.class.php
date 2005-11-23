<?php

//TODO: need test :php 4.3.0 for sha1
//TODO: after error handling is bad. is there is a return false without cmsg, you'll get the last one...'
//HINT: Code is not clear PHP5, but compatible

if (!defined('CLASS_PRESS')) {

	// I need MYSQL CLASS!
	if (!defined('CLASS_MYSQL')) {
		include ("cbmysql.class.php");
	}


/**
 * press
 *
 * @author cbecker@nachtwach.de
 * @link http://php.ch-becker.de/ more PHP Resources
 * @copyright Copyright (c) June-September 2005
 * @version 0.0.1
 * @access public
 */
class press {
	/**
	 * @access private
	 */
	var $prefix = ""; // dbase table prefix

	/**
	 * @access private
	 */	
	var $sites = "sites"; // dbase table, sites
	
	/**
	 * @access private
	 * 
	 */
	var $conn= NULL; // mysql_object connection
	
	/**
	 * @access private
	 * 
	 */
	var $DBG= NULL; // Debug_object connection
	
	/**
	 * @access public
	 */
	function set_prefix($new_prefix) {
		$this->prefix = trim($new_prefix);	
	}	
	
	/**
	 * constructor
	 * @access public
	 */
	function press(& $mysql_object) {
		if (get_class($mysql_object) != "mysql" && get_class($mysql_object) != "MySQL") {
			die("Das �bergebene Object muss vom Typ MySQL sein, es ist aber vom Typ: ".get_class($mysql_object));
		}
		if (str_replace(".", "", $mysql_object->VERSION) < 331) {
			die("Die Ableitung von MySQL muss von Version 3.3.1 oder h�her sein, sie ist Version: ".$mysql_object->VERSION);
		}
		$this->conn = $mysql_object;
		return true;
	}
	
	function set_debugger(& $DBG) {
		if (get_class($DBG) != "Debug" && get_class($DBG) != "debug") {
			die("Das übergebene Object muss vom Typ Debug sein, es ist aber vom Typ: ".get_class($DBG));
		}
		if (str_replace(".", "", $DBG->VERSION) < 30) {
			die("Die Ableitung von Debug muss von Version 3.3.1 oder h�her sein, sie ist Version: ".$DBG->VERSION);
		}
		$this->DBG = $DBG;
		return true;		
	}
	
}	


	define("CLASS_PRESS", 1);
} // define
?>