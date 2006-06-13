<?php
//require_once("init.php");

function updates() {
	return; // doesn't work

	$homeurl = "http://ch-becker.de/software/unipress.html";
	
	$latest = wget($homeurl);
	$infos  = wget($homeurl.$latest);
	
	if (VERSION!=$latest) {
		return "Ihre Version von unipress ist veraltet." .
				"<pre>\nIhre Version: " . VERSION .
			"\nNeueste Version: " . $latest .
			"\nInformationen: " . $infos .
			"</pre>";
	} else {
		echo "Sie setzen die neueste Version ein.";
	}
}

?>