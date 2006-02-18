<?php
// $Id$

require_once(I_PATH . "template.class.php");
require_once(I_PATH . "press_sites.class.php");
$T			= new template($DBG);
$PSITE		= new press_sites( $SQL , $DBG);
$PSITE->set_prefix( $VAR['db']['tableprefix'] );
#$DBG->watch_var("Files", $_FILES);

$formerror = array("bereichsname"=>"", "kuerzel"=>"");


// request infos
$id	=		init("id","gp",0); // cast für member method
// $preset		= $PSITE->get_info( $id );
$send	=	init("send");

	// debug
	$DBG->watch_var("_id",$id);
	$DBG->watch_var("_preset", $preset);

// check input
// name, nicht leer, mind. 3 
$Rname		= init("name","p","");
$Rkuerzel	= init("kuerzel","p","");
$Rhead		= init("head","p","");
$Rfoot 		= init("foot","p","");
$Ranew		= init("anew","p","nein");

if ($id==0) {
	// neuer eintrag
	$what_to_do = "neu anlegen";
	$preset['name'] = "";
	$preset['kuerzel'] = "";
	$preset['head']	= "";
	$preset['foot'] = "";
	$T->add_js_startup("document.forms[0].name.select();");
	if ($send!=0) {
		$new_id = $PSITE->add($Rname, $Rkuerzel, $Rhead, $Rfoot);
		if ($new_id == false) {
			$formerror['main'] .= "Fehler: ".$PSITE->error_msg;
		} else {
			$preset	= $PSITE->get_info( $new_id );
			$status = "Bereich '".$preset['name']."' erfolgreich angelegt.";
			// in dieser maske bleiben?
			if ($Ranew=="nein") {
				$T->add_refresh(0,"?menu=sites");
				$id = $new_id;	
			} else {
				$id = "";
				$preset = array();	
			}
			
		}
	}	
} else {
	// alter eintrag
	// auch editiert?
	$what_to_do = "editieren";
	if ($send!=0) {
		if ($PSITE->edit($id, $Rname, $Rkuerzel, $Rhead, $Rfoot) == false) {
			$formerror['main'] .= "Fehler: ".$PSITE->error_msg;
		} else {
			$status = "&Auml;nderung erfolgreich.";
			//$preset	= $PSITE->get_info( $id );
			$T->add_refresh(0,"?menu=sites");
		}
	}
	 // preset
	$preset		= $PSITE->get_info( $id );
	if($preset==false) {$formerror['main'] = $PSITE->error_msg; }
}
 



// fill template head
$T->add_title("Bereich ".$preset['name']." ändern");
$T->add_js("js/site_check.js");
$T->add_js("js/site_examples.js");
$T->add_css("css/nentry.css");
$T->add_hidden_field("id",$id);
$T->add_hidden_field("menu","edits");


// PREFILL -------------------------------------------------------------------
// head
$html['content']  = 	"<div class=\"error\">" . $formerror['main']."</div>" .
						"<div class=\"status\">" . $status . "</div>" .
						"<table summary=\"form table (as layout)\" width=\"100%\">\n" .
						"<tr>
							<td  width=\"180\" class=\"heading\" colspan=\"3\">Bereich ".$what_to_do."</td>
						</tr>";
// Name
$name = "name";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"Bereichs<u>n</u>ame", 
											"key"=>"n", 
											"type"=>"text",
											"help"=>"Bitte w&auml;hlen Sie einen gut beschreibenden Namen.\nz. Bsp.: Institut f&uuml;r Sonnenforschung"
											),
							$formerror[$name], 
							$preset[$name]
						);
//Kuerzel
$name = "kuerzel";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>K</u>&uuml;rzel", 
											"key"=>"k", 
											"type"=>"text",
											"help"=>"Vergeben Sie ein K&uuml;rzel. Unter diesem K&uuml;rzel wird sp&auml;ter die Bereichs&uuml;bersicht abrufbar sein.\nz. Bsp.: IFS",
										
											) ,
							$formerror[$name], 
							$preset[$name]
						);
//Head
$name = "head";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"K<u>o</u>pfbereich (XHTML)", 
											"key"=>"O", 
											"type"=>"text",
											"help"=>"Falls die Presseseite im Institutslayout erscheinen soll und nicht eingebunden wird, ist es notwendig, auf den Dokumentenkopf zu verweisen\nz. Bsp.: http://mein.institut.de/kopf.html",
										
											) ,
							$formerror[$name], 
							$preset[$name]
						);
//Foot
$name = "foot";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>F</u>u&szlig;bereich (XHTML)", 
											"key"=>"O", 
											"type"=>"text",
											"help"=>"Verweis auf den Dokumentfu&szlig;\nWeitere Informationen finden Sie in der Hilfe.\nz. Bsp.: http://mein.institut.de/fuss.html",
										
											) ,
							$formerror[$name], 
							$preset[$name]
						);

// another new entry? (if it is a new)
if ($id==0) {
	$name = "anew";
	$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"Einen <u>w</u>eiteren Bereich anlegen?", 
											"key"=>"m", 
											"type"=>"yn_radio",
											"help"=>"Falls Sie mehrere Bereiche anlegen wollen, w&auml;hlen sie -Ja-, damit Sie nicht zur &Uuml;bersicht umgeleitet werden.",
										
											) ,
							$formerror[$name], 
							$Ranew
						);	
}					
// foot		
$html['content']  .= $T->form_button("ex__ok_reset") . "</table>";
/*"<div id=\"sendbuttons\">" .

"</div>"
*/
// Fill into Template --------------------------------------------------------------

$T->add_form("index.php", true); // after form:button!
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->show();

?>