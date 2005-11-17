<?php
require_once "press.class.php";

class press_entry 
	extends press {
	// extends MySQL ?

	// private
	var $id			= NULL;
	var $source		= NULL;
	var $filename	= NULL;
	var $wlink		= NULL;
	var $title		= NULL;
	var $date		= NULL;
		// linked to sites
	var $sites		= array ();
	var $keywords	= array ();

	// public :(
	var $error_cmsg = "";
	
	// protected
	var $conn		= NULL;


	function error($nr,$text="") {
		
		$e = array(	0	=>"unbekannter fehler",
					1	=>"press_sites benoetigt mindestens MySQL-Class Version 3.3.1, das ï¿½bergebene Object ist nicht vom Typ MySQL.",
					2	=>"press_sites bneoetigt mindestens MySQL-Class Version 3.3.1",
					3	=>"es existiert keine Verbindung zur Datenbank",
					4	=>"kann neue Quelle nicht anlegen",
					10	=>"Es fehlen Daten: ",
					11	=>"ERROR; ",
		);

		if (!array_key_exists($nr,$e)) { $nr=0; }
		
		// error 0-9 are deadly errors
		if ($nr<10) die ($e[$nr]." ".$text);
		$this->error_cmsg=$e[$nr]." ".$text;
		return false;
	}
	
	/* contructor 
	function press_entry(& $mysql_object) {
		if (get_class($mysql_object) != "mysql" && get_class($mysql_object) != "MySQL") {
			$this->error(1, get_class($mysql_object));
		}
		if (str_replace(".", "", $mysql_object->VERSION) < 331) {
			$this->error(2);
		}
		$this->conn = $mysql_object;
	}
	*/
	/* public */
	// add and check date (day/month day.month. d.m.y dd.mm.yy dd.mm.yyyy mm.dd.yy / and -instead of .)
	function set_date($d) {
		$dateform	=	"german"; 		// what do i suppose, which form i got
		$buffer 	=	$d;				// save for error_msg
		$preset_year=	strftime("%Y");	// with current year
		
		$d = trim($d);
		if (strlen($d)<3) {
			return $this->error(11, "Datum ($d) ungültig da zu kurz.");	
		}	
		// make some tests
		if (!ereg("\.",$d)) $dateform = "other"; // sonst: german
		
		// transform into englisch form with "-"	
		$d = ereg_replace("\.|/","-",$d);
		
		// split
		$d = explode("-", $d);
		$c = count($d);
		
		if ($c<2 || $c>3) {
			return $this->error(11, "Datum ($buffer) ungültig da zuwenige/zuviele Trennzeichen ($c).");
		}

		switch ($dateform) {
			case 'german':	// i got dd.mm.yy? make: yy.mm.dd
				switch ($c) {
					case 2: // dd-mm into mm-dd and add Year
						$day		=	$d[0];
						$month		=	$d[1];
						$year		=	$preset_year; // current year?
					break;
					case 3:
						$day		=	$d[0];
						$month		=	$d[1];
						switch (strlen($year)) {
							case 2:
								$year = substr(0,2,strftime("%Y")).substr(-2,2,$d[2]);
							break;
							case 4:
								$year = $d[2];
								
							break;
							default:
								$year		=	$preset_year; // current year?
						}
					break;
				}
			break;
			default:
				switch ($c) {
					case 2: // no transform
						$day		=	$d[1];
						$month		=	$d[0];
						$year		=	$preset_year; // current year?
					break;
					case 3:
						$day		=	$d[2];
						$month		=	$d[1];
						switch (strlen($year)) {
							case 2:
								$year = substr(0,2,strftime("%Y")).substr(-2,2,$d[0]);
							break;
							case 4:
								$year = $d[0];
								
							break;
							default:
								$year		=	$preset_year; // current year?
						}
					break;
				}
				
		}
	
		if (checkdate(1*$month, 1*$day, 1*$year)!=true) return $this->error(11,"Dieses Datum existiert nicht: $day $month $year");
		//ok
		$ret = sprintf("%4d-%02d-%02d", $year, $month, $day);
	
		// set local
		$this->date = $ret;
		return $ret; // return for checking or as true
		
	}
	
	// add source id
	function set_source($s) {
		$this->source = /*(int) */$s; // überladen wär schick
	}

	// TODO: FileCheck?
	function set_filename($f) {
		$this->filename = trim($f);
	}

	function set_link($l) {
		if (!eregi("^(http|www)", $l)) {
			return $this->error(11, "Link ($l) beginnt nicht mit www oder http [".__FUNCTION__."]");
		}
		$this->wlink = trim($l);
		return true;
	}

	function set_title($t) {
		$this->title = trim($t);
	}

	// add site id (INT)
	function add_site($s) {
		// sites exists?
		$sql = "SELECT id FROM press_sites WHERE id=".$s;
		$id  = $this->conn->select( $sql );
		if (count($id)>0)	array_push($this->sites, (int) $s); //convert to int
		else return $this->error(11, "Site ($s) existiert nicht [".__FUNCTION__."]");
	}
	// add keyword as string
	function add_keyword($k) {
		array_push($this->keywords, strtolower($k));
	}

	// get source list
	function get_source_list() {
		$this->conn->set_select_type(MYSQL_BOTH);
		$sql = "SELECT id AS value ,name FROM ".$this->prefix."press_sources ORDER by name ASC";
		$ret = $this->conn->select( $sql );
		return $ret;
	}
	
	
	/* write */
	// entry: if there is an id, update, otherwise add new
	// kategory: if there is a new..
	function write(){
		$prefix = $this->prefix;
		
		// FIXME: BAD
		$this->error_cmsg = "";
		// check 
		// - db
		// - all required fields	
		if ($this->conn==NULL) {
			return $this->error(3, __FUNCTION__);	
		}
		
		if ($this->source==NULL) {
			return $this->error(10, "Quellenangabe");
		}
		if ($this->filename==NULL) {
			return $this->error(10, "Dateiname/Datei");
		}
		if ($this->wlink==NULL) {
			return $this->error(10, "Link");
		}
		if ($this->title==NULL) {
			return $this->error(10, "Titel");
		}
		if ($this->date==NULL) {
			return $this->error(10, "Erscheinungsdatum");
		}
		if (!is_array($this->sites)) {
			return $this->error(10, "Seiten auf denen Veröffentlicht werden soll");
		}
		if (!is_array($this->keywords)) {
			return $this->error(10, "Keywords, Schluesselbegriffe");
		}
		
		// ok
		// create new source ? -> only admin???
		// noew everybody could create a new source
		$sql = "SELECT id FROM ".$prefix."press_sources WHERE name='".$this->source."'";
		$id  = $this->conn->select( $sql );
		if (count($id)>0) {
			$this->source = $id[0]['id'];	
		} else {
			// create new
			$sql = "INSERT INTO ".$prefix."press_sources (id, name) VALUES ('', '".$this->source."')";
			$sid = $this->conn->insert( $sql );
			if ($sid==false) return $this->error(4);
			else $this->source = $sid;	
		}
			
		// write main entry
		
		$sql = "INSERT INTO ".$prefix."press_entries (source, link, filename, title, date) " .
				"VALUES ('".$this->source."','".$this->wlink . 
				"','".$this->filename."','".$this->title."','".$this->date."')";
		$eid  = $this->conn->insert( $sql );
		
		// after this point should be no errors!!!!!
		
		// write keywords
		// id, keyword
		reset ($this->keywords);
		$keywordlist = array();
		
		// determine existing keywords
		while (list (,$val) = each ($this->keywords)) {
			$id = NULL;
			$sql = "SELECT id FROM ".$prefix."press_keywords WHERE keyword='".$val."'";
			$id  = $this->conn->select( $sql );
			
			if (count($id)>0) {
				array_push($keywordlist, $id[0]['id']);	
			} else {
				// create new
				$sql = "INSERT INTO ".$prefix."press_keywords (id, keyword) VALUES ('', '".$val."')";
				$id  = $this->conn->insert( $sql );
				array_push($keywordlist, $id); 	
			}
			
		}

		// write keyword-entry relation
		while (list (,$val) = each ($keywordlist)) {
			$sql = "INSERT INTO ".$prefix."press_ke_rel (eid, kid) VALUES (".$eid.", ".$val.")";
			$id  = $this->conn->insert( $sql );
		}
		
		// write site-entry relation
		while (list (,$val) = each ($this->sites)) {
			$sql = "INSERT INTO ".$prefix."press_se_rel (eid, sid) VALUES (".$eid.", ".$val.")";
			$id  = $this->conn->insert( $sql );
		}
		

		
		// ok?
		
		return $eid;
						
	}
}


?>