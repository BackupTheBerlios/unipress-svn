<?php
// $Id$
// Bereiche anlegen, editieren, Bereichsliste erhalten

require_once "press.class.php";

class press_sites
	extends press {

	var $error_msg = ""; // FIXME: bad error handler

	var $min_name_len = 3; 
	
	var $prefix = "";
	
	function set_prefix($np) {
		$this->prefix=trim($np);
	}
	
	function error($nr,$text="") {
		$this->DBG->enter_method();
		$e = array(	0	=>"keine MySQL Object. Bitte erst use_connection(MySQL \$connection) verwenden!",
					1	=>"press_sites benoetigt mindestens MySQL-Class Version 3.3.1, das übergebene Object ist nicht vom Typ MySQL.",
					2	=>"press_sites bneoetigt mindestens MySQL-Class Version 3.3.1",
					3	=>"unbekannter fehler",

		);

		if (!array_key_exists($nr,$e)) { $nr=3; }
		$this->DBG->leave_method();
		die ($e[$nr]." ".$text);
	}

	// if id is additionally given, kuerzel have to exist with another id
	function kuerzel_exists($kuerzel, $id=0) {
			$this->DBG->enter_method();
		// check if kuerzel exists
		$id_w ="";
		if ($id!="") { $id_w= " AND id!=".$id; } 
		
		$sql = "SELECT kuerzel FROM ".$this->prefix."press_sites WHERE kuerzel='".$kuerzel."' ".$id_w;
				$this->DBG->sql($sql);
		$ret = $this->conn->select( $sql );
				$this->DBG->watch_var("ret", $ret);
		
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
		$this->DBG->enter_method();
		
		if ( empty( $this->conn ) ) {
			error(0);
		}
		$name = clean_in($name);
		if (strlen($name)<$this->min_name_len) {
			$this->error_msg="Name zu kurz";
			$this->DBG->leave_method($this->error_msg);
			return false; // normal exit, name too short
		}
		// check if kuerzel exists
		$kuerzel = clean_in($kuerzel);
		if ($kuerzel!="" && $this->kuerzel_exists($kuerzel)==true) {
			$this->error_msg="Kürzel existiert";
			$this->DBG->leave_method($this->error_msg);
			return false;
		}
			
		$sql = "INSERT INTO ".$this->prefix."press_sites ( id, name, kuerzel, head, foot ) " .
				"VALUES ('', '".clean_in($name)."', '".clean_in($kuerzel)."', '".clean_in($head)."', '".clean_in($foot)."')";
		//echo $sql;
		$this->DBG->sql($sql);
		$ret = $this->conn->insert( $sql );
		
		if ($ret == false) {
			$this->DBG->send_message($this->conn->error());
		}
		
		$this->DBG->leave_method($ret);
		return $ret;
	}

	function edit($id, $name, $kuerzel, $head, $foot) {
		$this->DBG->enter_method();
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
		
		if ($this->getty("name", $id)==false) {
			$this->error_msg="ID existiert nicht und kann nicht ge?ndert werden.";
			$this->DBG->leave_method($this->error_msg);
			return false; // normal exit, name too short
		}

		// update
		$sql = "UPDATE ".$this->prefix."press_sites SET name='".clean_in($name).
				"', kuerzel='".clean_in($kuerzel)."'" .
				", head='".clean_in($head)."', foot='".clean_in($foot)."'" .
				" WHERE id=$id";
		if(is_object($this->DBG))$this->DBG->sql($sql);
		$ret = $this->conn->update( $sql );
		if(is_object($this->DBG))$this->DBG->watch_var("ret",$ret);
		if(is_object($this->DBG))$this->DBG->leave_method($ret);
		return $ret;
	}

	function getty($what, $id ) {
				/*$id = (int) $id;
		if (!is_int($id)) {
			$this->error_msg="ID muss eine Ganzzahl (int) sein";
			return false; // id should be an int
		}
*/
		$sql = "SELECT ".$what." FROM ".$this->prefix."press_sites WHERE id=".$id;
		$ret = $this->conn->select( $sql );
		if (!is_array($ret)) {
			$this->error_msg="ID existiert nicht";
			return false; // id doesn exists
		}

		return $ret[0][$what];
	}
	
	
	function get_info($id) {
		$this->DBG->enter_method();
		$info['name']	=	$this->getty("name", $id);
		$info['kuerzel']=	$this->getty("kuerzel", $id);
		$info['head']	=	$this->getty("head", $id);
		$info['foot']	=	$this->getty("foot", $id);
		
		$info['id']		=	$id;
		if($info['name']==false) return false;
		$this->DBG->leave_method($info);
		return $info;
	}
	//ok
	function show_all() {
		$this->DBG->enter_method();
		if ( empty( $this->conn ) ) {
			error(0);
		}
		$this->conn->set_select_type(MYSQL_ASSOC);
		$sql = "SELECT id, name, kuerzel FROM ".$this->prefix."press_sites ORDER BY name ASC";
		$this->DBG->sql($sql);
		$ret = $this->conn->select( $sql );

		$this->DBG->leave_method($ret);
		return $ret;
	}
	
	function show_list($aim) {
		$this->DBG->enter_method();
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
		$this->DBG->leave_method($r);
		return $r;
	}
	
	function get_all($uid=1) {
		$this->DBG->enter_method();
		$this->conn->set_select_type(MYSQL_BOTH);
		// user?
		$where = "";
		if($uid!=1) $where = "WHERE r.uid= ".$uid;
		
		$sql = "SELECT s.id AS value ,concat(s.name,' \(', s.kuerzel,'\)') as name  " .
				"FROM ".$this->prefix."press_us_rel AS r " .
				"LEFT JOIN ".$this->prefix."press_sites AS s " .
				"ON r.sid=s.id " .
				$where.
				" ORDER by s.name ASC";
				
		//$sql = "SELECT id as value, concat(name,' \(', kuerzel,'\)') as name FROM ".$this->prefix."press_sites ORDER BY name ASC";
		$this->DBG->sql($sql);
		$ret = $this->conn->select( $sql );
		$this->DBG->watch_var("# values", count($ret));
		
		$this->DBG->leave_method($ret);
		return $ret;
	}
	
}

?>