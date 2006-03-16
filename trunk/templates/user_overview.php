<?php
// $Id$
// Benutzerübersicht

require_once(I_PATH . "auth.class.php");
require_once(I_PATH . "press_user.class.php");
$PU		= new press_user( &$SQL, &$DBG, new auth() ); 				// $SQL has to be a valid MySQL-Object
//$PU->set_prefix( $VAR['db']['tableprefix'] );

$sites_list = $PU->show_list("editu");	//get clickable list to aim-site

$html['content']  = " 
			<table summary=\"form table (as layout)\" width=\"100%\">
				<tr>
					<td  width=\"180\" class=\"heading\" colspan=\"3\">Bitte w&auml;hlen Sie den Benutzer, den Sie editieren wollen.</td>
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
						oder <a href=\"?menu=editu&id=0\" accesskey=\"n\">erstellen Sie einen <u><i>n</i></u>euen Benutzer</a>	
					</td>
				</tr>
            </table>";


// Fill into Template
require_once(I_PATH . "template.class.php");
$T 			= new template( & $DBG );
$T->add_title("Benutzer &Auml;ndern");
$T->add_js("js/sitelist.js");
$T->add_css("css/nentry.css");
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();
?>

