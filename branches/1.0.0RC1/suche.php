<?php
// $Id$
// Suchformular
require("init.php");
error_reporting(E_ALL);
require_once("include/init.inc.php");
require_once("include/form.class.php");
require_once("include/press_sites.class.php");


/* Einstllungen */
// wirklich nur zum debuggen einsetzen... erzeugt Ausgabe im normalen Fenster!!!
$search_debug = false; 
// absoluter http:// Pfad inkl. suche.php zum Such-Script
$abs_path = "http://localhost/unipress/suche.php"; 

/* Bitte ab hier nichts Aendern */
// Instanzieren mit Datenbank und Debugger
$PS = new press_sites(&$SQL, &$DBG);

// Gesendet?
$send 		= init("send","r",FALSE);
$fulltext 	= init("fulltext","r",FALSE);
$range 		= init("range","r","after");
$date 		= init("date","r",strftime("%d.%m.%Y",time()-3600*24*7));
$sites		= init("sites","r",FALSE);

// init
$join = "";
$where = "";

// sollte man mal umstrukturieren..
if ($send) {
		
	// Volltext
	if(!empty($fulltext)) {
		if(!empty($where)) $where .= " AND ";
		$where .= " (e.title LIKE '%".clean_in($fulltext)."%' OR k.keyword LIKE '%".
					clean_in($fulltext)."%') ";
		$join  .= " LEFT JOIN press_ke_rel AS kerel ON kerel.eid=e.id " .
					"LEFT JOIN press_keywords AS k ON kerel.kid=k.id ";
	}
	
	// Datumsfunktion
	$d = set_date($date); 
	
	switch ($range) {
		default:
		case 'all':
			
		break;
		
		case 'before':
			if(!empty($where)) $where .= " AND ";
			$where .= " (date <= '".$d."')";
		break;
		
		case 'after':
			if(!empty($where)) $where .= " AND ";
			$where .= " (date >= '".$d."')";
		break;
		
	}
	
	// Bereiche
	if(!empty($sites)) {
		if(!empty($where)) $where .= " AND ";
		$where .=" s.sid IN (". implode(",", $sites) .") "; 
		$join  .=" LEFT JOIN press_se_rel AS s ON e.id=s.eid "; 
	}
	
	// Ergebnisbeschr‰nkung
	if(strlen($where)<2) {
		$where = " LIMIT 50;";
	} else {
		$where = "WHERE ". $where;
	}
	
	// Suche itself
	$sql = "SELECT DISTINCT(e.id), title, filename, date, link FROM press_entries AS e ". $join . " " .$where;
	$res = $SQL->select ( $sql );
}

// head
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n" . 
		"<html>" . 
		"	<head>" . 
		"		<title>Suche Pressesystem</title>\n" . 
		"		<meta name=\"author\" content=\"Christoph Becker\" >" . 
		"		<meta name=\"keywords\" content=\"Pressesystem Suche Uni Rostock Fakult‰t f¸r Informatik und Elektrotechnik\" >" . 
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
if($search_debug == true) {
	echo "<pre>Sites_selected: ";
	var_export($sites);
	echo "\nSQL: $sql \n Results:\n";
	var_export($res);
	echo	"\n<hr noshade=noshade />";
}
echo 		"<div class='presstitle'>Suche</div>" . 
		//"<div class='presshint'>Bitte grenzen Sie Ihre Suche nach belieben ein.</div>".
		"<form name=\"sform\" method=\"POST\" action =\"".$abs_path."\">" .
			"<table>" .
			"<tr><td colspan='2' class='presssearchtitle'>Volltextsuche</td><td>&nbsp;</td></tr>" .
			"<tr><td>&nbsp;</td><td colspan='2'>" .
				FORM :: text("fulltext",$fulltext,"", 70) .
			//"<input type='text' name='fulltext'>" .
			"</td></tr>" .
			"<tr><td colspan='2' class='presssearchtitle'>Zeige alle Eintr‰ge</td><td>&nbsp;</td></tr>" .
			"<tr><td align='right'><table><tr><td>".FORM :: radio("range","before","",$range)."vor</td>" .
									"<td rowspan='2'>".FORM :: text ("date",$date,"",10)."</td></tr>" 
										."<tr><td>".FORM :: radio("range","after","",$range)."nach </td></tr>" .
										"</table></td>".
										"<td colspan='2'>" .
										"<b>oder</b> ".FORM :: radio("range","all","",$range)."egal, wann sie erstellt wurden</td>" .

			"<tr><td valign='top'  class='presssearchtitle' colspan='2'>Suche auf bestimmte Institute beschr‰nken</td><td>&nbsp;</td></tr>" .
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

//----
function set_date($d, $dateform	=	"german") {
	//$ret = $d;

		//$dateform	=	"german"; 		// what do i suppose, which form i got
		$buffer 	=	$d;				// save for error_msg
		$preset_year=	strftime("%Y");	// with current year
		
		$d = trim($d);
		if (strlen($d)<3) {
			print( "Datum ($d) ungueltig da zu kurz.");	
		}	
		// make some tests
		if (!ereg("\.",$d)) $dateform = "other"; // sonst: german
		
		// transform into englisch form with "-"	
		$d = ereg_replace("\.|/","-",$d);
		
		// split
		$d = explode("-", $d);
		$c = count($d);
		
		if ($c<2 || $c>3) {
			die( "Datum ($buffer) ung√ºltig da zuwenige/zuviele Trennzeichen ($c).");
		}

		switch ($dateform) {
			case 'german':	// i got dd.mm.yy? make: yy.mm.dd
				switch ($c) {
					case 2: // dd-mm into mm-dd and add Year
						$day		=	$d[0];
						$month		=	$d[1];
						$year		=	$preset_year; // current year?
					break;
					case 3:
						$day		=	$d[0];
						$month		=	$d[1];
						$year		=	$d[2];
						switch (strlen($year)) {
							case 2:
								$year = substr(0,2,strftime("%Y")).substr(-2,2,$d[2]);
							break;
							case 4:
								$year = $d[2];
								
							break;
							default:
								$year		=	$preset_year; // current year?
						}
					break;
				}
			break;
			default:
				switch ($c) {
					case 2: // no transform
						$day		=	$d[1];
						$month		=	$d[0];
						$year		=	$preset_year; // current year?
					break;
					case 3:
						$day		=	$d[2];
						$month		=	$d[1];
						$year		=	$d[0];
						switch (strlen($year)) {
							case 2:
								$year = substr(0,2,strftime("%Y")).substr(-2,2,$d[0]);
							break;
							case 4:
								$year = $d[0];
								
							break;
							default:
								$year		=	$preset_year; // current year?
						}
					break;
				}
				
		}
	
		if (checkdate(1*$month, 1*$day, 1*$year)!=true) 
			print("Dieses Datum existiert nicht: $day $month $year");
		//ok
		$ret = sprintf("%4d-%02d-%02d", $year, $month, $day);

		return $ret; // return for checking or as true
		
	}
