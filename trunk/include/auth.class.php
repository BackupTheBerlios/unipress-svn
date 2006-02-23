<?php

// $Id$
// Benutzerauthentifizierung mittels IMAP,POP3 


class auth {
	// public
	var $tested; // was connection tested and ok?
	
	// private
	var $_server;
	var $_port;
	var $_type;
	var $_errno;
	var $_errmsg;
	var $_timeout;
	var $_test;
	
	
	// public part
	/**
	 * auth - Authorising constructor
	 * 
	 * @access public
	 * 
	 * @var string type - Type of Connection (imap, simap, imapssl, pop3)
	 * @var string connectionhandle - Syntax: server[:port[:timeout]] 
	 */
    function auth($type="", $connectionhandle="") {
    	// var init
    	$this->tested=false;
    	$this->_server = NULL;
		$this->_port = NULL;
		$this->_type = NULL;
		$this->_errno = 0;
		$this->_errmsg = "";
		$this->_timeout=15;
		$this->_test=false;	
    	
    	$type = trim(strtolower($type));
    	$this->_type = in_array($type,array('imap','simap','imapssl','pop3')) ? $type : "simap";
    					
    	if ($type!="" && $connectionhandle!="") $this->set_new_auth($type, $connectionhandle) ;
    }
	/**
	 * set_new_auth - Authorising connection handler
	 * 
	 * @access public
	 * 
	 * @var string type - Type of Connection (imap, simap, imapssl, pop3)
	 * @var string connectionhandle - Syntax: server[:port[:timeout]] 
	 * @return boolean is a connection avaible? (test) 
	 */
	function set_new_auth($type, $connectionhandle) {
		//debug
		//echo $type." - ".$connectionhandle;
		$tmp = explode(":",$connectionhandle);
    	$tcnt= count($tmp);
    	$this->_server = $tmp[0];
    	if ($tcnt>1) {$this->_port = $tmp[1];}
    	if ($tcnt>2) {$this->_timeout = $tmp[2];}

		//test
		$this->_test=true;
		$handler = "_".$this->_type;
		$this->tested = $this->$handler();
		return $this->tested;
	}

    function check($user,$pass) {
    	if ($this->tested==false) return $this->_error("Die Verbindungsdaten sind fehlerhaft!");
    	$this->_test=false;
    	$handler = "_".$this->_type;
    	return $this->$handler($user, $pass);
    }
    
    // private part
    function _error ($text=false) {
    	if ($text) echo $text; 
    	else  	echo "Fehler (".$this->_errno."): ".$this->_errmsg;	
    	return false;
    }
  
    function _simap($user="", $pass="") {
    	$port = (empty($this->_port)) ? 143 : $this->_port;
		$mbox = @fsockopen($this->_server,$port,$this->_errno, $this->_errmsg,$this->_timeout);
		if ($mbox==false) return $this->_error("Die Verbindung zum Server konnte nicht hergestellt werden.");
		stream_set_blocking ( $mbox, 1 );
		$reti = fgetss($mbox); 	//echo "<br>".$reti;

		if ($this->_test) return (strlen($reti)>0);

		fputs($mbox,"a001 LOGIN ".$user." ".$pass."\n\r");
		$reti = fgetss($mbox); 	//echo "<br>".$reti;
		fputs($mbox,"a002 LOGOUT\n\r");
		if (eregi("User logged in",$reti)) $mbox=true; else $mbox=false;
		
		return $mbox;
    }
    
    function _imap($user="", $pass="") {
    	return $this->_error("Function not implemented yet.");	
    }
    
	function _imapssl($user="", $pass="") {
    	return $this->_error("Function not implemented yet.");	
    }
    
	function _pop3($user="", $pass="") {
    	
    	if (!function_exists("imap_open")) {
    		return $this->_error("Die PHP-Imap Funktionalität wird benötigt!");
    	}
    	$port = (empty($this->_port)) ? 110 : $this->_port;
    	
    	$mbox = @fsockopen($this->_server,$port,$this->_errno, $this->_errmsg,$this->_timeout);
		if ($mbox==false) return $this->_error("Die Verbindung zum Server konnte nicht hergestellt werden.");
		stream_set_blocking ( $mbox, 1 );
		$reti = fgetss($mbox); 	//echo "<br>".$reti;
		if ($this->_test) return (strlen($reti)>0);
    	    	
		$mbox = @imap_open ("{".$this->_server.":".$port."/pop3}INBOX", $user, $pass);
		
		return $mbox;
    }

}

?>