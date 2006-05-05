<?php
/**
* From toolbox: cbdebug.class.php 19 2005-12-01 15:58:32Z tuergeist
* 
* Debugger - php class to survilance a php script
*
* @version 0.3.1
* cahnges ro 0.3.1
*   # if enter_method gets a method name, this name is used (instead of auto-
*     ;)
* detected)
* changes to 0.3.0
*   + auto-determine caller function via debug_trace
* changes to 0.2.5 
* 	+ test if logfile is writeable
*   + contructor is now Debug(), init() is still ok
*   # commends fixed
*   # interfaces debugged (sometimes wrong behavior to objects)
* changes to 0.2.4 2005-06-27
*   + $onefile4oneip - you could choose to have a logfile for each ip or call
* changes to 0.2.3 2005-05-20
*   # better error msgs
* changes to 0.2.2 2004-02-01
* 	+ ::quit() could output a html link to logfile
* 		set via ::filelink=1
*
* Copyright (C) 2003 Christoph Becker <cbecker@nachtwach.de>
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your
* option) any later version.
*
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
* more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
*
* --- Deutsch ---
*
* Copyright (C) 2003 Christoph Becker <cbecker@nachtwach.de>
*
* Dieses Programm ist freie Software. Sie kï¿½nnen es unter den Bedingungen
* der GNU General Public License, wie von der Free Software Foundation
* verï¿½ffentlicht, weitergeben und/oder modifizieren, entweder gemï¿½ï¿½
* Version 2 der Lizenz oder (nach Ihrer Option) jeder spï¿½teren Version.
*
* Die Verï¿½ffentlichung dieses Programms erfolgt in der Hoffnung, daï¿½ es
* Ihnen von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne
* die implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT Fï¿½R EINEN
* BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.
*
* Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
* Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
*
*/


/**
 * Debug
 *
 *
 * @author cbecker@nachtwach.de
 * @copyright Copyright (c) 2003-11-17
 * @version 0.3.0
 * @access public
 **/
class Debug
{
	// public
	/**
	* Debug::debugger	set debugger default on(1)/off(0)
	*
	* boolean
	*
	* @access public
	**/
	var $debugger;

	/**
	* Debug::show_sqls	show sql queries in logfile (1) or not (0)
	*
	* boolean
	*
	* @access public
	**/
	var $show_sqls		=	1;
	
	/**
	* Debug::show_args	show arguments, if existing, when a method is entered
	*
	* boolean
	*
	* @access public
	**/
	var $show_args		=	1;

	/**
	* Debug::linefeed	linefeed before method_start entry in logfile? (and after method)
	*
	* boolean
	*
	* @access public
	**/
	var $linefeed		=	0;

	/**
	* Debug::fileline	should the debugger echo a link to logfile at the end of debugging
	*
	* link html
	*
	* @access public
	**/
	var $filelink		=	0;
	
	/**
	* Debug::prespace	how much spaces to use instead of tab
	*
	* integer
	*
	* @access public
	**/
	var	$prespace		=	4;

	/**
	* Debug::file_prefix	prefix of logfiles including path
	*
	* string
	*
	* @access public
	**/
	var $file_prefix	=	"logs/";

	/**
	* Debug::hide_fkt		array with methods to hide
	*
	* integer
	*
	* @access public
	**/
	var $hide_fkt		=	array();			// Array mit Funktionen, deren Ausgabe unterdrï¿½ckt werden soll

	// private
	/**
	* Debug::VERSION	class version
	*
	* string
	*
	* @access private
	**/
	var $VERSION		=	"0.3.1";

	/**
	* Debug::functions	method stack
	*
	* array
	*
	* @access private
	**/
	var $funktions		=	array(0=>"");

	/**
	* Debug::filehandle	logfile handle
	*
	* filehandle
	*
	* @access private
	**/
	var $filehandle;
	
	/**
	* Debug::time		microtime for runtime meassurement
	*
	* integer
	*
	* @access private
	**/
	var	$time;

	/**
	* Debug::deep		remember recursion deep
	*
	* integer
	*
	* @access protected
	**/
	var $deep;
	
	/**
	* Debug::filename		filename
	*
	* string
	*
	* @access private
	**/
	var $filename;
	
	/**
	* Debug::onefile4oneip		should i create one log for each ip (true) or for each call (false)
	*
	* boolean
	*
	* @access public
	**/
	var $onefile4oneip; // false means old style!
	
	var $blind=0; // Zähler
	
	function init() {
		return $this->Debug();
	}
	/**
	 * Debug::__contructor()
	 *
	 * Constructor
	 *
	 * @return boolean	true
	 **/
	function Debug($debug=true, $one4one=false)
	{
		$this->debugger = $debug;
		$this->onefile4oneip = $one4one;

		if($this->debugger==true)
		{
			
			if (!file_exists($this->file_prefix)) {
			    if (!file_exists("../".$this->file_prefix)) {
			    	die("debug-class: logfile dir error, exists it? -> ".$this->file_prefix);
			    }	else {
					$this->prefix = "../".$this->file_prefix;
				}
				die("debug-class: logfile dir error, exists it? -> ".$this->file_prefix);
			}

			$temp		=	explode(" ",microtime());
			$microtime	=	$temp[0]+$temp[1];
			
			// simple, append?   or a new file for each call?
			if ($this->onefile4oneip==true){
				$ip			=	str_replace(".","_",$_SERVER['REMOTE_ADDR']);
				$this->filename =	$this->file_prefix.strftime("%y%m%d") . "__". $ip .".txt";
				$mode			=	"a"; // access mode
			}    else {
			    $suffix			=	substr(uniqid(rand()),0,6);
				$suffix			=	str_replace(".","_",$_SERVER['REMOTE_ADDR'])."__".$suffix;
				$this->filename	=	$this->file_prefix.strftime("%y%m%d_%H%M%S")."__".$suffix.".txt";
				$mode			=	"w";
			}
			if (file_exists($this->filename) && !is_writable($this->filename)) die ("debug logfile ".$this->filename." is not writable (chmod to 777)");
			
			$filehandle		=	fopen($this->filename,$mode) or die("debug file init error");
			
			fwrite($filehandle,"start debugmode (".strftime("%H:%M:%S %d.%m.%Y").")\n");
			fwrite($filehandle,"caller IP: ".		$_SERVER['REMOTE_ADDR']."\n");
			fwrite($filehandle,"browser  : ".		$_SERVER["HTTP_USER_AGENT"]."\n");
			@fwrite($filehandle,"referer  : ".		$_SERVER["HTTP_REFERER"]."\n\n");
			$this->filehandle	=	$filehandle;
			$this->time			=	$microtime;
		}
		return true;
	}


	/**
	 * Debug::hide($methodname)
	 *
	 * hides all debug calles from mehtods in this array
	 *
	 * @access public
	 * @param $fkt	string	methodname
	 * @return
	 **/
	function hide($fkt)
	{
		array_push($this->hide_fkt, $fkt);
	}

	/**
	 * Debug::write()	Renders the text into the log
	 *
	 * @access private
 	 * @param $text
	 * @return
	 **/
	function write($text)
	{
		if($this->debugger==1 && !(in_array($this->funktions[0], $this->hide_fkt)) )
		{
			fwrite($this->filehandle, str_repeat(" ", $this->deep*$this->prespace).$text."\n");
		}
	}

	/**
	 * Debug::RenderVar()	Renders the var and return
	 *
	 * @access private
 	 * @param $var
	 * @return string	information about var
	 **/
	function RenderVar($var)
	{
		switch(gettype($var)){
			case "boolean":
				$text = ($var?"TRUE":"FALSE");
				$text.= " {BOOLEAN}";
				break;
			case "integer":
			case "double":
				$text = $var;
				break;
			case "string":
				$text = "\"" . htmlentities($var) . "\" {STRING}";
				break;
			case "array":
				$this->deep++;
				$text = $this->WriteArray($var);
				$this->deep--;
				break;
			case "object":
				// $this->WriteEOL();
				// $this->SendObject($var, "");
				$text = "got Object...";
				break;
			case "NULL":
				$text = "NULL";
				break;
			default:
				$text = "[Unknown type]";
		}
		return $text;
	}

	/**
	 * Debug::writearray()	Renders an array for rendervar
	 *
	 * @access private
 	 * @param $array	array to render
	 * @return
	 **/
	function WriteArray($array){
		$text = "\n".str_repeat(" ", ($this->deep)*$this->prespace)."{";
		if(count($array) > 0){
			#$text .= "\n";
		}
		while(list($key, $value) = each($array)){
			$text .= "\n".str_repeat(" ", ($this->deep+1)*$this->prespace);
			$text .= $this->RenderVar($key);
			$text .= " => ";
			$text .= $this->RenderVar($value);
		}
		$text.="\n".str_repeat(" ", ($this->deep)*$this->prespace);
		if(count($array) > 0){
			$text .= "}";
		}else{
			$text .= "}";
		}
		return "{ARRAY} ".$text;
	}


	/**
	 * Debug::enter_method()	Set method-start mark into logfile
	 *
	 * @access public
	 * @param $method	method-name
	 * @return
	 **/
	function enter_method($method="")
	{
		// caller method detection
		if ($method==NULL or $method=="empty" or $method=="") {
			$debug	=	debug_backtrace();
			$method	=	$debug[1]['function'];
		}
		if (array_key_exists("class", $debug[1])) {
			$class	=	$debug[1]['class'] . ":";
		} else {$class  =	"";} 
		
		// TEST
		// Wenn wir keine der zu versteckenden Funktion sind, mache...
		if(!(in_array($this->funktions[0], $this->hide_fkt)))
		{
			if($this->linefeed==1) $this->write("\n");
			if(in_array($method, $this->hide_fkt))	{ $hint = "\n".str_repeat(" ", ($this->deep+1)*$this->prespace)."{Ausgabe unterdrueckt}";}
			$this->write("--> ".$class.$method); #.$hint // ?! keine Ahnung was das sollte! cb
			array_unshift($this->funktions, $class.$method );	// vorne rein
			$this->deep++;
			// arguments
			if ($this->show_args==true && array_key_exists("args", $debug[1])) {
					if (!empty($debug[1]['args'])) {	
						$this->write("{ARGUMENTS} = ".$this->RenderVar($debug[1]['args'])); 
					}
			}
			
		} else { $this->blind++; }

	}

	/**
	 * Debug::leave_method()	sets method-leave mark into logfile
	 *
	 * @access public
	 * @return
	 **/
	function leave_method($return="")
	{
		// Wenn wir keine der zu versteckenden Funktion sind, mache...
		if(!(in_array($this->funktions[0], $this->hide_fkt)) OR $this->blind==0)
		{
			$this->deep--;
			$method = array_shift($this->funktions);	// vorne raus
			$filestring	=	"<-- ".$method;
			if ($return!="") {
			    $filestring .= ", returns: ".$this->RenderVar($return);
			}
			$this->write($filestring);
			if($this->linefeed==1) $this->write("\n");
		} else  { $this->blind--;}
	}


	/**
	 * Debug::send_message()	writes a message into logfile
	 *
	 * @access public
	 * @param $text	mesage
	 * @return
	 **/
	function send_message($text)
	{
		$this->write("{MSG} ".$text);
	}


	/**
	 * Debug::watch_var() writes a var and its value into logfile
	 *
	 * @access public
	 * @param $name	name of var
	 * @param $wert	value of var
	 * @return
	 **/
	function watch_var($name, $wert)
	{ 
		$this->write("{VAR} ".$name." = ".$this->RenderVar($wert));
	}
	// hint: there is no possibility i think to transmit the varname alone.. if you find a way, write me


	/**
	 * Debug::sql()	writes a Query into log, if not set off by config
	 *
	 * @access public
	 * @param $wert	query
	 * @return
	 **/
	function sql($wert)
	{
		if($this->show_sqls==1) $this->write("{SQL} ".$wert);
	}


	/**
	 * Debug::quit()	ends debug mode and closes logfile
	 *
	 * @access public
	 * @return
	 **/
	function quit()
	{
		if($this->debugger==1)
		{
			$microtime	=	$this->time;
			$temp		=	explode(" ",microtime());
			$microtime	=	$temp[0]+$temp[1]-$microtime;

			$this->write("\nruntime:  ".number_format(abs($microtime),3,",",".")." sec");
			$this->write("end debug (".strftime("%H:%M:%S %d.%m.%Y").")"
						."\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -");
			if ($this->filelink==1) {
			    echo "<p style=\"text-align:center;\"><a style=\"font-family:arial;font-size:9px;color:green;\" href=\"".$this->filename."\">Debug-Logfile</a></p>";
			}
			fclose($this->filehandle);
		}
	}


	/**
	 * Debug::checkpoint()	sets checkpoint, call $DBC->checkpoint(__LINE__,__FILE__);
	 *
	 * @access public
	 * @param $zeile	line of code
	 * @param $datei	file
	 * @return
	 **/
	function checkpoint($zeile, $datei)
	{
		$this->write("{CHP} Zeile: ".sprintf("%04d",$zeile)." - Datei: ".$datei);
	}

}// end

class errorlog {
	var $_filename;

	function errorlog($dir="logs/",$errfile="") {
		$this->_filename = $dir."error_".$errfile.".log";	
	}
	function entry($text){
		
		$fh = fopen($this->_filename,"a")or die("errorlog file init error. can't write to '".$this->_filename."'");
		fwrite($fh,strftime("%H:%M:%S %d.%m.%Y")."\n");
		fwrite($fh," caller IP: ".		$_SERVER['REMOTE_ADDR']."\n");
		fwrite($fh," browser  : ".		$_SERVER["HTTP_USER_AGENT"]."\n");
		@fwrite($fh," referer  : ".		$_SERVER["HTTP_REFERER"]."\n\n");
		fwrite($fh," ".$text."\n");
		fwrite($fh," Session data:".var_export($_SESSION,true)."\n- - - - -\n");
		fclose($fh);	
	}
}

if(!function_exists("debug_print_backtrace2")){
	// PHP4 workaround... not needed 4 PHP5
	function debug_print_backtrace2() {
		echo "<pre>";
		$ar = debug_backtrace();
		//array_pop($ar);
		var_dump($ar);
		echo "</pre>";	
	}
}
?>