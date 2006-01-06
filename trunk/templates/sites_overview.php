<?php
//require_once(I_PATH."form.class.php");

// Bereich laden
require_once(I_PATH . "press_sites.class.php");
$PSITES		= new press_sites( $SQL, $DBG ); 				// $SQL has to be a valid MySQL-Object
$PSITES->set_prefix( $VAR['db']['tableprefix'] );

$sites_list = $PSITES->show_list("edits");	//get clickable list to aim-site

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr>
					<td  width=\"180\" class=\"heading\" colspan=\"3\">Bitte wählen Sie den Bereich, den Sie editieren wollen.</td>
				</tr>
				<tr>
					<td id=\"tc4\" width=\"20\" valign=\"top\">
				  		".$sites_list."
					</td>
				</tr>
            </table>
         </div>
         
         <div class=\"tablelayer\"> 
			<table summary=\"form table new button\" width=\"100%\">
				<tr>
					<td  width=\"180\"colspan=\"3\"  class=\"heading\">
						oder <a href=\"?menu=edits&id=0\" accesskey=\"n\">erstellen Sie einen <u><i>n</i></u>euen Bereich</a>	
					</td>
				</tr>
            </table>";


// Fill into Template
require_once(I_PATH . "template.class.php");
$T 			= new template();
$T->add_title("Bereiche ändern");
$T->add_js("js/sitelist.js");
$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();
?>

