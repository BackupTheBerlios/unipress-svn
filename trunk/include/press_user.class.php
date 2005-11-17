<?php
require_once("user.class.php");
// TODO: LDAP Auth
class press_user extends User {
    //TODO: tablechecker oder �hnliches w�re gut
    function check_extension(){
     	// table extension setzen?!
     	$this->error("function not implemented yet. [".__FUNCTION__."]");

    }

    // TODO: admin level
    function get_admin_level($id){
    	$this->error("function not implemented yet. [".__FUNCTION__."]");

		if (!$this->user_exists($id)) {
			return $this->error("Der Benutzer ($id) existiert nicht [".__FUNCTION__."]");
		}
		$sql = "SELECT adminlevel FROM ".$this->prefix."press_user WHERE id=$id";
		$ret = $SQL->select($sql);
		if(array_key_exists("adminlevel",$ret)) {
			decbin($ret[0]['adminlevel']);
		} else {
		 	return 0;
		}

    }
    // TODO: set admin levels
	function set_site_admin($id){
		$this->error("function not implemented yet. [".__FUNCTION__."]");
	}
}
?>