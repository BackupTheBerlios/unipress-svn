<?php
// $Id$

// Bereich laden
require_once(I_PATH . "press_sites.class.php");
$PSITES		= new press_sites( &$SQL, &$DBG );
$PSITES->set_prefix( $VAR['db']['tableprefix'] );

$sites_list = $PSITES->show_list("edits");	// liste mit "klickbaren" elementen generieren für ziel "edits"

$html['content']  = "<table summary=\"form table (as layout)\" width=\"100%\">" .
			"<tr>" .
				"<td  width=\"180\" class=\"heading\" colspan=\"3\">Bitte w&auml;hlen Sie den Bereich, den Sie editieren wollen.</td>" .
			"</tr>" .
			"<tr>" .
				"<td id=\"tc4\" valign=\"top\">" .
				$sites_list."" .
				"</td>" .
			"</tr>" .
		"</table>" .
		"</div>" .
		"<div class=\"tablelayer\">" .
		"<table summary=\"form table new button\" width=\"100%\">" .
			"<tr>" .
				"<td  width=\"180\"colspan=\"3\"  class=\"heading\">" .
				"oder <a href=\"?menu=edits&id=0\" accesskey=\"n\">erstellen Sie einen <u><i>n</i></u>euen Bereich</a>" .
				"</td>" .
			"</tr>" .
		"</table>";

// Fülle Template
require_once(I_PATH . "template2.class.php");
$T= new template( & $DBG );
$T->add_title("Bereiche &Auml;ndern");
$T->add_js("js/sitelist.js");
$T->add_css("css/nentry.css");
$T->add_menu($menu_links); 			// werden in index.php erzeugt
$T->add_content($html['content']);	// hart inhalte hinzu
$T->show(); // anzeigen
?>

