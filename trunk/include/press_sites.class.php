let <?php
require_once "press.class.php";

class press_sites
	extends press {

	var $error_msg = ""; // FIXME: bad error handler

	var $min_name_len = 3; 
	
	
	function error($nr,$text="") {
		$this->DBG->enter_method();
		$e = array(	0	=>"keine MySQL Object. Bitte erst use_connection(MySQL \$connection) verwenden!",
					1	=>"press_sites benoetigt mindestens MySQL-Class Version 3.3.1, das �bergebene Object ist nicht vom Typ MySQL.",
					2	=>"press_sites bneoetigt mindestens MySQL-Class Version 3.3.1",
					3	=>"unbekannter fehler",

		);

		if (!array_key_exists($nr,$e)) { $nr=3; }
		$this->DBG->leave_method();
		die ($e[$nr]." ".$text);
	}

	// if id is additionally given, kuerzel have to exist with another id
	function kuerzel_exists($kuerzel, $id=0) {
		if(is_object($this->DBG))$this->DBG->enter_method();
		// check if kuerzel exists
		$id_w ="";
		if ($id!="") { $id_w= " AND id!=".$id; } 
		
		$sql = "SELECT kuerzel FROM ".$this->prefix.$this->sites." WHERE kuerzel='".$kuerzel."' ".$id_w;
		if(is_object($this->DBG))$this->DBG->sql($sql);
		$ret = $this->conn->select( $sql );
		if(is_object($this->DBG))$this->DBG->watch_var("ret", $ret);
		
		//echo "<br>aff_rows:".$this->conn->get_affected_rows();
		
		if ($this->conn->get_affected_rows()!=0) {
			$this->error_msg="Kuerzel existiert";
			if(is_object($this->DBG))$this->DBG->leave_method($this->error_msg);
			return true;
		}	
		if(is_object($this->DBG))$this->DBG->leave_method(false);
		return false;
	}
	
	// FIXME: if entry exists and you try to write it twice, you would got the old id
	// otherwise -1
	function add($name, $kuerzel, $head, $foot) {
		if(is_object($this->DBG)) $this->DBG->enter_method();
		
		if ( empty( $this->conn ) ) {
			error(0);
		}
		$name = clean_in($name);
		if (strlen($name)<$this->min_name_len) {
			$this->error_msg="Name zu kurz";
			if(is_object($this->DBG))$this->DBG->leave_method($this->error_msg);
			return false; // normal exit, name too short
		}
		// check if kuerzel exists
		$kuerzel = clean_in($kuerzel);
		if ($kuerzel!="" && $this->kuerzel_exists($kuerzel)==true) {
			if(is_object($this->DBG))$this->DBG->leave_method(false);	
			return false;
		}
		
		$sql = "INSERT INTO ".$this->prefix."sites ( id, name, kuerzel, head, foot ) " .
				"VALUES ('', '".$name."', '".$kuerzel."', '".clean_in($head)."', '".clean_in($foot)."')";
		//echo $sql;
		$ret = $this->conn->insert( $sql );
		
		if(is_object($this->DBG))$this->DBG->leave_method($ret);
		return $ret;
	}

	function edit($id, $name, $kuerzel, $head, $foot) {
		if(is_object($this->DBG))$this->DBG->enter_method();
		if ( empty( $this->conn ) ) {
			error(0);
		}
		$name = clean_in($name);
		if (strlen($name) < $this->min_name_len) {
			$this->error_msg="Name zu kurz";
			if(is_object($this->DBG))$this->DBG->leave_method($this->error_msg);
			return false; // normal exit, name too short
		}
		// check if kuerzel exists
		$kuerzel = clean_in($kuerzel);
		if ($this->kuerzel_exists($kuerzel, $id)==true) return false;
		
		if ($this->get_name($id)==false) {
			$this->error_msg="ID existiert nicht und kann nicht ge�ndert werden.";
			$this->DBG->leave_method($this->error_msg);
			return false; // normal exit, name too short
		}

		// update
		$sql = "UPDATE ".$this->prefix.$this->sites." SET name='$name', kuerzel='$kuerzel'" .
				", head='".clean_in($head)."', foot='".clean_in($foot)."'" .
				" WHERE id=$id";
		if(is_object($this->DBG))$this->DBG->sql($sql);
		$ret = $this->conn->update( $sql );
		if(is_object($this->DBG))$this->DBG->watch_var("ret",$ret);
		if(is_object($this->DBG))$this->DBG->leave_method($ret);
		return $ret;
	}

	function get_name($id) {
		/*$id = (int) $id;
		if (!is_int($id)) {
			$this->error_msg="ID muss eine Ganzzahl (int) sein";
			return false; // id should be an int
		}
*/
		//check id_exists
		$sql = "SELECT name FROM ".$this->prefix.$this->sites." WHERE id=$id";
		$ret = $this->conn->select( $sql );
		if (!is_array($ret)) {
			$this->error_msg="ID existiert nicht";
			return false; // id doesn exists
		}

		return $ret[0]['name'];
	}

	function get_kuerzel($id) {
		/*$id = (int) $id;
		if (!is_int($id)) {
			$this->error_msg="ID ($id) muss eine Ganzzahl (int) sein";
			return false; // id should be an int
		}
*/
		//check id_exists
		$sql = "SELECT kuerzel FROM ".$this->prefix.$this->sites." WHERE id=$id";
		$ret = $this->conn->select( $sql );
		if (!is_array($ret)) {
			$this->error_msg="ID existiert nicht";
			return false; // id doesn exists
		}

		return $ret[0]['kuerzel'];
	}
	
	function get_info($id) {
		$info['name']	=	$this->get_name($id);
		$info['kuerzel']=	$this->get_kuerzel($id);
		$info['id']		=	$id;
		if($info['name']==false) return false;
		return $info;
	}
	//ok
	function show_all() {
		if ( empty( $this->conn ) ) {
			error(0);
		}
		$this->conn->set_select_type(MYSQL_ASSOC);
		$sql = "SELECT id, name, kuerzel FROM ".$this->prefix.$this->sites." ORDER BY name ASC";
		$ret = $this->conn->select( $sql );

		return $ret;
	}
	
	function show_list($aim) {
		$aim	=	trim($aim);
		$list 	= 	$this->show_all();
		$optionen = "<ul>";
		if (is_array($list) && count($list)>0) {
			reset($list);
			while (list($key, $val) = each($list)) {
				$optionen .= "<li><a href=\"?menu=$aim&id=".$val['id']."\">".htmlentities($val['name'])." (".htmlentities($val['kuerzel']).")</a></li>";
			}
			$r =""
				#."<strong>Bereich (K&uuml;rzel)</strong>"
				.$optionen."</ul>";
		} else $r= $optionen . "Es existiert noch kein Bereich. Legen Sie einen an." ."</ul>";
		return $r;
	}
	
	function get_all() {
		$this->conn->set_select_type(MYSQL_BOTH);
		$sql = "SELECT id as value, concat(name,' \(', kuerzel,'\)') as name FROM ".$this->prefix.$this->sites." ORDER BY name ASC";
		$ret = $this->conn->select( $sql );
		return $ret;
	}
	
}

?>