<?php
//require_once(I_PATH."form.class.php");

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr><td>Hallo und willkommen zum Pressesystem.<br><br>" .
						"Sie finden das Navigationsmen� immer oben links. " .
						"Falls Sie keine Maus haben oder diese nicht benutzen k�nnen" .
						"oder m�chten, k�nnen Sie " .
						"dieses System mittels Accesskeys (Tasten in Kombination mit" .
						"der ALT Taste) per Tastatur bedienen.<br>" .
						"Dr�cken Sie ALT + 5 f�r weitere Informationen dazu." .
						"<br><br>" .
						"Viel Spa� w�nscht" .
						"<br> Christoph</td></tr>
            </table>";


// Fill into Template
require_once(I_PATH . "template.class.php");
$T 			= new template();
$T->add_title("Startseite Pressesystem");

$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();
?>

