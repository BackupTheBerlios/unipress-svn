<?php
/* $Id$
 * press system
 *
 * index.php - mainfile 
 *
 * Christoph Becker <mail@ch-becker.de>
 *
 * June 2005 - February 2006
 * Version 0.1.1 
 * 
 */
        
// do main initialization (db, db-checkup, debug, smarty)
include("init.php");
require_once(I_PATH."auth.class.php");
require_once(I_PATH."press_user.class.php");
$PU	= new press_user( &$SQL , &$DBG, new auth());

// Anmeldung - session
$DBG->send_message("* * * Anmeldung * * *");
$authenticated = $PU->auth();

// show login form
if($authenticated==false)  {
	include (T_PATH."login.php");
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
