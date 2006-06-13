<?php
function check_writeable($path, $dir, $echodie=true) {
	if (!is_dir($path . $dir)) { 
		if($echodie==true) {
			die ($dir . " does not exists or is not " .
			"readable in '".$path."'");
		} else return false;
	}
	if (!is_writable($path . $dir)) { 
		if($echodie==true) {
			die ($dir . " is not writeable in " .
			"'".$path."'<br>You should run 'chmod 777 ".$dir."'");
		} else return false;
	}
	return true;
}
?>