<?php
//require_once(I_PATH."form.class.php");

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr><td>Bedienhilfe zum Pressesystem<br><br>" .
						"Das System kann zus�tzlich zur Maus, auch mit der Tastatur bedient werden. " .
						"Folgende Tastenkombinationen sind verf�gbar:<ul>" .
						"<li>ALT+0 f�hrt Sie auf diese Seite und somit ins Hauptmen�</li>" .
						"<li>ALT+1,2,3 f�r die Men�punkte</li>" .
						"<li>ALT+7 f�llt ein Formular mit Beispieldaten</li>" .
						"<li>ALT+8 sende ein Formular, alternativ zur Return-Taste</li>" .
						"<li>ALT+9 l�scht Formulardaten auf Nachfrage</li>" .
						"<li>ALT+5 ruft diese Seite auf.</li></ul>" .

						"In den Formularen k�nnen Sie mit der Tabulator-Taste navigieren. " .
						"<br><br>Falls Sie jetzt also einen neuen " .
						"Bereich anlegen m�chten, klicken sie auf den Men�punkt " .
						"oder dr�cken gleichzeitig die 'ALT' und '1' Taste.<br><br>" .
						"Im den Formularen sind die sogenannten Accesskeys in der " .
						"Feldbezeichnung <u>u</u>nterstrichen dargestellt.<br><br>" .
						"Viel Spa� w�nscht" .
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

