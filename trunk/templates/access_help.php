<?php
//require_once(I_PATH."form.class.php");

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr><td>Bedienhilfe zum Pressesystem<br><br>" .
						"Das System kann zusätzlich zur Maus, auch mit der Tastatur bedient werden. " .
						"Folgende Tastenkombinationen sind verfügbar:<ul>" .
						"<li>ALT+0 führt Sie auf diese Seite und somit ins Hauptmenü</li>" .
						"<li>ALT+1,2,3 für die Menüpunkte</li>" .
						"<li>ALT+7 füllt ein Formular mit Beispieldaten</li>" .
						"<li>ALT+8 sende ein Formular, alternativ zur Return-Taste</li>" .
						"<li>ALT+9 löscht Formulardaten auf Nachfrage</li>" .
						"<li>ALT+5 ruft diese Seite auf.</li></ul>" .

						"In den Formularen können Sie mit der Tabulator-Taste navigieren. " .
						"<br><br>Falls Sie jetzt also einen neuen " .
						"Bereich anlegen möchten, klicken sie auf den Menüpunkt " .
						"oder drücken gleichzeitig die 'ALT' und '1' Taste.<br><br>" .
						"Im den Formularen sind die sogenannten Accesskeys in der " .
						"Feldbezeichnung <u>u</u>nterstrichen dargestellt.<br><br>" .
						"Viel Spaß wünscht" .
						"<br> Christoph</td></tr>
            </table>";


// Fill into Template
require_once(I_PATH . "template.class.php");
$T 			= new template();
$T->add_title("Bedienhilfe");

$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();
?>

