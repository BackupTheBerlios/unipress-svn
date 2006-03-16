<?php
// User or Admin menu?
$menu	=	($_SESSION['admin']==true) ? parse_ini_file(C_PATH.'menu.ini',1):parse_ini_file(C_PATH.'user_menu.ini',1) ;

// detect menupoint
// first GET value
$actual  =	init("menu","r","");
$DBG->watch_var("menu key exists",array_key_exists($actual,$menu ));

if (array_key_exists($actual,$menu )) $menu	=	&$menu[$actual]; 
else {
	if ($actual!="") $ERRLOG->entry("Access violation. Normal user tried to access: $actual");
	$menu	=	&$menu['main'];
	$actual = "main";
}

// build links
$DBG->watch_var("act. menu",$actual);	// write var to debug log
$DBG->watch_var("menu",$menu);
reset($menu);
$i=0;
$menu_links="<a href='index.php' accesskey='0'></a><a href='index.php?menu=ahelp' accesskey='5'></a>";
while (list($key, $val) = each($menu)) {
	if ($key==$actual) {$val = "<strong>".$val."</strong>";}
	$i++;
    $menu_links.= " <a href='?menu=$key' accesskey='$i'>$val</a><br />";
}
// posts
while (list($key, $val) = each($_REQUEST)) {
    $o.= "<br>$key := $val";
}
$o="";

// menu
$DBG->watch_var("Request", $_REQUEST);
$DBG->watch_var("Files", $_FILES);
switch ($actual) {
	case "ahelp":
		//echo XHTMLHEAD . $menu_links . XHTMLFOOT;
		include (T_PATH."access_help.php");
	break;
	/* ----- main entries ----- */
    default:
	case "main":
		//echo XHTMLHEAD . $menu_links . XHTMLFOOT;
		include (T_PATH."main.php");
	break;
	
	case "user":
	case "edituser":
		include (T_PATH."user_overview.php");
	break;
	

	
	
	case "sites":
		include (T_PATH."sites_overview.php");
	break;
	
	case "entry":
		// test input
		include (T_PATH."entries_edit.php");
	break;
	
	/* ----- sites ----- */
	case "news":
	case "edits":
		// edit an old site
		include (T_PATH."sites_edit.php");
	break; 
	
	/* ----- user ----- */
	case "newu":
	case "editu":
	case "deluser":
		// edit an old site
		include (T_PATH."user_edit.php");
	break;
	
	/* ----- nentry ----- */
	case "nentry":
		// new entry form was send
		// test and throw errors
				// test input
		include (T_PATH."entries_edit.php");
	break;
}
?>
