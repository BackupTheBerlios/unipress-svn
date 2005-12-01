<?php
// $Id$
require_once(I_PATH . "template.class.php");
require_once(I_PATH . "press_entry.class.php");
require_once(I_PATH . "press_sites.class.php");
require_once(I_PATH . "form.class.php");

$T			= new template();
$PE			= new press_entry( $SQL );
$PE->set_debugger( $DBG );
$PS			= new press_sites( $SQL );


// error initializer
$formerror = array(	"bereichsname"=>"", 
					"kuerzel"=>""
					);

// was form  send?
$send	=	init("send");
 
// fill template head
$T->add_title("Presseintrag erstellen");
$T->add_css("css/nentry.css");
$T->add_hidden_field("menu","nentry");


// PREFILL -------------------------------------------------------------------
// head
$html['content']  = 	"<div class=\"error\">" . $formerror['main']."</div>" .
						"<div class=\"status\">" . $status . "</div>" .
						"<table summary=\"form table (as layout)\" width=\"100%\">\n";
// Titel
$name = "title";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>T</u>itel", 
											"key"=>"t", 
											"type"=>"text",
											"help"=>"Bitte geben Sie den Titel des Presseartikels ein.\nz. Bsp.: Rostocker Mensa gewinnt goldenen Suppentopf"
											),
							$formerror[$name], 
							$preset[$name]
						);
// stichw�rter
$name = "keywords";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>S</u>tichw&ouml;rter", 
											"key"=>"s", 
											"type"=>"text",
											"help"=>"Bitte geben Sie Stichw&ouml;rter an, die den Inhalt des Artikels gut beschreiben und trennen Sie die mit Komma oder Leerzeichen.\nz. Bsp.: Mensa, Suppentopf, Auszeichnung"
											),
							$formerror[$name], 
							$preset[$name]
						);	
// Link
$name = "link";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>L</u>ink (ohne http://)*", 
											"key"=>"l", 
											"type"=>"text",
											"help"=>"Bitte geben Sie, falls vorhanden, einen Link zu weiterf&uuml;hrenden Informationen an.\nz. Bsp.: www.rostocker-mensa.de (ohne http://)"
											),
							$formerror[$name], 
							$preset[$name]
						);
// Datei
$name = "pressfile";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>D</u>atei ausw&auml;hlen", 
											"key"=>"d", 
											"type"=>"file",
											"help"=>"Bitte laden Sie den Artikel hoch. M&ouml;gliche Formate sind jpg, png, gif, pdf und jpeg",
											) ,
							$formerror[$name], 
							$preset[$name]
						);
// Source Select
$name = "source";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>Q</u>uelle ausw&auml;hlen", 
											"key"=>"q", 
											"type"=>"source_select",
											"values"=>$PE->get_source_list(),
											"help"=>"Bitte w&auml;hlen Sie eine Quelle aus der Liste aus, oder geben Sie eine neue an.",
											) ,
							$formerror[$name], 
							$preset[$name]
						);
// new source
$name = "newsource";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>N</u>eue Quelle*", 
											"key"=>"n", 
											"type"=>"text",
											"help"=>"Bitte geben Sie den Namen der Quelle an, die Sie neu anlegen m&ouml;chten.\nFalls die Quelle schon existiert, wird sie nicht neu angelegt."
											),
							$formerror[$name], 
							$preset[$name]
						);
// date, calendar	
$name = "date";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>E</u>rscheinungstag", 
											"key"=>"e", 
											"type"=>"date",
											"help"=>"Wann wurde der Artikel ver&ouml;ffentlicht. Geben Sie das Datum in der Form Tag.Monat.Jahr an.\nz. Bsp.: 5.3.05 oder 16.02."
											),
							$formerror[$name], 
							$preset[$name]
						);						
// Site Select
$name = "sites[]";
$html['content']  .=	$T->form_row( array("name"=>$name, 
											"label"=>"<u>Z</u>ielbereiche ausw&auml;hlen", 
											"key"=>"q", 
											"type"=>"site_select",
											"values"=>$PS->get_all(),
											"help"=>"W&auml;hlen Sie die Bereiche aus, auf denen sp&auml;ter der Artikel erscheinen soll.\nEine Mehrfachauswahl mit mit gedr&uuml;ckter STRG-Taste m&ouml;glich",
											) ,
							$formerror[$name], 
							$preset[$name]
						);					
						
						
// footer with buttons	
$html['content']  .= $T->form_button("ex__ok_reset") . "</table>";

// Fill into Template --------------------------------------------------------------

// form after buttons, beause of "example hiddenfield, onchange..."
$T->add_form("index.php", true); // action, checksubmit/checkreset, special
$T->add_menu($menu_links);
$T->add_content($html['content']);
$T->add_js("js/entry_examples.js");
$T->add_js("js/entry_check.js");

if ($send==1) $T->check_form(); else $T->show();

?>