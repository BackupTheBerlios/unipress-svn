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

