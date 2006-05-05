<?php
// $Id$
// Suchformular
require("init.php");
require_once("include/init.inc.php");
require_once("include/form.class.php");
require_once("include/press_sites.class.php");
/* Einstllungen */
$abs_path = "http://localhost/unipress/suche.php"; // absoluter Pfad zum Such-Script

/* Bitte ab hier nichts ändern */
// Instanzieren
$PS = new press_sites(&$SQL, &$DBG);

// Gesendet?
$send 		= init("send","r",FALSE);
$fulltext 	= init("fulltext","r",FALSE);
$range 		= init("range","r","after");
$date 		= init("date","r",strftime("%d.%m.%Y",time()-3600*24*7));
$sites		= init("sites","r",false);

$xtras = "Suche! ".$fulltext." ".$range." ".$date." ".var_export($sites);

if(!empty($fulltext)) {
	if(!empty($where)) $where .= " AND ";
	$where .= " (e.title LIKE '%".clean_in(fulltext)."%' OR k.keyword LIKE '%".clean_in(fulltext)."%') ";
	$join  .= " LEFT JOIN press_ke_rel AS kerel ON kerel.eid=e.id LEFT JOIN press_keywords AS k ON kerel.kid=k.id ";
	
}


// head
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n" . 
		"<html>" . 
		"	<head>" . 
		"		<title>Suche Pressesystem</title>\n" . 
		"		<meta name=\"author\" content=\"Christoph Becker\" >" . 
		"		<meta name=\"keywords\" content=\"Pressesystem Suche Uni Rostock Fakultät für Informatik und Elektrotechnik\" >" . 
		"		<meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\" >" . 
		"		<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" >" . 
		"		<style>" . 
		"			.presstitle{ font-family:Verdana,Geneva,sans;" . 
		"						 font-size:14pt;" . 
		"						 color:black; " . 
		"			}" . 
		"			td {font-family:Verdana,Geneva,sans;" . 
		"						font-size:10pt;" . 
		"						color:black;" . 
		"						margin-top:10pt;" . 
		"						" . 
		"			}" . 
		"			td.presssearchtitle {" . 
		"					font-weight: bold;" . 
		"			}" . 
		"			a.presslink:hover {" . 
		"					color:red;	" . 
		"			}" . 
		"		</style>" . 
		"		" . 
		"	</head>" . 
		"	<body link=\"#090851\" text=\"#000000\" bgcolor=\"#ffffff\">";
// content
echo	$xtras."<div class='presstitle'>Suche</div>" . 
		"<div class='presshint'>Bitte grenzen Sie Ihre Suche nach belieben ein.</div>".
		"<form name=\"sform\" method=\"POST\" action =\"".$abs_path."\">" .
			"<table>" .
			"<tr><td colspan='2' class='presssearchtitle'>Volltextsuche</td><td>&nbsp;</td></tr>" .
			"<tr><td>&nbsp;</td><td colspan='2'>" .
				FORM :: text("fulltext",$fulltext,"", 70) .
			//"<input type='text' name='fulltext'>" .
			"</td></tr>" .
			"<tr><td colspan='2' class='presssearchtitle'>Zeitraum des Erscheinens</td><td>&nbsp;</td></tr>" .
			"<tr><td align='right'><table><tr><td>".FORM :: radio("range","before","",$range)."vor</td>" .
									"<td rowspan='2'>".FORM :: text ("date",$date,"",10)."</td></tr>" 
										."<tr><td>".FORM :: radio("range","after","",$range)."nach </td></tr>" .
										"</table></td>".
										"<td colspan='2'>" .
										"<b>oder</b> ".FORM :: radio("range","all","",$range)."alle Einträge durchsuchen</td>" .

			"<tr><td valign='top'  class='presssearchtitle' colspan='2'>Suche auf bestimmte Institute beschränken</td><td>&nbsp;</td></tr>" .
			"	<td>&nbsp;</td><td>".
					FORM :: select("sites[]", $PS->get_all(1), $sites, 6)
				."</td></tr>" .
			"<tr><td>&nbsp;</td><td colspan='2'>".FORM :: submit("send","suche!")."</td></tr> "
			."</table>"
					. "</form>" . 
		"		</div>";
// footer 
echo	"	</body>" . 
		"</html>"; 

// close debug		
require("init.php");
