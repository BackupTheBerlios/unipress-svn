<?php
// $Id$
// Login Seite
if ($error) {
	$error = "<tr><td colspan='2' bgcolor='red'>".$error."</td></tr>";	
}

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr><td colspan='2'>" .
				"Hallo und Willkommen zum Pressesystem.<br />" .
				"Bitte melden Sie sich an." .
				
				"</td></tr>" .
				
				"<tr><td width='100'>&nbsp;</td><td> " .
				    "<form method=\"post\" action=\"".$PHPSELF."\"><table>".
				    $error .
    "<tr><td>Benutzername</td><td><input type=\"text\" name=\"username\" /></td></tr>".
    "<tr><td>Passwort</td><td><input type=\"password\" name=\"password\" />" .
    "<input type=\"hidden\" name=\"send\" value=\"1\"/>" .
    "</td></tr>".
    "<tr><td>&nbsp;</td><td><input value=\"Anmelden.\" type=\"submit\" /></td></tr></table>".
    "</form>".	
	"</td></tr></table>" . 
     "  ";


// Fill into Template
require_once(I_PATH . "template2.class.php");
$T 			= new template( & $DBG );
$T->add_title("Anmeldeseite Pressesystem");

$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();
?>

