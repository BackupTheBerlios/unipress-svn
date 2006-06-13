<?php
// $Id$
require_once("cbmysql.class.php");

class User extends MySQL {
	/**
	 * press::prefix
	 * table name prefix
	 *
	 * @access private
	 */
	var $prefix = "press_";

	/**
	 *  press::set_prefix()
	 * sets tables name prefix
	 *
	 * @param string $prefix new table prefix
	 * @access public
	 *
	 */
	function set_prefix($prefix) {
		$prefix = trim($prefix);
		$this->prefix = $prefix;
		return $prefix;
	}

    // protected
	function user_exists($user) {

	    if (is_int($user)) {
	         $sql = "SELECT (id=$user) as isuser FROM ".$this->prefix."user WHERE id='$user' LIMIT 1";
	    } else {
			$user = stripcslashes(trim($user));
		    $sql = "SELECT (name='$user') as isuser FROM ".$this->prefix."user WHERE name='$user' LIMIT 1";
		}

		$ret = $this->select($sql);
		#echo "[user exists] $sql <br>";
		#var_dump($ret);
		#echo "[ue] ".count($ret);
		if (count($ret) > 0) {
			return ($ret[0]['isuser']);
		} else {
			return false;
		}
	}

    //public
    function user_is_admin($user) {
        $user = stripcslashes(trim($user));
        if (is_int($user)) {
	         $sql = "SELECT (admin>0) as admin FROM ".$this->prefix."user WHERE id='$user' LIMIT 1";
	    } else {
		     $sql = "SELECT (admin>0) as admin FROM ".$this->prefix."user WHERE name='$user' LIMIT 1";
		}
        //old $sql = "SELECT (admin>0) as admin FROM ".$this->prefix."user WHERE name='$user' LIMIT 1";
        $ret = $this->select($sql);
        if (count($ret) > 0) {
			return ($ret[0]['admin']);
		} else {
			return false;
		}
    }


	function create_user($user = "", $pass = "", $admin=0) {
		$Cuser = strlen($user);
		$Cpass = strlen($pass);
		if ($Cuser < 4) {
			return $this->error("Name zu kurz ($Cuser Zeichen) [".__FUNCTION__."]");
		}
		if ($Cpass < 8) {
			return $this->error("Passwort zu kurz ($Cpass Zeichen)[".__FUNCTION__."]");
		}

        if ($this->user_exists($user)) {
			return $this->error("Der Benutzer existiert bereits [".__FUNCTION__."]");
		}
		$sql = "INSERT INTO ".$this->prefix."user (name,pass,admin) VALUES ('$user', '".sha1($pass)."', $admin)";
		return $this->insert($sql);
	}

	function edit_user($id, $user, $pass, $admin) {
	    #echo $id;
		#echo ;
		#$r=1;
		if (!$this->user_exists($id)) {
			return $this->error("Der Benutzer ($id) existiert noch nicht [".__FUNCTION__."]");
		}

		$Cuser = strlen($user);
		$Cpass = strlen($pass);
		if ($Cuser < 4) {
			return $this->error("Name zu kurz ($Cuser Zeichen) [".__FUNCTION__."]");
		}
		if ($Cpass < 8) {
			return $this->error("Passwort zu kurz ($Cpass Zeichen)[".__FUNCTION__."]");
		}

        if ($admin<0 || $admin>1) {$admin=0;} // set default to normal user

		$sql = "UPDATE ".$this->prefix."user SET name='$user',pass='".sha1($pass)."', admin=$admin WHERE id=$id";
		#echo "<br>".$sql;
		return $this->update($sql);
	}

	// flase auth counter
	function fcount($user, $reset = false) {
		if ($user == "root") {
			return true;
		}
		// reset counter
		if ($reset == true) {
			$this->update("UPDATE ".$this->prefix."user SET counter=0 WHERE name='$user'");
			return true;
		}
		// inc counter
		$this->update("UPDATE ".$this->prefix."user SET counter=counter+1 WHERE name='$user' AND counter < 10");
		return true;
	}

	// new, default session auth
	function auth($user = "", $pass = "") {
		$this->error_cmsg=""; // reset error msg

		$user = stripcslashes(trim($user));
		$pass = stripcslashes(trim($pass));

		/* session auth */
		if ($user=="" && $pass=="") {
		    #echo "session auth!!";
			$user = $_SESSION['id'];
			$pass = session_id();

			// session alive?
			$sql = "SELECT id, session FROM ".$this->prefix."user WHERE id='$user' AND session='".$pass."'";

			#echo $sql;
			$ret = $this->select($sql);
            #var_dump($ret);
			if (array_key_exists("session",$ret)) {
				return true;
			} else {
				$this->error("Session auth fehlgeschlagen. Existiert Session? Ist User $user angemeldet?");
			}
		}

		/* normal auth */
		$Cuser = strlen($user);
		$Cpass = strlen($pass);
		if ($Cuser < 4) {
			return $this->error("Name zu kurz ($Cuser Zeichen) [".__FUNCTION__."]");
		}
		if ($Cpass < 8) {
			return $this->error("Passwort zu kurz ($Cpass Zeichen)[".__FUNCTION__."]");
		}

		$sql = "SELECT id, name, session FROM ".$this->prefix."user WHERE name='$user' AND pass='".sha1($pass)."'";
		$ret = $this->select($sql);
		if (count($ret) == 0) {
			// false
			// inc falsecounter
			$this->fcount($user);
			// destroy session if existing
			if (session_id() != "")
				session_destroy();
			return $this->error("Name oder Password falsch. [".__FUNCTION__."]");
			#return false;

		}
		elseif ($ret[0]['name'] == $user) {
			// ok
			$this->fcount($user, true);
			// FIXME: session could only started before output was send.. otherwise
			// you got a warning
			@session_start();
			$_SESSION["id"] = $ret[0]['id'];
			$this->update("UPDATE ".$this->prefix."user SET session='".session_id()."' WHERE id=".$ret[0]['id']);
			return true; // return true
		}
	} // auth

	function logout() {
		$user = $_SESSION['id'];
		$pass = session_id();

		// session alive?
		$sql = "SELECT id, session FROM ".$this->prefix."user WHERE id='$user' AND session='".$pass."'";
		$ret = $this->select($sql);

		if (count($ret) > 0) {
			session_destroy();
			return true;
		}
		// FIXME: informal
		$this->error_cmsg = "no session to kill or already logged out";
		return false;
	} // logout

    /* press entry section /
    function write_entry($press_entry){
        if (!is_Object($press_entry)) {
            return $this->error("Eintrag ist kein Object [".__FUNCTION__."]");
        }
        if (get_class($press_entry)!="press_entry") {
            return $this->error("Object vom falschen Typ, erwate 'press_entry', bekam '".get_class($press_entry)."' [".__FUNCTION__."]");
        }
        //echo $press_entry;
        echo $press_entry->text;
    }
    */
}
?>