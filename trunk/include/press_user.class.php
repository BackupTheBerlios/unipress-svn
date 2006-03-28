<?php
// $Id$
// Benutzerverwaltung

class press_user {
	// public
	
	// private
	var $SQL;
	var $DBG;
	var $AUTH;
	var $_prefix;
	var $_conf;
	var $_passpol;
	var $_namepol;
	var $_max_false_auth;
	// private import bloc
	var $_pass;
	var $_name;
	var $_admin;
	var $_auth;
	var $_sites;
	var $_id;
	// public methods bloc
	
	function press_user(&$SQL, &$DBG, &$AUTH) {
		// TODO: class checks missing
		$this->SQL = &$SQL;
		$this->DBG = &$DBG;
		$this->AUTH= &$AUTH;
		$this->_max_false_auth = 5;
		
		$this->DBG->enter_method();
		
		$this->_conf = parse_ini_file("configs/auth.conf.ini",1);
		$this->DBG->watch_var("ini-file", $this->_conf);
		
		$this->_passpol = parse_ini_file("configs/passpol.conf.ini");
		$this->DBG->watch_var("ini-file", $this->_passpol);
		
		$this->set_prefix("");
		$this->_namepol=array("min"=>4,"max"=>10);
		$this->_pass = false;
		$this->_name = false;
		$this->_admin = false;
		$this->_id = false;
		$this->_auth = 0;
		
		
		$this->DBG->leave_method();
	}
	
	function auth(){
		$this->DBG->enter_method();
		session_start();
		session_set_cookie_params ( 0 ); // bis browser geschlossen wird...
		$send   = init("send","p",false);
		$active = init("session","s",false);
		$logout = init("menu","r",false);

		$this->DBG->watch_var("Session",$_SESSION);
		// logout request
		if ($logout=="logout") {
			session_destroy();
			session_start();
			$this->DBG->leave_method(false);
			HEADER("Location:index.php"); // redirector 
			return false;
		}
		// sended form
		$user = init("username","p",false);
		$pass = init("password","p",false);	
		if ($send && $user && $pass) {	
			$this->DBG->send_message("User/Pass Check");
	
			$ok = $this->check($user,$pass);
			if ($ok) {
				$this->DBG->send_message("ok");
				//session_register("Usession","Uid","Uadmin","Ulastactivity");
				$_SESSION['id'] = $ok;  
				$authenticated =true; 
				// register session
				$_SESSION['lastactivity'] = time();
				$_SESSION['admin'] = $this->is_admin($_SESSION['id']);
				$_SESSION['session'] = uniqid($_SESSION['lastactivity']);
				$this->register($_SESSION['id'],$_SESSION['session']);
				$this->DBG->leave_method(true);
				return true;
			} else {
				// fehler	
				$error = (strlen($ok)<2) ? "Benutzername oder Passwort falsch." : $ok;
				$this->DBG->send_message("Fehler ".$error);
				$this->DBG->leave_method(false);
				return false;
			}
		} // if send
		
		// active session
		if ($active) {
			$this->DBG->send_message("* * * Session vorhanden * * *");
			$id = init("id","s",0);
			$session = init("session","s",0);
			
			if ($this->check_session($id,$session)) {
				$this->DBG->send_message("* * * Session ok * * *");
				$_SESSION['lastactivity'] = time();
				$this->DBG->leave_method(true);return true;	
				
			} else {
				$this->DBG->send_message("* * * Session getötet * * *");
				session_destroy();
				$this->DBG->leave_method(false);return false;	
			}
		
		}
		$this->DBG->leave_method(false);return false;
	} // function ende
	
	// imports a user from pre-checked form array
	function import($i=array(),$has_id=0) {
		$this->DBG->enter_method();
		if (empty($i) || !is_array($i)) {
			return false;
		}
		
		
		if($has_id>0) {
			// existing user
			// preload from db
			$preload = $this->get_info($has_id);
			$this->_id = $preload;
		} /*
			if($preload==false) return $this->error(10,"Benutzer existiert nicht.");
			// overwrite with formdata
			$this->add_pass($i['pass']);
			$this->add_auth($i['auth']);
			$this->add_sites($i['sites[]']);	
		} else {*/
			foreach($i as $property) {
				list($key,$val)=each($property);
				
				if (is_array($val)) {
					$handler = "add_".substr($key,0,-2);
				} else {
					$handler = "add_".$key;
				}
				// error?
				$reti = $this->$handler($val);
				if(!empty($val) && $has_id==0) { 
					if(!$reti) return $this->error(10,"Fehler bei $handler mit $val");
				}
				$this->DBG->watch_var("Key",$key);
				$this->DBG->watch_var("Handler",$handler);
				$this->DBG->watch_var("Value",$val);
			} // foreach
		//}
		// everything seems to be ok. write down
		
		$this->DBG->leave_method();
		return true;
	}

	// writes a new entry
	function write() {
		$prefix = $this->_prefix;
		$this->DBG->enter_method();
		
		// FIXME: BAD
		$this->error_cmsg = "";

		if($this->_id==false){		
			if (!is_array($this->_sites)) 	{
				$this->DBG->watch_var("Fehler: Bereiche (sites)",$this->_sites);
				return $this->error(10, "Bereichszuordnung");
			}
			if (empty($this->_name)) 		return $this->error(10, "Benutzername");
			if (empty($this->_pass) && $this->_auth==0) return $this->error(10, "Passwort für lokalen Benutzer");
			if (empty($this->_auth))		return $this->error(10, "Authentifizierungsmechanismus");
			if (empty($this->_admin)) 		return $this->error(10, "Administratorflag");
		} else {
		 	$and = "";
		 	$set = "";
		 	$id	=	$this->_id['main']['id'];
			if($this->_pass!=false) { $set .=$and."pass=sha1('".$this->_pass."') ";$and=", ";}
			if($this->_auth!=false) { $set .=$and."auth=".$this->_auth; $auth=", ";}
		} 
		
		// write main entry
		// id  	 name  	 pass  	 counter  	 session  	 auth
		if($this->_id==0) {
			$sql = "INSERT INTO ".$prefix."press_user (id, name, pass, auth) " .
					"VALUES ('','".$this->_name."','".sha1($this->_pass) . 
					"',".$this->_auth.")";
			$eid  = $this->SQL->insert( $sql );
		} elseif($set!="") {
			$sql = "UPDATE ".$prefix."press_user SET ".$set
					." WHERE id=".$id;
			$eid	= $id;
			$eid  = $this->SQL->update( $sql );
		}
		// after this point should be no errors!!!!!
		
		// write site_relation
		// id, site
		reset ($this->_sites);
		
		// write user-site relation
		while (list (,$val) = each ($this->_sites)) {
			if($eid>0){
				$sql = "INSERT INTO ".$prefix."press_us_rel (uid, sid) VALUES (".$eid.", ".$val.")";
				$id  = $this->SQL->insert( $sql );
				if(!$id) $this->error(10, "User($eid)-Site($val)-Relation konnte nicht geschrieben werden.");
			}
		}
		
		// remove admin state (always)
		$sql = "DELETE FROM ".$prefix."press_admins WHERE id=".$eid;
		$this->SQL->delete($sql);
		
		// is admin?
		if(strtolower($this->_admin)=="ja" && $eid>0) {
			$sql = 	"INSERT INTO ".$prefix."press_admins (id) VALUES (".$eid.")";
			if(!$this->SQL->insert( $sql )) $this->error(10, "Konnte Admin-Flag nicht setzen.");
		}
		
		
		
		// ok?
		$this->DBG->leave_method($eid);
		return $eid;
						
	}
	
	function register($id,$session) {
		
		$sql = "UPDATE ".$this->_prefix."press_user SET session='".$session."', counter=0 WHERE id='".$id."' LIMIT 1";
		$this->SQL->update($sql); 	
	}
	
	function inc_counter($name) {
		
		$sql = "UPDATE ".$this->_prefix."press_user SET counter=counter+1 WHERE name='".$name."' LIMIT 1";
		$this->SQL->update($sql); 	
	}
	
	function set_prefix($np) {
		$this->_prefix=trim($np);
	}
	
	function get_password_policy() {
		return $this->_passpol;
	}
	
	function get_auth_list () {
		return $this->_conf;	
	}
	
	function show_list($link) {
		$this->DBG->enter_method();
		$sql = "SELECT id,name, auth FROM ".$this->_prefix."press_user  ORDER BY auth ASC,name ASC";
		$ret = $this->SQL->select($sql);
		$this->DBG->watch_var($sql,$ret);
		
		$sql = "SELECT id AS INDEX_ASSOCIATION FROM ".$this->_prefix."press_admins ";
		$ret2 = $this->SQL->select($sql);
		$this->DBG->watch_var($sql,$ret2);
		

		$output="";		
		$last_auth="";
		
		foreach($ret as $val) {
			// neuer Abschnitt
			if($val['auth']!=$last_auth){
				$last_auth=$val['auth'];
				if ($output!="") {
					$output .= "</ul>";
				}
				$output .="<b>".$this->_conf[$last_auth]['name']."</b><ul>";	
			}
			// Admin?
			if (array_key_exists($val['id'],$ret2)) {$admin = " (Administrator)";}
			else {$admin = "";}
			
			// Abschnittinhalt
			$output.= "<li><a href=\"?menu=$link&id=".$val['id']."\">".htmlentities($val['name']).$admin."</a></li>";


		} // foreach
		
		$output.="</ul>";
		
		$this->DBG->leave_method();
		return $output;
	}
	
	function is_admin($id) {
		$this->DBG->enter_method();
		$sql = "SELECT id AS INDEX_ASSOCIATION FROM ".$this->_prefix."press_admins WHERE id=".clean_in($id);
		$ret = $this->SQL->select($sql);
		$this->DBG->sql($sql);
		$this->DBG->leave_method($ret);
		return ($this->SQL->affected_rows);
	}
	
	function get_info($id) {
		$this->DBG->enter_method();
		$this->SQL->set_select_type(MYSQL_ASSOC);
		// 
		$r = array();
		
		// user info
		$sql = "SELECT id, name, auth FROM ".$this->_prefix."press_user WHERE id=".clean_in($id);
		$ret = $this->SQL->select($sql);
		$this->DBG->sql($sql);
		$r['main'] = $ret[0];
		$r['admin']= $this->is_admin($id);
		
		$sql = "SELECT id,name, kuerzel FROM ".$this->_prefix."press_us_rel AS rel " .
				"LEFT JOIN ".$this->_prefix."press_sites AS s ON rel.sid=s.id " .
				"WHERE rel.uid=".clean_in($id)." ORDER BY name ASC";
		$ret = $this->SQL->select($sql);
		$this->DBG->sql($sql);
		$r['sites'] = $ret;
		$this->DBG->leave_method($r);
		return $r;
	}
	
	function check($user, $pass) {
		$this->DBG->enter_method();
		$sql = "SELECT auth, pass,id,counter FROM ".$this->prefix."press_user WHERE name='".clean_in($user)."'";
		$ret = $this->SQL->select($sql); $data=$ret[0];
		$this->DBG->watch_var($sql,$ret);
		
		
		if ($data['auth']==0) {
			$this->DBG->send_message("USING: local auth");
			$ret = sha1($pass)==$data['pass'];	
		} elseif (array_key_exists($data['auth'],$this->_conf)) {
			$this->DBG->send_message("USING: remote auth");
			$this->AUTH->set_new_auth($this->_conf[$data['auth']]['type'], $this->_conf[$data['auth']]['handle']);
			$ret = $this->AUTH->check($user,$pass);		
			$this->DBG->watch_var("remote auth:",$ret);	
		} else {
			$this->DBG->send_message("NOT USING: invalid auth mechanism	");
			$this->DBG->watch_var("auth mechanisms",$this->_conf);
			$this->DBG->watch_var("used i should auth",$data['auth']);
			$ret = false;
		}
		
		if($ret['counter']>=$this->_max_false_auth) {
			$this->DBG->send_message("false auth counter has reached ".$data['counter']);
			$ret = false;			
		} 
		
		if($ret==true) {
			$ret = $data['id'];
		} 
		else {
			$this->inc_counter($user);	
		}
		
		$this->DBG->leave_method($ret);
		return 	$ret;
	}
	
	function check_session ($id,$session) {
		$this->DBG->enter_method();
		
		$sql = "SELECT id,session FROM ".$this->prefix."press_user WHERE id='".clean_in($id)."'";
		$ret = $this->SQL->select($sql); $ret=$ret[0];
		$this->DBG->watch_var($sql,$ret);
		if($ret['session']==$session) {$ret=true;} else {$ret=false;}
		$this->DBG->leave_method($ret);
		return 	$ret;
	}
	
	function edit_user($user="",$pass="",$auth=0){
		die("function edit_user not implemented yet");		
	}
	
	function add_pass($pass="") {
		$pass = trim($pass);
		$len  = strlen($pass);
		$min  = $this->_passpol['min'];
		$max  = $this->_passpol['max'];
		
		if ($len > $max) return $this->error(11,"Passwort zu lang ($len statt $max Zeichen)");
		if ($len < $min && $len>0) {
			// zero-length is ok for me, its used for "no-change" or remote user
			return $this->error(11,"Passwort zu kurz ($len statt $min Zeichen)");
		}
		
		$this->_pass = $pass;
		return true;
	}

	function add_name($name="") {
		$name = trim($name);
		$len  = strlen($name);
		$min  = $this->_namepol['min'];
		$max  = $this->_namepol['max'];
		
		if ($len > $max) return $this->error(11,"Name zu lang ($len statt $max Zeichen)");
		if ($len < $min) return $this->error(11,"Name zu kurz ($len statt $min Zeichen)");
		
		$this->_name = $name;
		return true;
	}
	
	function add_admin($admin="") {
		$admin = trim($admin);
		$this->_admin=$admin;

		return true;	
	}
	
	function add_auth($auth=0) {
		$auth = (int) $auth;
		if( array_key_exists($auth, $this->_conf) ) $this->_auth = $auth;
		else return $this->error(11,"Unbekanntes Authentifizierungsverfahren angefordert!");
		return true;
	}
	
	function add_sites($sites=array()) {
		$this->DBG->enter_method();
		if(!is_array($sites) || empty($sites)) {
			$r = $this->error(11,"Keine Seitenzuordnung übergeben!");
			$this->DBG->leave_method($r);
			return $r;
		}
		$this->_sites = $sites;
		$this->DBG->leave_method(true);
		return true;
	}
	
	//? 
	function create_user($user = "", $pass = "", $auth = 0) {
		$Cuser = strlen($user);
		$Cpass = strlen($pass);
	
        if ($this->user_exists($user)) {
			return $this->error("Der Benutzer existiert bereits");// [".__FUNCTION__."]");
		}
		
		$sql = "INSERT INTO ".$this->prefix."user (name,pass,auth) VALUES ('$user', '".sha1($pass)."', $auth)";
		return $this->insert($sql);
	}
	// private block	
	
	function error($nr,$text="") {
		
		$e = array(	0	=>"unbekannter fehler",
					1	=>"press_user benoetigt mindestens MySQL-Class Version 3.3.1, das uebergebene Object ist nicht vom Typ MySQL.",
					2	=>"press_user benoetigt mindestens MySQL-Class Version 3.3.1",
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
}
?>