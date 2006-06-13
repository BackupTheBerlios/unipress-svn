<?php
/* $Id$
 * press system
 *
 * index.php - mainfile 
 *
 * Christoph Becker <mail@ch-becker.de>
 *
 * June 2005 - March 2006
 * 
 * 
 */
        
// do main initialization (db, db-checkup, debug, smarty)
include("init.php");
require_once(I_PATH."auth.class.php");
require_once(I_PATH."press_user.class.php");
$PU	= new press_user( &$SQL , &$DBG, new auth());

// Anmeldung - session
$DBG->send_message("* * * Anmeldung * * *");// <--- Debug-Meldung
$authenticated = $PU->auth();// <- Anmeldedaten prüfen (Formular oder Session)

// show login form
if($authenticated==false)  {
	include (T_PATH."login.php");// <-Anmeldeformular
	$DBG->send_message("* * * Anmeldung zurückgewiesen * * * died.");
	// stop debugging, close mysql-connection if wanted
	include("init.php");
	die();
}
$DBG->send_message("* * * Anmeldung ok * * *");
// ------------------------
// Anmeldung - ende

// menu -> Hier wird entschieden, was gemacht werden soll.
require(I_PATH."menu.inc.php");

// stop debugging, close mysql-connection if wanted
include("init.php");
?>
