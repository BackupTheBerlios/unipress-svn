<?php
// $Id$
// Startseite nach Anmeldung
//
//require_once("include/updatemanager.php");
//
$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr><td>" .
						"Hallo und Willkommen zum Pressesystem." .
						"<br><br>" .
						"Sie finden das Navigationsmen&uuml; immer oben links. " .
						"<p>" .
						"Hinweis: Sie werden nicht automatisch abgemeldet. " .
						"Um sich abzumelden klicken " .
						"Sie bitte auf 'Abmelden' oder schlie&szlig;en Sie Ihren Browser.</p>" .
						
						"Viel Spa&szlig;!" .
						"<p>&nbsp;</p>" .
						"Fehler (Bugs) melden Sie bitte über folgenden Link: " .
						"<a href='http://developer.berlios.de/bugs/?group_id=5318' target='_blank'>" .
						"http://developer.berlios.de/bugs/?group_id=5318</a> oder direkt als " .
						" <a href='http://developer.berlios.de/sendmessage.php?touser=20628'>Nachricht an den Entwickler</a>." .
				"</td></tr>" .
				"<tr><td> "
				 //. updates()
				  ."</td></tr>" .
				"</table>";


// Fill into Template
require_once(I_PATH . "template2.class.php");
$T= new template( &$DBG );
$T->add_title("Startseite Pressesystem");

$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();

?>

