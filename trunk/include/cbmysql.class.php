<?php
/**
 * MySQL - MySQL PHP Object Class / mysql(i) Version
 * uses mysqli_* AND mysql_ functions
 * it has been tested with php 5.0.5 and mysqli (mysql4.1)
 * 
 *

MySQL PHP Class - provides MySQL 3.x-5.0 connectivity to PHP
Copyright (C) 2002-2005 Christoph Becker <cbecker@nachtwach.de>

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 **/
/*
 2006-02-18
o changes to 4.0.3
 +check_mysql_interface tries to (re)load mysql extension
 #debug default_Class_Debug could only be set via Construktors array or
        set_DBG - now it's also possibly to set local $DBG
        
 2005-11-21
o changes to 4.0.2
  +<String> clean_in(<String> $in)
   removes whitespaces at the begin and end (trim)
   adds slashes
  #debug, preload test, double declaration from now on it's impossible 
  
o changes to 4.0.1
 2005-11-07
 #debug
  preload test, if mysql_ or mysqli_ is avaible...
  if avaible, mysqli_ is the better choice

o changes to 4.0.0
 2005-10-07
  double code, supports mysql(i)_*
  
o changes to 3.5.0
 2005-10-06
  added: set_DBG / get_DBG
 2005-09-25
  all functions were transoformed to mysqli_* precedural model
  and it is not recommented to use this class in new projects
  
o changes to 3.4.0
 2005-09-08
  bug fixed: if update has no affected rows, return true 
 + get_affected_rows() - returns affected rows as int 
 ? interfaces fpr sql-logging implemented ?!
 2005-08-12
  code cleared: unreachable code removed (breaks after return)
 2005-06-08
  bug fixed: update returns false on error
  bug fixed: change_db
  
o changes to 3.3.0
 2005-05-30 time measuring algorith implemented
 - right way of catching update errors
 - set_select_type returns selected "SELECT" type
 
o changes to 3.2.5
 2005-03-22 ::close now exists, could close mysql connection, set also
 $TRY_persistent = false !
 
o changes to 3.2.4
 2004-12-03 ::query not you could give a string OR an array as argument to it

o changes to 3.2.3
 2004-11-06 debug: init $data in ::query

o changes to 3.2.2
 2004-11-01 debug: notices because
 select2csv: filename could be set in  $this->csv_filename, $this->csv_extension

o changes to 3.2.1
 2004-09-29 ::delete returns affected rows or false

o changes to 3.2.0
 2004-09-27 bugfix, ::query() - first line in array had no name, only empty string
 now it' zero
 2004-09-26 ::select() with true as 2n arg, returnes smarty compatible arrays
*/

/**
 * PreTest
 * if mysqli_ is avaible, choose that, otherwise mysql_
 * TODO: class-factory like in java
 * 
 */
if (!function_exists("check_mysql_interface")) { // anti-redeclare

function check_mysql_interface($preference="LOAD_MYSQLI"){
	$mysqli = function_exists("mysqli_connect");
	$mysql  = function_exists("mysql_connect");
	if($mysqli && $mysql) {
		define($preference, true);
	} elseif($mysqli) {
		// define missing constants ~ transparent for the user
		// this is for maximum code compatibility
		if(!defined("MYSQL_NUM")) 
		{
			define("MYSQL_NUM", MYSQLI_NUM);
			define("MYSQL_BOTH",  MYSQLI_BOTH); 
			define("MYSQL_ASSOC", MYSQLI_ASSOC);
		}
		if(!defined("LOAD_MYSQLI")) define('LOAD_MYSQLI', true);	
		
	} elseif($mysql) {
		if(!defined("MYSQLI_NUM")) 
		{
			define("MYSQLI_NUM", MYSQL_NUM);
			define("MYSQLI_BOTH",  MYSQL_BOTH); 
			define("MYSQLI_ASSOC", MYSQL_ASSOC);
		}
		if(!defined("LOAD_MYSQL")) define('LOAD_MYSQL', true);
		
	} else {
		if (defined("MYSQL_SO_LOAD")) die("MySQL-Class Load Fatal Error. Unable to find mysql or mysqli Extension. Stop!");
		// or:
		// load mysql extension based on OS and recheck
		if (!extension_loaded('mysql')) {
		   if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		       dl('php_mysql.dll') or die("cbmysql class couldn't load php_mysql.dll");
		   } else {
		       dl('mysql.so') or die("cbmysql class couldn't load mysql.so");
		   }
		}
		check_mysql_interface("LOAD_MYSQL");
		define("MYSQL_SO_LOAD", true);
	}
}

// removes bad chars in input		
function clean_in($q) {
	//return preg_replace('/([\W])/i',"\\\\\\1",$q);
	return addslashes(trim($q));
}

/**
 * Check PHP/MySQL Version (to use mysql_ or mysqli_)
 */
check_mysql_interface();


} // check



/**
 * MySQL
 *
 * @author cbecker@nachtwach.de
 * @link http://www.ch-becker.de/?php more PHP Resources
 * @copyright Copyright (c) 2002-2005
 * @version 4.0.1
 * @access public
 */
if (!defined('CLASS_MYSQLI') && defined('LOAD_MYSQLI')) {

	class MySQL {
		VAR $VERSION = "4.0.1";

		VAR $SELECT_TYPE = MYSQLI_BOTH;

		VAR $affected_rows = 0;
		/**
		 * MySQL::measure_time - should the script measure sql times?
		 *
		 * int
		 *
		 * @since 2005-05-30
		 * @access public
		 */
		VAR $measure_time = 1; // 0 - none; 1 - milli secs

		// protected
		VAR $temptime = 0; //temp timememory

		/**
		 * MySQL::csv_filename - name without extension of future csv export file
		 *
		 * String
		 *
		 * @since 2004-11-01
		 * @access public
		 */
		VAR $csv_filename = "data";

		/**
		 * MySQL::csv_extension - extension of future csv export file
		 *
		 * String
		 *
		 * @since 2004-11-01
		 * @access public
		 */
		VAR $csv_extension = "csv";

		/**
		 * MySQL::save2file - should i try to save exported csv file?
		 *
		 * int
		 *
		 * @since 2003-11-25
		 * @access public
		 */
		VAR $save2file = 0;

		/**
		 * MySQL::prompt4file - prompt with "save file" dialog when exporting csv file (1-yes/0-no)
		 *
		 * (0) - select2csv only returns csv-string-data
		 * (1) - select2csv also return csv-string-data but add. header-info to get save file dialog in your browser
		 *
		
		 * @see testsuite_csv.php
		
		int
		 * @since 2003-11-23
		 * @access public
		 */
		VAR $prompt4file = 1;

		/**
		 * MySQL::error_no - last Error No
		 *
		 * int
		 *
		 * @access public
		 */
		VAR $error_no;

		/**
		 * MySQL::error_msg - last Error Msg
		 *
		 * string
		 *
		 * @access public
		 */
		VAR $error_msg;

		/**
		 * MySQL::error_cmsg - last Custom Error Msg
		 *
		 * string
		 *
		 * @access public
		 */
		VAR $error_cmsg;

		/**
		 * MySQL::fatalerros - list of errornumbers, we should stop, regardless of which 
		 * errorlevel is defined
		 *
		 * string
		 *
		 * @access public
		 */
			VAR $fatalerrors = array (//1045, // Access denied for user
	);

		/**
		 * MySQL::akt_DBASE - actuall database
		 *
		 * @access public
		 */
		VAR $akt_DBASE;

		/**
		 * MySQL::commands	array	counts all method-calls in an associative array
		 *
		 * @access private
		 */
		// VAR $commands	=	array();
		VAR $commands = array ("alter" => 0, "select" => 0, "insert" => 0, "delete" => 0, "update" => 0, "query" => 0, "sum" => 0, "alter_time" => 0, "select_time" => 0, "insert_time" => 0, "delete_time" => 0, "update_time" => 0, "query_time" => 0, "sum_time" => 0);

		/**
		 * MySQL::$CONN	connection handle
		 *
		 * @access private
		 */
		VAR $CONN;

		/**
		 * MySQL::DBG	debuglevel, set by config file
		 *
		 * @access private
		 */
		VAR $DBG = 0; // if you have some startup errors, it's fine to see them.
		// 0-liefert "nur" false zur�ck; Fehlerbehandlung in der aufrufenden Funktion
		// 1-zeigt Fehler per echo an, liefert false, l��t das Script jedoch weiterlaufen
		// 2-"stirbt" mir Fehlermeldung ... die("Fehler)
		// 3-bricht ab ohne Fehleraus- oder R�ckgabe (stiller Tod)
		/**
		 * MySQL::last_DBASE	database used bevore actual
		 *
		 * @access private
		 */
		VAR $last_DBASE;

		/**
		 * MySQL::field_dimension dimension of the returning array from select
		 *
		 * @access private
		 */
		VAR $field_dimension = 2;
		// 2 - always two dimensions
		// 1 - automatic one/two-dim

		/**
		 * MySQL::TRY_persistent try to connect with persistent connection
		 *
		 * @access private
		 */
		VAR $TRY_persistent = true; // Soll versucht werden persistente Verbindungen aufzubauen?
		// Bei Transaktionen oder Locking nicht sinnvoll!

		VAR $STATUS_persistent = false; // Status, ob eine persistente Verbindung aufgebaut wurde oder oder nicht

		/**
		 * MySql::error() - Error Handler
		 *
		 * @access protected
		 * @param string $text Custom Error Text
		 * @return Boolean false (always)
		 */
		function error($text = "-") {
			$this->error_no = mysqli_errno($this->CONN);
			$this->error_msg = mysqli_error($this->CONN);
			$this->error_cmsg = $text;

			$msg = "<pre style=\"color:red;background-color:white;font-size:12px;\"><b>Custom Error Message: $text ";
			if ($this->error_no > 0)
				$msg .= "\nMySQL ErrorNo: ".$this->error_no."\r\nMySQL ErrorMsg: ".$this->error_msg."\r\nMySQL State: ".mysqli_sqlstate($this->CONN);
			$msg .= "\r\n </b></pre>\r\n ";

			// override MAIN debug config
			// halt on connection errors
			if (in_array($this->error_no, $this->fatalerrors)) {
				$this->DBG = 2;
				$msg = "<div style=\"font-size:20px;color:red;backgorund-color:white;\"><strong>FATAL!</strong><br> ".$msg."</div>";

			}

			switch ($this->DBG) {
				case 0 :
					// errorlog?
					//f_write("tmp/mysql/mysql_error_".$_SERVER["REMOTE_ADDR"]."_".microtime().".txt",$text."\r\n".$this->error_no."\r\n".$this->error_msg);
					return false; // do not need break, because of return
				case 1 :
					echo $msg;
					return false;
				default :
				case 2 :
					debug_print_backtrace2();
					die($msg);
					return false;
				case 3 :
					die();
			} // switch
			return false; // no path will reach this
		}

		/**
		 * MySQL::MySQL() 
		 *
		 * Contructor
		 *
		 * @param string $SERVER servername (e.g. localhost) OR array containing ['user|pass|dbase|server']
		 * @param string $USER username
		 * @param string $PASS password
		 * @param string $DBASE database
		 * @return bool
		 */
		// real constructor
		function MySQL($SERVER, $USER = "", $PASS = "", $DBASE = "", $DBG = 1) {
				// check if configarray is given or not
	if (is_array($SERVER)) {
				if (!array_key_exists("server", $SERVER)) {
					$this->error("given config array does not contain \"server\"!");
					return false;
				}
				if (!array_key_exists("user", $SERVER)) {
					$this->error("given config array does not contain \"user\"!");
					return false;
				}
				if (!array_key_exists("pass", $SERVER)) {
					$this->error("given config array does not contain \"pass\"!");
					return false;
				}
				if (!array_key_exists("dbase", $SERVER)) {
					$this->error("given config array does not contain \"dbase\"!");
					return false;
				}
				if (array_key_exists("dbg", $SERVER)) {
					$this->set_DBG($SERVER['dbg']);
				}

				$USER = $SERVER['user'];
				$PASS = $SERVER['pass'];
				$DBASE = $SERVER['dbase'];
				$SERVER = $SERVER['server'];

			}

			
			// set mysql_fetch_array to associative arrays, look at php manual "mysql_fetch_array()"
			$this->set_select_type(MYSQLI_BOTH);
			// lookup for persistent connections
			$conn = mysqli_connect($SERVER, $USER, $PASS);
			if (!$conn) {
				echo mysqli_connect_error();
				//return $this->error( "no connection!" );
			}
			if (!mysqli_select_db($conn, $DBASE)) {
				$this->CONN = $conn;
				//return $this->error( "no database!" );
			}
			$this->akt_DBASE = $DBASE;
			$this->CONN = $conn;
			return true;
		}

		/**
		 * MySQL::set_DBG() - sets Debug level
		 * 
		 * <br> if you have some startup errors, it's fine to see them.
		 * <br>0-liefert "nur" false zur�ck; Fehlerbehandlung in der aufrufenden Funktion
		 * <br>1-zeigt Fehler per echo an, liefert false, l��t das Script jedoch weiterlaufen
		 * <br>2-"stirbt" mir Fehlermeldung ... die("Fehler)
		 * <br>3-bricht ab ohne Fehleraus- oder R�ckgabe (stiller Tod)<br>
		 *
		 * @param int $newdbg - 0->4 
		 * @return boolean
		 * @access public
		 * @since 2005-10-06
		 */
		function set_DBG($newdbg) {
			if ($newdbg >= 0 and $newdbg < 4) {
				$this->DBG = $newdbg;
				return true;
			} else {
				return false;
			}

		}

		/**
		 * MySQL::get_DBG() - sets Debug level
		 * 
		 * <br> if you have some startup errors, it's fine to see them.
		 * <br>0-liefert "nur" false zur�ck; Fehlerbehandlung in der aufrufenden Funktion
		 * <br>1-zeigt Fehler per echo an, liefert false, l��t das Script jedoch weiterlaufen
		 * <br>2-"stirbt" mir Fehlermeldung ... die("Fehler)
		 * <br>3-bricht ab ohne Fehleraus- oder R�ckgabe (stiller Tod)<br>
		 *
		 * 
		 * @return int 
		 * @access public
		 * @since 2005-10-06
		 */
		function get_DBG() {
			return	$this->DBG;
		}
		/**
		 * MySQL::checkup() - checks sql-query is not empty, connection is still alive and query is right
		 *
		 * @param string $method method (insert, select etc)
		 * @param string $sql sql query
		 * @return boolean
		 * @access protected
		 * @since 2003-11-25
		 */
		function checkup($method = "", & $sql) {
			$sql = trim($sql);
			$method = trim($method);

			if (empty ($sql)) {
				$temp = $method == "" ? "Name" : "Query";
				return $this->error($temp." is empty: $sql ");
			}
			if ($method != "" && !eregi("^".$method, $sql)) {
				return $this->error("Error, its not a ".$method."-query: $sql ");
			}
			if (empty ($this->CONN)) {
				return $this->error("No connection or connection lost");
			}
			if ($method != "" && !eregi("^CREATE DATABASE", $sql) && $this->akt_DBASE == "") {
				return $this->error("No database! Create database first, then change actiove DB!");
			}

			return true;
		}

		/**
		 * MySql::new_db()
		 *
		 * creates new database
		 *
		 * @param string $name Datenbankname
		 * @return bool
		 */
		function new_db($name) {
			$sql = "CREATE DATABASE IF NOT EXISTS ".$name;
			if ($this->checkup("", $sql) != true) {
				return $this->error("an error occured!");
			}

			#		if ( mysqli_query ($this->CONN, $sql ) ) {
			if ($this->one_query($sql)) {
				return true;
			} else {
				return $this->error("error while creating database '$name'!");
			}
		}

		/**
		 * MySql::change_db()
		 *
		 * changes active database
		 *
		 * @param string $name database
		 * @return bool
		 */
		function change_db($name) {
			$name = trim($name);

			if (empty ($name)) {
				return $this->error("DB-Name is empty: $name ");
			}
			if (empty ($this->CONN)) {
				return $this->error("No connection or connection lost");
			}

			if (mysqli_select_db($this->CONN, $name)) {
				$this->last_DBASE = $this->akt_DBASE;
				$this->akt_DBASE = $name;
				return true;
			} else {
				return $this->error("error changing database!");
			}
		}

		/**
		 * MySql::drop_db()
		 *
		 * drops a db
		 *
		 * @param string $name database
		 * @return bool
		 */
		function drop_db($name) {
			if ($this->checkup("", $name) != true) {
				return $this->error("no database name given");
			}
			#if ( $this->one_query ( "DROP DATABASE IF EXISTS " . $name ) ){ // seems to be buggy
			if (!mysqli_query($this->CONN, "DROP DATABASE IF EXISTS ".$name)) {
				return $this->error("could not drop database!");
			}
			// if db dropped, change to last database, if exists
			if ($name == $this->akt_DBASE && $this->last_DBASE != "") {
				$this->change_db($this->last_DBASE);
			}
			return true;
		}

		/**
		 * MySql::create()
		 *
		 * sends every query to db whitsch begins with "create" - it is not case sensitive
		 *
		 * @param string $sql SQL-create-Query
		 * @return bool
		 */
		function create($sql) {
			$result = $this->checkup("create", $sql);
			if ($result == false) {
				return $this->error("creation fails.");
			}
			if (!$this->one_query($sql)) {
				$this->error("could not create anything [ ".$sql." ]");
				return false;
			}
			return true;
		}

		/**
		 * MySql::empty_table()
		 *
		 * truncates a table
		 *
		 * @param string $name tablename
		 * @return bool
		 */
		function empty_table($name) {
			$result = $this->checkup("", $name);
			if ($result == false) {
				return $this->error("could not truncate.");
			}

			#if ( mysql_query ( "TRUNCATE TABLE " . $name, $this->CONN ) ) {
			if ($this->one_query("TRUNCATE TABLE ".$name)) {
				return true;
			} else {
				$this->error("error truncating table '$name' !");
				return false;
			}
		}

		/**
		 * MySql::optimize()
		 *
		 * optimizes table
		 *
		 * @param string $name tablename
		 * @return bool
		 */
		function optimize($name) {
			$result = $this->checkup("", $name);
			if ($result == false) {
				return $this->error("could not optimize.");
			}

			if ($this->one_query("OPTIMIZE TABLE ".$name)) {
				return true;
			} else {
				$this->error("error optimizing table '$name' !");
				return false;
			}
		}

		/**
		 * MySql::drop_table()
		 *
		 * drops a table
		 *
		 * @param string $name table
		 * @return bool
		 */
		function drop_table($name) {
			$name = trim($name);
			if (empty ($name)) {
				return false;
			}
			if (empty ($this->CONN)) {
				return false;
			}
			#if ( mysql_query ( "DROP TABLE IF EXISTS " . $name, $this->CONN ) ) {
			if ($this->one_query("DROP TABLE IF EXISTS ".$name)) {
				return true;
			} else {
				$this->error("error dropping table!");
				return false;
			}
		}

		/**
		 * MySQL::set_select_type()
		 *
		 * sets the resulttype of mysql_fetch_array used in sql->select()
		 *
		 * @param  $type one out of "MYSQL_ASSOC", "MYSQL_NUM", "MYSQL_BOTH"
		 * @return
		 */
		function set_select_type($type) {
			$allowed = array (MYSQLI_NUM, MYSQLI_BOTH, MYSQLI_ASSOC);
			if (in_array($type, $allowed)) {
				$this->SELECT_TYPE = $type;
			}
			return $this->SELECT_TYPE;
		}

		//	select field return
		// 2 - always two dimensions
		// 1 - automatic one/two-dim
		function set_field_dimension($type = "0") {
			$allowed = array (1, 2);
			if (in_array($type, $allowed)) {
				$this->field_dimension = $type;
			}
			return $this->field_dimension;
		}
		function get_field_dimension() {
			return $this->field_dimension;
		}
		/**
		 * MySQL::select()
		 *
		 * queries db with select and returns an array with one or two dimension, dependion on entities
		 * if a entity is named INDEX_ASSOCIATION you'll have an associative array with this entity als key
		 * SELECT id AS INDEX ASSOCIATION, name, land FROM xy ...
		 * the array will be $result[id-entry][name|land] = value...
		 * id-entry is in ( id1, id2 ...)
		 * <br>
		 * 07-26-2004 3.1.0<br>
		 *     + via MySQL->fiel_dimension, you could say, you want optional 1-d arrays or not
		 * <br>
		 * 01-30-2004 3.0.0pre3			<br>
		 *     + choose via constant SELECT_TYPE if you want to have NUM, ASSOC or BOTH Types in ARRAY
		 *       look at set_select_type()
		 * <br>
		 * 09-28-2004 3.1.0 <br>
		 *     + 2nd argument = True says, that output shout be smarty array
		 *
		 * @param string $sql
		 * @param int $csvdata
		 * @return array
		 */
		function select($sql = "", $smarty = false) {
				#echo "<br>".$sql."<br>";
			$result = 0;
			$this->time_start();
			$r = $this->checkup("select", $sql);
			if (!$r) { $this->error("error queriing, its not a select-query: ".$sql); return false; }
			
			$this->affected_rows = 0;

			// if there is an SHOW query, there is MYSQL_BOTH needed as mysql_fetch_array result type
			if (eregi("^show", $sql)) {
				$temp_type = $this->SELECT_TYPE;
				$this->SELECT_TYPE = MYSQLI_BOTH;
			}
			$conn = $this->CONN;

			$results = mysqli_query($conn, $sql) or $this->error($sql);
			if ((!$results) or (empty ($results))) {
				//mysqli_free_result($conn); // nothing to free
				return false;
			}

			$count = 0;
			$data = array ();

			$this->affected_rows = mysqli_affected_rows($conn);

			// create a smarty readable array
			if ($smarty == true) {
				while ($row = mysqli_fetch_array($results, $this->SELECT_TYPE)) {
					// if there's only one column and the script is allow to create,
					// create a 1-d array
					array_push($data, $row);
				}
				// normal int associated array
			}
			elseif (!ereg("INDEX_ASSOCIATION", $sql)) {
				// normal results return (1-d, 2-d arrays)
				$anz_rows = mysqli_num_fields($results);
				while ($row = mysqli_fetch_array($results, $this->SELECT_TYPE)) {
					// if there's only one column and the script is allowed to create,
					// create a 1-d array
					if ($anz_rows < 2 && $this->field_dimension == 1) {
						$data[$count] = $row[0];
					} else {
						$data[$count] = $row;
					}
					$count ++;
				}
			} else {
				// build index-assozitive 1-d/2-d array
				// entity named INDEX_ASSOCIATION would be the array-key
				while ($row = mysqli_fetch_array($results)) {
					$count = $row['INDEX_ASSOCIATION'];
					// if theres only one entity left, build a 1-d array
					if (count($row) > 4) {
						// remove ASSOCIATIONs, not needed
						$data[$count] = $row;
					} else {
						if ($row[1] == $row['INDEX_ASSOCIATION']) {
							$data[$count] = $row[0];
						} else {
							$data[$count] = $row[1];
						}
					}
				}
			}
			if (eregi("^show", $sql)) {
				$this->SELECT_TYPE = $temp_type;
			}

			mysqli_free_result($results);
			$this->commands["select"]++;
			$this->commands["sum"]++;

			$this->time_stop("select");

			return $data;
		}

		/**
		 * MySQL::get_affected_rows()
		 *
		 * returns number of rows that were affected by last query
		 *
		 * 
		 * @return int affected rows
		 */
		// FIXME: seams to have a bug; does not return right value
		function get_affected_rows() {
			return $this->affected_rows;
		}

		/**
		 * MySQL::insert()
		 *
		 * inserts a "insert"-query
		 *
		 * @param string $sql
		 * @return int inserted id | affected rows
		 */

		function insert($sql = "") {
			$this->time_start();

			if (empty ($sql)) {
				return false;
			}
			if (!eregi("^insert", $sql)) {
				return $this->error("error, its not a insert-query: ".$sql);
			}
			if (empty ($this->CONN)) {
				return false;
			}
			$conn = $this->CONN;
			$results = mysqli_query($conn, $sql) or $this->error($sql);
			$results = mysqli_insert_id($conn);
			$results == 0 ? $results = mysqli_affected_rows($conn) : 1;

			$this->time_stop("insert");
			return $results;
		}

		/**
		 * MySQL::update()
		 *
		 * updates db
		 *
		 * @param string $sql
		 * @return boolean true if result | false if no result
		 */
		function update($sql = "") {
			$this->time_start();
			if (empty ($sql)) {
				return false;
			}
			if (!eregi("^update", $sql)) {
				$this->error("error, its not a update-query: ".$sql);
				return false;
			}
			if (empty ($this->CONN)) {
				return false;
			}
			$conn = $this->CONN;

			// cdhack for errors
			$results = $this->one_query($sql); 
			/*mysql_query($sql, $conn); //old: mysql_query( $sql, $conn );
			if ($results == false) {
				$this->error("could not exec update(): ".$sql);
				return false;
			}*/
			
			$this->time_stop("update");
			return $results;
		}

		/**
		 * MySQL::delete()
		 *
		 * send delete query
		 *
		 * @param string $sql
		 * @return int affected rows (since 3.2.1)
		 */
		function delete($sql = "") {
			$this->time_start();

			if ($this->checkup("delete", $sql) != true) {
				return $this->error("no delete query: ".$sql);
			}

			$conn = $this->CONN;
			$results = $this->one_query($sql);
			$results = $this->affected_rows;// mysql_affected_rows($conn);

			$this->time_stop("delete");

			return $results;
		}

		/**
		 * MySQL::alter()
		 *
		 * send alter query
		 *
		 * @param string $sql
		 * @return
		 */
		 //TODO: remove
		function alter($sql = "") {
			$this->time_start();

			if (empty ($sql)) {
				return false;
			}
			if (!eregi("^alter", $sql)) {
				$this->error("error, it's not a alter-query: ".$sql);
				return false;
			}
			if (empty ($this->CONN)) {
				return false;
			}
			$conn = $this->CONN;
			$results = mysqli_query($conn, $sql);
			if (!$results) {
				return false;
			}

			$this->time_stop("alter");
			return true;
		}

		/**
		 * MySQL::select2csv()
		 *
		 * sends an array as csv-file / with save/open dialog
		 * <br>(since 2004-11-01) In  $this->csv_filename, $this->csv_extension the filename and
		 * extension could be set.
		 *
		 * @since 2003-11-23
		 * @see array2csv
		 * @see data2file
		 * @param string $sql select query
		 * @param int $append2file if 2, append to existing file with seperator
		 * @return csv file / csv data
		 */
		function select2csv($sql = "", $append2file = "0") {
			$add_file = "cbadditionals.inc.php";

			require_once $add_file; // manualy load the extensions
			$result = $this->select($sql);
			$data = array2csv($result);

			if ($this->save2file != 0) {
				/* some temp calcs */
				$t = explode("FROM ", $sql);
				$t = explode(" ", $t[1]);
				$table = $t[0];
				$result = data2file($data, $this->csv_filename, $this->csv_extension, 2, $table); // write;
				if ($result == 0) {
					return $this->error("Could not write csv-data file to disc");
				}
			}

			switch ($this->prompt4file) {
				default :
				case 1 : // prompt for save/open
					data2file($data);
					return $data;
				case 0 : // do not prompt, return csv-string
					return $data;
			} // switch
		}

		/**
		 * MySQL::query()
		 *
		 * @since 2004-07-26
		 * @param string $ / array - $sql query
		 * @return false /true // resultarray
		 */
		function query($sql = "") {
				// is array? more than one query
			if (is_array($sql)) {
				// more than 1 Query
				while (list (, $val) = each($sql)) {
					$this->one_query($val);
				} // while
				return true;
			}
			return $this->one_query($sql);
		} //# end query()
		/**
		 * MySQL::one_query()
		 *
		 * sends any sql query to database and, if possible, returns the result
		 * it's dangerous to use this with user edited data (e.g. login forms)
		 *
		 * @access private
		 * @param string $sql
		 * @return
		 */
		function one_query($sql = "") {
			$this->time_start();
			$sql = trim($sql);
			if ($this->checkup("", $sql) != true) {
				$this->error("Queryfehler...");
			}
			$data = array ();
			$count = 0;
			$conn = $this->CONN;
			$result = mysqli_query($conn, $sql);
			$rows   = mysqli_affected_rows($conn);
			$this->affected_rows = $rows;
			if (!is_bool($result)) {
				while ($row = mysqli_fetch_array($result, $this->SELECT_TYPE)) {
					$data[$count ++] = $row;
				} //# while()
			} else {
				$data = $result;
			}

			$this->time_stop("query");

			if ($data == false) {
				$this->error($sql);
			}

			return $data;
		}

		// starts time m,
		function time_start() {
			if ($this->measure_time = 0) {
				return true;
			}

			$tmp = explode(" ", microtime());
			$this->temptime = $tmp[0] + $tmp[1];
			unset ($tmp); // Zeitmessung Start
		}

		// stops time measuring
		function time_stop($func) {
			//how often was the function called
			$this->commands[$func]++;
			$this->commands["sum"]++;

			// stop here
			if ($this->measure_time = 0) {
				return true;
			}

			$tmp = explode(" ", microtime());
			$microtime = $tmp[0] + $tmp[1] - $this->temptime;
			unset ($tmp); // Zeitmessung Ende
			$this->commands[$func."_time"] += $microtime;
			$this->commands["sum_time"] += $microtime;
		}



		// destructor
		/*
		 * @access public
		 * @return bool
		 */
		function close() {
			return mysqli_close($this->CONN);
		}
	} //# end class

	if (!function_exists("debug_print_backtrace2")) {
		// PHP4 workaround... not needed 4 PHP5
		function debug_print_backtrace2() {
			echo "<pre>";
			$ar = debug_backtrace();
			//array_pop($ar);
			var_dump($ar);
			echo "</pre>";
		}
	}

	define("CLASS_MYSQLI", true);
} // define

/* ************************************************************************* */

if (!defined('CLASS_MYSQL') && defined('LOAD_MYSQL')) {
	// PHP 4 - MySQL 4 Version of Class... //FIXME: bad style
	
	
class MySQL {
	VAR $VERSION = "3.4.0";

	VAR $SELECT_TYPE = MYSQL_BOTH;

	/**
	 * MySQL::measure_time - should the script measure sql times?
	 *
	 * int
	 *
	 * @since 2005-05-30
	 * @access public
	 */
	VAR $measure_time = 1; // 0 - none; 1 - milli secs

	// protected
	VAR $temptime	=0;//temp timememory

	/**
	 * MySQL::csv_filename - name without extension of future csv export file
	 *
	 * String
	 *
	 * @since 2004-11-01
	 * @access public
	 */
	VAR $csv_filename = "data";

	/**
	 * MySQL::csv_extension - extension of future csv export file
	 *
	 * String
	 *
	 * @since 2004-11-01
	 * @access public
	 */
	VAR $csv_extension = "csv";

	/**
	 * MySQL::save2file - should i try to save exported csv file?
	 *
	 * int
	 *
	 * @since 2003-11-25
	 * @access public
	 */
	VAR $save2file = 0;

	/**
	 * MySQL::prompt4file - prompt with "save file" dialog when exporting csv file (1-yes/0-no)
	 *
	 * (0) - select2csv only returns csv-string-data
	 * (1) - select2csv also return csv-string-data but add. header-info to get save file dialog in your browser
	 *

	 * @see testsuite_csv.php

int
	 * @since 2003-11-23
	 * @access public
	 */
	VAR $prompt4file = 1;

	/**
	 * MySQL::error_no - last Error No
	 *
	 * int
	 *
	 * @access public
	 */
	VAR $error_no;

	/**
	 * MySQL::error_msg - last Error Msg
	 *
	 * string
	 *
	 * @access public
	 */
	VAR $error_msg;

	/**
	 * MySQL::error_cmsg - last Custom Error Msg
	 *
	 * string
	 *
	 * @access public
	 */
	VAR $error_cmsg="";

	/**
	 * MySQL::fatalerros - list of errornumbers, we should stop, regardless of which 
	 * errorlevel is defined
	 *
	 * string
	 *
	 * @access public
	 */
	VAR $fatalerrors = array(//1045, // Access denied for user
						);
	
	
	/**
	 * MySQL::akt_DBASE - actuall database
	 *
	 * @access public
	 */
	VAR $akt_DBASE;

	/**
	 * MySQL::commands	array	counts all method-calls in an associative array
	 *
	 * @access private
	 */
	// VAR $commands	=	array();
	VAR $commands = array( "alter"=>0, "select"=>0, "insert"=>0, "delete"=>0, "update"=>0, "query"=>0, "sum"=>0,
						   "alter_time"=>0, "select_time"=>0, "insert_time"=>0, "delete_time"=>0, "update_time"=>0, "query_time"=>0, "sum_time"=>0 );

	/**
	 * MySQL::$CONN	connection handle
	 *
	 * @access private
	 */
	VAR $CONN;

	/**
	 * MySQL::DBG	debuglevel, set by config file
	 *
	 * @access private
	 */
	VAR $DBG = 0; // if you have some startup errors, it's fine to see them.
	// 0-liefert "nur" false zur�ck; Fehlerbehandlung in der aufrufenden Funktion
	// 1-zeigt Fehler per echo an, liefert false, l��t das Script jedoch weiterlaufen
	// 2-"stirbt" mir Fehlermeldung ... die("Fehler)
	// 3-bricht ab ohne Fehleraus- oder R�ckgabe (stiller Tod)
	/**
	 * MySQL::last_DBASE	database used bevore actual
	 *
	 * @access private
	 */
	VAR $last_DBASE;

	/**
	 * MySQL::field_dimension dimension of the returning array from select
	 *
	 * @access private
	 */
	VAR $field_dimension = 2; 
	// 2 - always two dimensions
	// 1 - automatic one/two-dim
	
	/**
	 * MySQL::TRY_persistent try to connect with persistent connection
	 *
	 * @access private
	 */
	VAR $TRY_persistent = true; // Soll versucht werden persistente Verbindungen aufzubauen?
	// Bei Transaktionen oder Locking nicht sinnvoll!

	VAR $STATUS_persistent = false;	// Status, ob eine persistente Verbindung aufgebaut wurde oder oder nicht

	/**
	 * MySql::error() - Error Handler
	 *
	 * @access protected
	 * @param string $text Custom Error Text
	 * @return Boolean false (always)
	 */
	function error( $text = "-" )
	{
		$this->error_no = mysql_errno() != 0 ? mysql_errno() : $this->error_no ;
		$this->error_msg = mysql_error()!= "" ? mysql_error() : $this->error_msg ;
		$this->error_cmsg = $text;
		
		$msg = "<pre style=\"color:red;background-color:white;font-size:12px;\"><b>Custom Error Message: $text ";
		if ($this->error_no>0) $msg .= "\nMySQL ErrorNo: " . $this->error_no . "\r\nMySQL ErrorMsg: " . $this->error_msg;
		$msg .= "\r\n </b></pre>\r\n ";
	
		// override MAIN debug config
		// halt on connection errors
		if (in_array($this->error_no, $this->fatalerrors )) {
		    $this->DBG = 2;
			$msg = 	"<div style=\"font-size:20px;color:red;backgorund-color:white;\"><strong>FATAL!</strong><br> "
					. $msg . "</div>";
			
		}

		switch ( $this->DBG ) {
			case 0:
				// errorlog?
				//f_write("tmp/mysql/mysql_error_".$_SERVER["REMOTE_ADDR"]."_".microtime().".txt",$text."\r\n".$this->error_no."\r\n".$this->error_msg);
				return false; // do not need break, because of return
			case 1:
				echo $msg;
				return false;
			default:
			case 2:
				debug_print_backtrace2();
				die ( $msg );
				return false;
			case 3: die();
		} // switch
		return false; // no path will reach this
	}

	/**
	 * MySQL::MySQL() / MySQL::init()
	 *
	 * Contructor
	 *
	 * @param string $SERVER servername (e.g. localhost) OR array containing ['user|pass|dbase|server']
	 * @param string $USER username
	 * @param string $PASS password
	 * @param string $DBASE database
	 * @param string $CONFIGFILE configfile.. since 3.1.0 no longer needed
	 * @return bool
	 */

	// real?! constructor
	function init ( $SERVER , $USER = "", $PASS = "", $DBASE = "" , $DBG = 1)
	{
		return $this->MySQL ( $SERVER , $USER , $PASS , $DBASE , $DBG );
	}

	// old constructor
	function MySQL( $SERVER , $USER = "", $PASS = "", $DBASE = "", $DBG = 1 )
	{   
		// check if configarray is given or not
		if (is_array($SERVER)) {
			if (!array_key_exists("server", $SERVER)) {
			    $this->error( "given config array does not contain \"server\"!");
				return false;
			}
			if (!array_key_exists("user", $SERVER)) {
			    $this->error( "given config array does not contain \"user\"!");
				return false;
			}
			if (!array_key_exists("pass", $SERVER)) {
			    $this->error( "given config array does not contain \"pass\"!");
				return false;
			}
			if (!array_key_exists("dbase", $SERVER)) {
			    $this->error( "given config array does not contain \"dbase\"!");
				return false;
			}
			if (array_key_exists("dbg", $SERVER)) {
			    $this->set_DBG($SERVER['dbg']);
			}
			   
			$USER = $SERVER['user'];
			$PASS = $SERVER['pass'];
			$DBASE= $SERVER['dbase'];
			$SERVER=$SERVER['server'];

			
		}

		// set mysql_fetch_array to associative arrays, look at php manual "mysql_fetch_array()"
		$this->set_select_type( MYSQL_BOTH );
		// lookup for persistent connections
		if ( $this->TRY_persistent == true && ini_get( "mysql.allow_persistent" ) == true ) {
			$conn = mysql_pconnect( $SERVER, $USER, $PASS );
			$this->STATUS_persistent = true; // persistente Verbindung wurde aufgebaut
		} else {
			$conn = mysql_connect( $SERVER, $USER, $PASS );
			$this->STATUS_persistent = false;	// es wurde keine persistente Verbindung aufgebaut
		}

		if ( !$conn ) {
			return $this->error( "no connection!" );
		}
		// new in 3.0.0pre2
		// if no database, connection is regardless bounded to $this->CONN.
		// That means, theres no database, but a connection. Now you have the chance to create one
		if ( !@mysql_select_db( $DBASE, $conn ) ) {
			$this->CONN = $conn;
			//return $this->error( "no database!" );
		}
		$this->akt_DBASE = $DBASE;
		$this->CONN = $conn;
		return true;
	}

		/**
		 * MySQL::set_DBG() - sets Debug level
		 * 
		 * <br> if you have some startup errors, it's fine to see them.
		 * <br>0-liefert "nur" false zur�ck; Fehlerbehandlung in der aufrufenden Funktion
		 * <br>1-zeigt Fehler per echo an, liefert false, l��t das Script jedoch weiterlaufen
		 * <br>2-"stirbt" mir Fehlermeldung ... die("Fehler)
		 * <br>3-bricht ab ohne Fehleraus- oder R�ckgabe (stiller Tod)<br>
		 *
		 * @param int $newdbg - 0->4 
		 * @return boolean
		 * @access public
		 * @since 2005-10-06
		 */
		function set_DBG($newdbg) {
			if ($newdbg >= 0 and $newdbg <= 4) {
				$this->DBG = $newdbg;
				return true;
			} else {
				return false;
			}

		}

		/**
		 * MySQL::get_DBG() - sets Debug level
		 * 
		 * <br> if you have some startup errors, it's fine to see them.
		 * <br>0-liefert "nur" false zur�ck; Fehlerbehandlung in der aufrufenden Funktion
		 * <br>1-zeigt Fehler per echo an, liefert false, l��t das Script jedoch weiterlaufen
		 * <br>2-"stirbt" mir Fehlermeldung ... die("Fehler)
		 * <br>3-bricht ab ohne Fehleraus- oder R�ckgabe (stiller Tod)<br>
		 *
		 * 
		 * @return int 
		 * @access public
		 * @since 2005-10-06
		 */
		function get_DBG() {
			return	$this->DBG;
		}
		
	/**
	 * MySQL::checkup() - checks sql-query is not empty, connection is still alive and query is right
	 *
	 * @param string $method method (insert, select etc)
	 * @param string $sql sql query
	 * @return boolean
	 * @access protected
	 * @since 2003-11-25
	 */
	function checkup( $method="", &$sql )
	{
		$sql = trim ( $sql );
		$method = trim ( $method );
		
		if ( empty( $sql ) ) {
			$temp = $method=="" ? "Name" : "Query";
			return $this->error( $temp . " is empty: $sql " );
		}
		if ( $method != "" && !eregi( "^" . $method, $sql ) ) {
			return $this->error( "Error, its not a " . $method . "-query: $sql " );
		}
		if ( empty( $this->CONN ) ) {
			return $this->error( "No connection or connection lost" );
		}
		if ( $method != "" && !eregi("^CREATE DATABASE", $sql) && $this->akt_DBASE=="") {
			return $this->error( "No database! Create database first, then change actiove DB!" );
		}
		
		return true;
	}

	/**
	 * MySql::new_db()
	 *
	 * creates new database
	 *
	 * @param string $name Datenbankname
	 * @return bool
	 */
	function new_db( $name )
	{
	    $sql = "CREATE DATABASE " . $name;
		if ( $this->checkup( "", $sql ) != true ) {
			return $this->error( "an error occured!" );
		}
		if ( mysql_query ( $sql , $this->CONN ) ) {
			return true;
		} else {
			return $this->error ( "error while creating database '$name'!" );
		}
	}

	/**
	 * MySql::change_db()
	 *
	 * changes active database
	 *
	 * @param string $name database
	 * @return bool
	 */
	function change_db( $name )
	{
		$name = trim ( $name );
		
		if ( empty( $name ) ) {
			return $this->error( "DB-Name is empty: $name " );
		}
		if ( empty( $this->CONN ) ) {
			return $this->error( "No connection or connection lost" );
		}
		
		if ( mysql_select_db( $name ) ) {
			$this->last_DBASE	= $this->akt_DBASE;
			$this->akt_DBASE	= $name;
			return true;
		} else {
			return $this->error ( "error changing database!" );
		}
	}

	/**
	 * MySql::drop_db()
	 *
	 * drops a db
	 *
	 * @param string $name database
	 * @return bool
	 */
	function drop_db( $name )
	{
		if ( $this->checkup( "", $name ) != true ) {
			return $this->error( "no database name given" );
		}
		
		if ( !mysql_query( "DROP DATABASE " . $name, $this->CONN ) ) {
			return $this->error ( "could not drop database!" );
		}
		// if db dropped, change to last database, if exists
		if ( $name == $this->akt_DBASE && $this->last_DBASE != "" ) {
			$this->change_db( $this->last_DBASE );
		}
		return true;
	}

	/**
	 * MySql::create()
	 *
	 * sends every query to db whitsch begins with "create" - it is not case sensitive
	 *
	 * @param string $sql SQL-create-Query
	 * @return bool
	 */
	function create( $sql )
	{
		$result = $this->checkup( "create", $sql );
		if ($result==false) {
		    return $this->error( "creation fails." );
		}
		if ( !mysql_query( $sql ) ) {
			$this->error( "could not create anything [ " . $sql . " ]" );
			return false;
		}
		return true;
	}

	/**
	 * MySql::empty_table()
	 *
	 * truncates a table
	 *
	 * @param string $name tablename
	 * @return bool
	 */
	function empty_table( $name )
	{
		$result = $this->checkup( "", $name );
		if ($result==false) {
			return $this->error( "could not truncate." );
		}
		
		if ( mysql_query ( "TRUNCATE TABLE " . $name, $this->CONN ) ) {
			return true;
		} else {
			$this->error ( "error truncating table '$name' !" );
			return false;
		}
	}

	/**
	 * MySql::optimize()
	 *
	 * optimizes table
	 *
	 * @param string $name tablename
	 * @return bool
	 */
	function optimize( $name )
	{
		$result = $this->checkup( "", $name );
		if ($result==false) {
			return $this->error( "could not optimize." );
		}
		
		if ( mysql_query ( "OPTIMIZE TABLE " . $name, $this->CONN ) ) {
			return true;
		} else {
			$this->error ( "error optimizing table '$name' !" );
			return false;
		}
	}

	/**
	 * MySql::drop_table()
	 *
	 * drops a table
	 *
	 * @param string $name table
	 * @return bool
	 */
	function drop_table( $name )
	{
		$name = trim( $name );
		if ( empty( $name ) ) {
			return false;
		}
		if ( empty( $this->CONN ) ) {
			return false;
		}
		if ( mysql_query ( "DROP TABLE IF EXISTS " . $name, $this->CONN ) ) {
			return true;
		} else {
			$this->error ( "error dropping table!" );
			return false;
		}
	}

	/**
	 * MySQL::set_select_type()
	 *
	 * sets the resulttype of mysql_fetch_array used in sql->select()
	 *
	 * @param  $type one out of "MYSQL_ASSOC", "MYSQL_NUM", "MYSQL_BOTH"
	 * @return
	 */
	function set_select_type( $type )
	{
		$allowed = array( MYSQL_NUM, MYSQL_BOTH, MYSQL_ASSOC );
		if ( in_array( $type, $allowed ) ) {
			$this->SELECT_TYPE = $type;
		}
		return $this->SELECT_TYPE;
	}
	//	select field return
		// 2 - always two dimensions
		// 1 - automatic one/two-dim
		function set_field_dimension($type = "0") {
			$allowed = array (1, 2);
			if (in_array($type, $allowed)) {
				$this->field_dimension = $type;
			}
			return $this->field_dimension;
		}
		function get_field_dimension() {
			return $this->field_dimension;
		}
	/**
	 * MySQL::select()
	 *
	 * queries db with select and returns an array with one or two dimension, dependion on entities
	 * if a entity is named INDEX_ASSOCIATION you'll have an associative array with this entity als key
	 * SELECT id AS INDEX ASSOCIATION, name, land FROM xy ...
	 * the array will be $result[id-entry][name|land] = value...
	 * id-entry is in ( id1, id2 ...)
	 * <br>
	 * 07-26-2004 3.1.0<br>
	 *     + via MySQL->fiel_dimension, you could say, you want optional 1-d arrays or not
	 * <br>
	 * 01-30-2004 3.0.0pre3			<br>
	 *     + choose via constant SELECT_TYPE if you want to have NUM, ASSOC or BOTH Types in ARRAY
	 *       look at set_select_type()
	 * <br>
	 * 09-28-2004 3.1.0 <br>
	 *     + 2nd argument = True says, that output shout be smarty array
	 *
	 * @param string $sql
	 * @param int $csvdata
	 * @return array
	 */
	function select( $sql = "", $smarty = false )
	{
		#echo "<br>".$sql."<br>";
	    $result=0;
		$this->time_start();
		$this->checkup( "select", $sql ) or $this->error( "error queriing, its not a select-query: " . $sql );
		$this->affected_rows = 0;
		
		// if there is an SHOW query, there is MYSQL_BOTH needed as mysql_fetch_array result type
		if ( eregi( "^show", $sql ) ) {
			$temp_type = $this->SELECT_TYPE;
			$this->SELECT_TYPE = MYSQL_BOTH;
		}
		$conn = $this->CONN;
		$results = mysql_query( $sql, $conn ) or $this->error( $sql );
		if ( ( !$results ) or ( empty( $results ) ) ) {
			@mysql_free_result( $results );
			return false;
		}

		$count = 0;
		$data = array();
		
		$this->affected_rows = mysql_affected_rows ( $conn );
		
		// create a smarty readable array
		if ( $smarty == true ) {
			while ( $row = mysql_fetch_array( $results, $this->SELECT_TYPE ) ) {
				// if there's only one column and the script is allow to create,
				// create a 1-d array
				array_push( $data, $row );
			}
			// normal int associated array
		} elseif ( !ereg( "INDEX_ASSOCIATION", $sql ) ) {
			// normal results return (1-d, 2-d arrays)
			$anz_rows = mysql_num_fields( $results );
			while ( $row = mysql_fetch_array( $results, $this->SELECT_TYPE ) ) {
				// if there's only one column and the script is allowed to create,
				// create a 1-d array
				if ( $anz_rows < 2 && $this->field_dimension == 1 ) {
					$data[$count] = $row[0];
				} else {
					$data[$count] = $row;
				}
				$count++;
			}
		} else {
			// build index-assozitive 1-d/2-d array
			// entity named INDEX_ASSOCIATION would be the array-key
			while ( $row = @mysql_fetch_array( $results ) ) {
				$count = $row['INDEX_ASSOCIATION'];
				// if theres only one entity left, build a 1-d array
				if ( count( $row ) > 4 ) {
					// remove ASSOCIATIONs, not needed
					$data[$count] = $row;
				} else {
					if ( $row[1] == $row['INDEX_ASSOCIATION'] ) {
						$data[$count] = $row[0];
					} else {
						$data[$count] = $row[1];
					}
				}
			}
		}
		if ( eregi( "^show", $sql ) ) {
			$this->SELECT_TYPE = $temp_type;
		}

		
		@mysql_free_result( $results );
		$this->commands["select"]++; $this->commands["sum"]++;

		$this->time_stop("select");

		return $data;
	}

	/**
	 * MySQL::get_affected_rows()
	 *
	 * returns number of rows that were affected by last query
	 *
	 * 
	 * @return int affected rows
	 */
	 // FIXME: seams to have a bug; does not return right value
	function get_affected_rows(){
		return $this->affected_rows;
	}
	
	/**
	 * MySQL::insert()
	 *
	 * inserts a "insert"-query
	 *
	 * @param string $sql
	 * @return int inserted id | affected rows
	 */

	function insert( $sql = "" )
	{
		$this->time_start();

		if ( empty( $sql ) ) {
			return false;
		}
		if ( !eregi( "^insert", $sql ) ) {
			return $this->error( "error, its not a insert-query: " . $sql );
		}
		if ( empty( $this->CONN ) ) {
			return false;
		}
		$conn = $this->CONN;
		$results = mysql_query( $sql, $conn ) or $this->error( $sql );
		$results = mysql_insert_id( $conn );
		$results == 0 ? $results = mysql_affected_rows( $conn ): 1;

		$this->time_stop("insert");
		return $results;
	}

	/**
	 * MySQL::update()
	 *
	 * updates db
	 *
	 * @param string $sql
	 * @return boolean true if result | false if no result
	 */
	function update ( $sql = "" )
	{
		$this->time_start();
		if ( empty( $sql ) ) {
			return false;
		}
		if ( !eregi( "^update", $sql ) ) {
			$this->error( "error, its not a update-query: " . $sql );
			return false;
		}
		if ( empty( $this->CONN ) ) {
			return false;
		}
		$conn = $this->CONN;

		// cdhack for errors
		$results = mysql_query( $sql, $conn );//old: mysql_query( $sql, $conn );
		if ($results==false) {
			$this->error( "could not exec update(): " . $sql );
			return false;
		}
		$results = mysql_affected_rows( $conn );
		$this->affected_rows = $results;
		if ($results==0) $results=true;
		$this->time_stop("update");
		return $results;
	}

	/**
	 * MySQL::delete()
	 *
	 * send delete query
	 *
	 * @param string $sql
	 * @return int affected rows (since 3.2.1)
	 */
	function delete( $sql = "" )
	{
		$this->time_start();

		if ( $this->checkup( "delete", $sql ) != true ) {
			return $this->error( "no delete query: " . $sql);
		}
		
        $conn = $this->CONN;
		$results = mysql_query( $sql, $conn );
		$results = mysql_affected_rows( $conn );

		$this->time_stop("delete");

		return $results;
	}

	/**
	 * MySQL::alter()
	 *
	 * send alter query
	 *
	 * @param string $sql
	 * @return
	 */
	function alter ( $sql = "" )
	{
		$this->time_start();

		if ( empty( $sql ) ) {
			return false;
		}
		if ( !eregi( "^alter", $sql ) ) {
			$this->error( "error, it's not a alter-query: " . $sql );
			return false;
		}
		if ( empty( $this->CONN ) ) {
			return false;
		}
		$conn = $this->CONN;
		$results = mysql_query( $sql, $conn );
		if ( !$results ) {
			return false;
		}

		$this->time_stop("alter");
		return true;
	}

	/**
	 * MySQL::select2csv()
	 *
	 * sends an array as csv-file / with save/open dialog
	 * <br>(since 2004-11-01) In  $this->csv_filename, $this->csv_extension the filename and
	 * extension could be set.
	 *
	 * @since 2003-11-23
	 * @see array2csv
	 * @see data2file
	 * @param string $sql select query
	 * @param int $append2file if 2, append to existing file with seperator
	 * @return csv file / csv data
	 */
	function select2csv( $sql = "" , $append2file = "0" )
	{
		$add_file = "cbadditionals.inc.php";

		require_once $add_file; // manualy load the extensions
		$result	= $this->select( $sql );
		$data	= array2csv( $result );

		if ( $this->save2file != 0 ) {
			/* some temp calcs */$t = explode( "FROM ", $sql );
			$t = explode( " ", $t[1] );
			$table = $t[0];
			$result = data2file( $data, $this->csv_filename, $this->csv_extension, 2, $table ); // write;
			if ( $result == 0 ) {
				return $this->error( "Could not write csv-data file to disc" );
			}
		}

		switch ( $this->prompt4file ) {
			default:
			case 1: // prompt for save/open
				data2file( $data );
				return $data;
			case 0: // do not prompt, return csv-string
				return $data;
		} // switch
	}

	/**
	 * MySQL::query()
	 *
	 * @since 2004-07-26
	 * @param string $ / array - $sql query
	 * @return false /true // resultarray
	 */
	function query( $sql = "" )
	{
		// is array? more than one query
		if ( is_array( $sql ) ) {
			// more than 1 Query
			while ( list( , $val ) = each( $sql ) ) {
				$this->one_query( $val );
			} // while
			return true;
		}
		return $this->one_query( $sql );
	} //# end query()
	/**
	 * MySQL::one_query()
	 *
	 * sends any sql query to database and, if possible, returns the result
	 * it's dangerous to use this with user edited data (e.g. login forms)
	 *
	 * @access private
	 * @param string $sql
	 * @return
	 */
	function one_query( $sql = "" )
	{
		$this->time_start();
		$sql = trim( $sql );
		if ( $this->checkup( "", $sql ) != true ) {
			return $this->error( "Queryfehler..." );
		}
		$data = array();
		$count = 0;
		$conn = $this->CONN;
		$result = mysql_query( $sql, $conn );
		if ( !eregi( "create|load|alter|outfile", $sql ) ) {
			while ( $row = @mysql_fetch_array( $result ) ) {
				$data[$count++] = $row;
			} //# while()
		} else {
			$data = $result;
		}
		@mysql_free_result( $result );

		$this->time_stop("query");

		if($data==false) {
			return $this->error($sql);
		}

		return $data;
	}

	// starts time m,
	function time_start()	 {
		if ($this->measure_time=0) { return true; }

	 	$tmp=explode(" ",microtime());
		$this->temptime=$tmp[0]+$tmp[1];
		unset($tmp);	// Zeitmessung Start
	}

	// stops time measuring
	function time_stop($func) {
		//how often was the function called
		$this->commands[$func]++;
		$this->commands["sum"]++;

		// stop here
		if ($this->measure_time=0) { return true; }

		$tmp		=	explode(" ",microtime());
		$microtime	=	$tmp[0]+$tmp[1]-$this->temptime;
		unset($tmp);				// Zeitmessung Ende
		$this->commands[$func."_time"]	+= $microtime;
		$this->commands["sum_time"]		+= $microtime;
	}

	// destructor
	/*
	 * @access public
	 * @return bool
	 */
	function close()
	{
		return mysql_close($this->CONN);
	}
} //# end class
	
	
	
	
	if (!function_exists("debug_print_backtrace2")) {
		// PHP4 workaround... not needed 4 PHP5
		function debug_print_backtrace2() {
			echo "<pre>";
			$ar = debug_backtrace();
			//array_pop($ar);
			var_dump($ar);
			echo "</pre>";
		}
	}

	define("CLASS_MYSQL", true);
} // define
?>