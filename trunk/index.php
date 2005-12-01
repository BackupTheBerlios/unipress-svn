<?php
/*
 * press system
 *
 * index.php - mainfile 
 *
 * Christoph Becker <mail@ch-becker.de>
 *
 * June-August 2005
 * Version 0.1.1 
 * $Id$
 */
 
 /* changes:
 * 
 */
 
 /* todo
 *
 * auth
 * user management
 * site management
 * source management
 * entry management
 * normal user-backend
 * frontent-templates (smarty)
 */
        
// do main initialization (db, db-checkup, debug, smarty)
include("init.php");

// testing
// create user
#ok $PRESS->create_user("theo","testtest");

/* auth */
#if( $PRESS->auth("theo","testtest") ) echo "ok"; else echo "nich ok";

/*session_start();

$user	=	init("user","spg");	//	extract user from session or post
$pass	=	init("pass","p",session_id());		//	extract pass from .. or use ssion_id
if (!$PRESS->auth($user,$pass)){	
	die("auth first!");	
}
session_register("user", $user);                                  
*/

	/* the main thing */
	$o	=	""; // init output buffer;
	
	/* menu */
	require(I_PATH."menu.inc.php");
			
	/* end menu */
	
	/* end main */

// output
//echo XHTMLHEAD ."<pre>" .$o ."<pre>". XHTMLFOOT;

// stop debugging, close mysql-connection if wanted
include("init.php");
?>
