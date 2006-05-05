<?php
// $Id$
// Benutzer editieren etc.

require_once(I_PATH . "template2.class.php");
//require_once(I_PATH . "press_user.class.php");
//require_once(I_PATH . "auth.class.php");
require_once(I_PATH . "press_sites.class.php");
//$AUTH 		= new auth();
$T			= new template(&$DBG);
$PU			= new press_user( &$SQL , &$DBG, new auth()); //&$AUTH);
$PS			= new press_sites( &$SQL , &$DBG);
#$DBG->watch_var("Files", $_FILES);

$DBG->send_message("Initialisierung ok");


// request infos
$id	=		init("id","gp",0); // cast for member method
$send	=	init("send");

// password policy
$pass_policy = $PU->get_password_policy();

// debug
$DBG->watch_var("_id",$id);
$DBG->watch_var("_preset", $preset);

// check input
$Ranew		= init("anew","p","nein");

$DBG->send_message("Inputcheck ok");

// prefill
if ($id==0) {
	// neuer eintrag
	$what_to_do = "neu anlegen";
	$preset['name'] = "";
	$preset['main']['auth'] = 1;
	$optinality['pass'] = (init("auth","r",1)==0) ? false : true;
	
	
	$T->add_js_startup("document.forms[0].name.select();");
	if ($send!=0) {
	//echo "create.";

	}	
} else {
	// alter eintrag
	// auch editiert?
	$what_to_do = "editieren";
	$optinality['pass'] = true;
	
	if ($send!=0) {
		//	echo "edit.";
	}
	 // preset
	$preset		= $PU->get_info( $id );
	
}
 



// fill template head
$T->add_title("Benutzer ".$preset['name']." ".$what_to_do);
/*$T->add_js("js/site_check.js");
$T->add_js("js/site_examples.js");*/
$T->add_css("css/nentry.css");
$T->add_hidden_field("id",$id);
$T->add_hidden_field("menu","newu");// wo geht die reise hin


// PREFILL -------------------------------------------------------------------
// head
$T->leadin( 	"<div class=\"error\">" . $formerror['main']."</div>" .
						"<div class=\"status\">" . $status . "</div>" .
						"<table summary=\"form table (as layout)\" width=\"100%\">\n" .
						"<tr>
							<td  width=\"180\" class=\"heading\" colspan=\"3\">Benutzer ".$what_to_do."</td>
						</tr>");
// Name
$name = "name";
$html['content']  .=	$T->add_form_field( array("name"=>$name, 
											"label"=>"<u>B</u>enutzername", 
											"key"=>"B", 
											"type"=>"text",
											"help"=>"Bitte w&auml;hlen Sie einen Benutzernamen den Sie importieren oder anlegen m&ouml;chten.\nZum Beispiel: cb098",//Bitte w&auml;hlen Sie einen gut beschreibenden Namen.\nz. Bsp.: Institut f&uuml;r Sonnenforschung"
											"prefill"=>	$preset['main'][$name]
											)
						);
// Passwort
$name = "pass";
$html['content']  .=	$T->add_form_field( array("name"=>$name, 
											"label"=>"<u>P</u>asswort", 
											"key"=>"p", 
											"type"=>"password",
											"help"=>"Nur notwendig, wenn ein lokaler Benutzer angelegt werden soll!\nBitte wählen Sie ein Passwort mit mindestens ".$pass_policy['min']." Zeichen. \nZum Beispiel: ".uniqid(),
											"optional"=>$optinality[$name], // optional?
											"minmax"=>$pass_policy,
											"prefill"=>$preset['main'][$name]
											) 
						);

if ($id==1) { /* SUPERUSER */
	
// Site Select
$name = "dummy";	
$html['content']  .=	$T->add_form_field( array("name"=>$name, 
											"label"=>"<u>A</u>dministrator, Authentifikation und Bereiche", 
											"key"=>"A", 
											"type"=>"dummy",
											"optional"=>true,
											"help"=>"Dieser Administrator ist der Superuser." .
													" Er hat immer Zugriff auf alle Bereiche und wird" .
													" lokal authentifiziert. Des Weiteren bleibt er immer Superuser.",
											) 
						);
} else {
// Admin
$name = "admin";
$html['content']  .=	$T->add_form_field( array("name"=>$name, 
											"label"=>"<u>A</u>dministrator", 
											"key"=>"A", 
											"type"=>"yn_radio",
											"help"=>"Wählen Sie 'ja' wenn dieser Benutzer Administratorrechte erhalten soll. Im Regelfall ist nicht nicht n&ouml;tig!",
											"prefill"=>$preset['admin']
											) 
						);

// Auth
$name = "auth";
$html['content']  .=	$T->add_form_field( array("name"=>$name, 
											"label"=>"Authentifizierungs<u>v</u>erfahren", 
											"key"=>"v", 
											"type"=>"radio",
											"help"=>"Wählen Sie, ob ein Benutzer lokal angelegt werden, oder mit Hilfe anderer Systeme authentifiziert werden soll.\nZum Beispiel: IMAP bedeutet, dass der Benutzer über einen IMAP-Server identifiziert wird.",
											"values"=>$PU->get_auth_list(),
											"prefill"=>$preset['main']['auth']
											) 
						);
// Site Select
$name = "sites[]";							
$html['content']  .=	$T->add_form_field( array("name"=>$name, 
											"label"=>"B<u>e</u>reiche", 
											"key"=>"e", 
											"type"=>"site_select",
											"values"=>$PS->get_all(),
											"prefill" =>$preset['sites'],
											"help"=>"W&auml;hlen Sie die Bereiche aus, auf die der Benutzer Zugriff erhalten soll.",
											) 
						);
}
						
// ok - following is bad
// another new entry? (if it is a new)
if ($id==0) {
	$name = "anew";
	$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"Einen <u>w</u>eiteren Benutzer anlegen?", 
											"key"=>"m", 
											"type"=>"yn_radio",
											"help"=>"Falls Sie mehrere Benutzer anlegen wollen, w&auml;hlen sie -Ja-, damit Sie nicht zur &Uuml;bersicht umgeleitet werden.",
											"value"=>init("anew","r","nein")
											) 
						);	

	// foot
	$T->append($T->form_button("ex__ok_reset") . "</table>");
} else {		
// foot		
	$T->append($T->form_button("ok_reset") . "</table>");
}
// Fill into Template --------------------------------------------------------------

$T->add_form("index.php", true); // after form:button!
$T->add_menu($menu_links);

if ($send==1) {
	$r = $T->check_form(); 
	if ($r) {
		echo "checked and ok (".$r.")".
			"<br>[MUE01] please implement ".$what_to_do;
		//$DBG->watch_var("CheckForm",$r);
		if($PU->import($r,$id) && $PU->write()) 
			echo "<br>[MUE02] alles ok ($id) ".$PU->error_cmsg; else echo "<br>[MUE03] fehler ($id).. ".$PU->error_cmsg;		
	}
} else {
	$T->show();
}
?>