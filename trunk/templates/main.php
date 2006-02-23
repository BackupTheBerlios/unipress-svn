<?php
//require_once(I_PATH."form.class.php");

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr><td>Hallo und willkommen zum Pressesystem.<br><br>" .
						"Sie finden das Navigationsmenü immer oben links. " .
						"<p>Hinweis: Sie werden nicht automatisch abgemeldet. Um sich abzumelden klicken " .
				"Die bitte auf 'Abmelden' oder schließen Sie Ihren Browser.</p>" .
						
						"Viel Spaß!" .
						"</td></tr>
            </table>";


// Fill into Template
require_once(I_PATH . "template.class.php");
$T 			= new template( &$DBG );
$T->add_title("Startseite Pressesystem");

$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();
?>

