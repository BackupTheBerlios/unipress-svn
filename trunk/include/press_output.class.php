<?php
// $Id$
// Ausgabebehandlung
/*
 * class htemplate{
	
	var $_fh;
	
	function htemplate($file){
		$fh = fopen($file,"r");
		if($) 			
	}	
}
*/

function wget($url) {
	if (empty($url)) return "";

	$handle = fopen($url,"r");
	$buffer = "";
	while (!feof($handle)) {
		$buffer .= fgets($handle, 4096);
	}
	fclose ($handle);
	return $buffer;
}

class press_output{
	// public
	// private
	// protected
	var $_SQL;
	var $_DBG;
	var $_prefix;
	
	function press_output(&$SQL, &$DBG) {
		$this->_SQL =&$SQL;
		$this->_DBG =&$DBG;
		$this->_prefix="";
	}
	
	function set_prefix($prefix){
		$this->_prefix = trim($prefix);
	}
	
	function make_static($kuerzel="") {
		$kuerzel = trim($kuerzel);
		$data = $this->show_all4($kuerzel);
		
		// unvollst. dokument
		$fh = fopen("cache/_all_".$kuerzel.".html", "w");
		fputs($fh, $data);
		fclose($fh);
		// dokument mit header dateien (wenn gegeben)
		$sql = "SELECT head,foot FROM ".$this->_prefix."press_sites WHERE kuerzel='$kuerzel'";
		$res = $this->_SQL->select( $sql );
		//print_r($res);
		if(!empty($res)) {
			$head = wget($res[0]['head']);
			$foot = wget($res[0]['foot']);
			// schreibe doc mit headern
			$fh = fopen("cache/_cus_".$kuerzel.".html", "w");
			fputs($fh, $head.$data.$foot);
			fclose($fh);
		}
	}
	
	function show_all4($kuerzel="") {
		$kuerzel = trim($kuerzel);
		$data = $this->_get_all($kuerzel);
		return $this->_parse_results($data,$kuerzel,$meta=array());
	}
	
	// private
	
	
	function _parse_results($data, $kuerzel="", $meta=array()){
				
		$templatefile = "t_cache/_all_".$kuerzel.".thtml";
		if (!file_exists($templatefile)) {
			$templatefile = "t_cache/_all_default.thtml";		
			if (!file_exists($templatefile)) 
				die("Default-Template (".$templatefile.") existiert nicht. Dies ist ein schwerwiegender Fehler!");
		}
		
		// parse ... this should be made better
		$pattern="";
		require ($templatefile); // FIXME: bad style!
		
		// now $pattern is defined
		$loopstart = strpos($pattern,"##loop-start##")+strlen("##loop-start##");
		$loopend = strpos($pattern,"##loop-end##");
		$loop = substr($pattern, $loopstart, $loopend-$loopstart);
		$head = substr($pattern, 0, $loopstart-strlen("##loop-start##"));
		$foot = substr($pattern, $loopend+strlen("##loop-end##"));
		
		$result="";
		foreach($data as $entry){
			$tmp = $loop;
			while(list($key,$val)=each($entry)) {
				$tmp = str_replace("##".$key."##",$val,$tmp);
			}
			// remove rest
			$result .= preg_replace("(##([\w]*)##)","",$tmp);
//			$result .= $tmp;
		}
		
		// head&foot
		while(list($key,$val)=each($meta)) {
			$head = str_replace("##".$key."##",$val,$head);
		}
		while(list($key,$val)=each($meta)) {
			$foot = str_replace("##".$key."##",$val,$foot);
		}
		
		// remove rest ##
		$head = preg_replace("(##([\w]*)##)","",$head);
		$foot = preg_replace("(##([\w]*)##)","",$foot);
		
		// return				
		return $head . $result . $foot;
	}
	
	// ersetzt get_all
	// ja, aber wo?
	// WER ZUM TEUFEL ruft mich auf?
	function _get_data($meta=array()){
		
		debug_print_backtrace2();
		die(__METHOD__. "::".__CLASS__."::".__FILE__);
	
		$where = " WHERE ";
		$join  = "";
		$sort  = " e.date DESC ";
		
		foreach($meta as $property) {
			while(list($key,$val)=each($property)) {
				switch ($key) {
					case 'kuerzel':
						$join .= " LEFT JOIN ".$this->_prefix."press_se_rel AS rel ON e.id=rel.eid " .
								" LEFT JOIN ".$this->_prefix."press_sites AS sit ON rel.sid=sit.id ";
						$where .= " sit.kuerzel='$vall'"; $wand=" and ";
					 break;
					case 'year':
						$where .= $wand . " YEAR(e.date)='$val'"; $wand=" and ";
					 break; 	
					case 'month':
						$where .= $wand . " MONTH(e.date)='$val'"; $wand=" and ";
					 break;
					case 'keywords':
						$joker ="%";
						if(array_key_exists("exactkeywords",$property) && $property['exactkeywords']=="ja") {$joker="";}
						$join .= " LEFT JOIN ".$this->_prefix."press_ke_rel AS kwrel ON e.id=kwrel.eid " .
								" LEFT JOIN ".$this->_prefix."press_keywords AS kw ON kwrel.kid=kw.id ";
						$keywords = str_replace(","," ",$keywords);
						$keywords = explode(" ",$val);
						$wand = $wand ." ( ";
						foreach($keywords as $keyword) {
							$where .= $wand . " keyword LIKE '".$joker.$val.$joker."'";
							$wand=" and ";
						}
					 break;  	 
				}
			}	
		}
		
		$sql = 'SELECT e.filename, e.title, ' .
				'DATE_FORMAT(e.date,"%d.%m.%Y") as date,s.name as source, ' .
				'SUBSTRING(e.filename, -3) as fileext' .
				' FROM '.$this->_prefix.'press_entries ' .
				'AS e LEFT JOIN '.$this->_prefix.'press_sources AS s ' .
				'ON e.source=s.id '.$join.' '.$where.' ORDER BY e.date DESC';
		$this->_DBG->sql($sql);
		$ret =	$this->_SQL->select($sql);
	}

	function _get_all($kuerzel="") {
		
		$where="";$join="";
		if($kuerzel!="") {
			$join = " LEFT JOIN ".$this->_prefix."press_se_rel AS rel ON e.id=rel.eid " .
					" LEFT JOIN ".$this->_prefix."press_sites AS sit ON rel.sid=sit.id ";
			$where = " WHERE sit.kuerzel='$kuerzel'";
			
		}
		$sql = 'SELECT e.filename, e.title, ' .
				'DATE_FORMAT(e.date,"%d.%m.%Y") as date,s.name as source' .
				' FROM '.$this->_prefix.'press_entries ' .
				'AS e LEFT JOIN '.$this->_prefix.'press_sources AS s ' .
				'ON e.source=s.id '.$join.' '.$where.' ORDER BY e.date DESC';
		$this->_DBG->sql($sql);
		$ret =	$this->_SQL->select($sql);
		return $ret;
	}
		
}
?>