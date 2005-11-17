<?php
require_once(I_PATH . "template.class.php");
require_once(I_PATH . "press_sites.class.php");
$T			= new template();
$PSITE		= new press_sites( $SQL );
$PSITE->set_debugger( $DBG );

$formerror = array("bereichsname"=>"", "kuerzel"=>"");


// request infos
$id	=		init("id","gp",0); // cast fÃ¼r member method
// $preset		= $PSITE->get_info( $id );
$send	=	init("send");

	// debug
	$DBG->watch_var("_id",$id);
	$DBG->watch_var("_preset", $preset);

// check input
// name, nicht leer, mind. 3 
$Rname		= init("name","p","");
$Rkuerzel	= init("kuerzel","p","");
$Ranew		= init("anew","p","nein");

if ($id==0) {
	// neuer eintrag
	$what_to_do = "neu anlegen";
	$preset['name'] = "";
	$preset['kuerzel'] = "";
	$T->add_js_startup("document.forms[0].name.select();");
	if ($send!=0) {
		$new_id = $PSITE->add($Rname, $Rkuerzel);
		if ($new_id == false) {
			$formerror['main'] .= "Fehler: ".$PSITE->error_msg;
		} else {
			$preset	= $PSITE->get_info( $new_id );
			$status = "Bereich '".$preset['name']."' erfolgreich angelegt.";
			// in dieser maske bleiben?
			if ($Ranew=="nein") {
				$T->add_refresh(3,"?menu=sites");
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
		if ($PSITE->edit($id, $Rname, $Rkuerzel) == false) {
			$formerror['main'] .= "Fehler: ".$PSITE->error_msg;
		} else {
			$status = "Änderung erfolgreich.";
			//$preset	= $PSITE->get_info( $id );
			$T->add_refresh(3,"?menu=sites");
		}
	}
	 // preset
	$preset		= $PSITE->get_info( $id );
	if($preset==false) {$formerror['main'] = $PSITE->error_msg; }
}
 



// fill template head
$T->add_title("Bereich ".$preset['name']." Ändern");
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
											"help"=>"Bitte wählen Sie einen gut beschreibenden Namen.\nz. Bsp.: Institut für Sonnenforschung"
											),
							$formerror[$name], 
							$preset[$name]
						);
//Kuerzel
$name = "kuerzel";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>K</u>ürzel", 
											"key"=>"k", 
											"type"=>"text",
											"help"=>"Vergeben Sie ein Kürzel. Unter diesem Kürzel wird später die Bereichsübersicht abrufbar sein.\nz. Bsp.: IFS",
										
											) ,
							$formerror[$name], 
							$preset[$name]
						);
// another new entry? (if it is a new)
if ($id==0) {
	$name = "anew";
	$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"In dieser <u>M</u>aske bleiben?", 
											"key"=>"m", 
											"type"=>"yn_radio",
											"help"=>"Falls Sie mehrere Bereiche anlegen wollen, wählen sie -Ja-, damit Sie nicht zur Übersicht umgeleitet werden.",
										
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