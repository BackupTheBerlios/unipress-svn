<?php
/*
 * Created on 05.09.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 // upfile = $_FILES['formfieldname'];
 // aimdir should end with /
function upload_file($aimdir, $upfile){
	GLOBAL $DBG;
	$DBG->enter_method();
	$DBG->watch_var("uploaded file:",$upfile);
		
	// uploaded file
	$uploaddir	=	$aimdir;
	$filename	=	$upfile['name'];
	$answer		=	"";
	if (move_uploaded_file($upfile['tmp_name'], $uploaddir . $upfile['name'])) {
	   $answer	=	"Datei (".$upfile['name'].") gültig und erfolgreich hochgeladen. \n";
	} else {
		$DBG->send_message("could not move uploaded file to ".$uploaddir." -> false...");
	
		echo "</pre>";
	}
	$DBG->leave_method();
	return $answer;
}

?>


