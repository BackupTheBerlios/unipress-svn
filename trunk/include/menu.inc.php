<?php
$menu	=	parse_ini_file(C_PATH.'menu.ini',1);

// detect menupoint
// first GET value
#$actual	=	array_pop(array_reverse(array_flip($_REQUEST)));
$actual  =	init("menu","pg","");
echo $pmenu;
if (array_key_exists($actual,$menu )) $menu	=	&$menu[$actual]; else $menu	=	&$menu['main'];


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
	/*
	case "user":
		include (T_PATH."_template_user.php");
	break;
	*/
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
	
	
	/* ----- nentry ----- */
	case "nentry":
		// new entry form was send
		// test and throw errors
				// test input
		include (T_PATH."entries_edit.php");
	break;
}
?>
