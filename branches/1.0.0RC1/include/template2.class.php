<?php
// $Id$
//
/* Idee:
 * add_row() ...
 *  -> was send?
 * [no]
 * 		[render]
 * 		[show]
 * [yes]
 * 		-> was check ok?
 * 		[no]
 * 			[render with errors]
 * 			[show]
 * 		[yes]
 * 			[deliver an array with fields and values]
 * 
 */
require_once I_PATH."htmlscripts.inc.php";

class template {
	// all private
	var $js = array ();
	var $css = array ();
	var $form = array ("start" => "", "end" => "", "hidden" => "");
	var $user_content = "";
	var $menu = "";
	var $help = "";
	var $js_startup = "";
	var $title = "";
	var $startup_focus = false;
	var $refresh = "";

	var $build_resetcheck = false;
	var $linebreak = "\n";
	var $helpers = array ();
	var $block = false;
	var $special_form = "";
    var $filefield = "";
    var $fieldlist = array();
    var $leadin="";
    var $append="";
    var $results=array();
    var $fielderrors=array();
    var $accepted_field_types = array("dummy","radio","select","text","file","source_select","site_select",
						"yn_radio","date","link","password");
    
    var $DBG;
    
	// all public
	function template(& $DBG) {
		$static_helpfile = "help/index.html";
		$this->add_help("<div id=\"helpicon\">"."<a href=\"".$static_helpfile."\"" .
				" title=\"Hilfeseite in einem "."neuen Fenster &ouml;ffnen\" " .
				" target=\"_blank\">"."<img border=\"0\"" .
				" src=\"images/help/helpIcon.png\"".
				" height=\"30\" width=\"32\" alt=\"hilfe\" /></a>"."</div>".
				"<div class=\"tip\" id=\"tjavascript\">".
				"<span id=\"menu\">Hilfe </span>".
				"<span style=\"color:red\">Sie sollten Javascript aktivieren ".
				"um die Online-Hilfe nutzen zu k&ouml;nnen.<br />".
				"<a href=\"".$static_helpfile."\" target=\"_blank\">Die ".
				"Hilfeseite in einem neuen Fenster &ouml;ffnen.</a></span>".
				"</div>");
		$this->add_js_startup(//	"// hide javascript not avaible error" .
		"document.getElementById('tjavascript').style.visibility=\"hidden\";\n".
		//"// hide helpicon" .
		"document.getElementById('helpicon').style.visibility=\"hidden\";");

		$this->add_js('js/std.js'); // show/hide

		$this->add_css('css/main.css'); // mainstyles
		
		$this->DBG = $DBG; // debugger
		$this->DBG->send_message("template Class V2 started");

	}
	function add_js_startup($js) {
		$this->js_startup .= $js.$this->linebreak;
	}

	function set_startup_focus($field) {
		if ($this->startup_focus == false) {
			$this->startup_focus = true;
			$this->add_js_startup("document.forms[0].".$field.".focus();");
		}
	}

	function add_refresh($sec, $url) {
		$this->refresh = "<meta http-equiv=\"refresh\" "."content=\"".$sec.",URL=".$url."\">";
	}
	// private
	function get_js_startup() {
		//if ($this->js_startup!="") {
		return "<script type=\"text/javascript\">"."<!--\n"."function startup() {".$this->js_startup."}"."\n//-->"."</script>";
		//} else return "";
	}

	function add_js($string) {
		array_push($this->js, $string);
	}

	function add_css($string) {
		array_push($this->css, $string);
	}

	function add_hidden_field($name, $value) {
		$this->form['hidden'] .= "<input type=\"hidden\" name=\"".$name."\" value=\"".$value."\" />";
	}
	function add_form($aim, $subres_check = false, $special = "") {
		if ($subres_check == false) {
			$onsubmit_reset = "";
		}
		else {
			$onsubmit_reset = " onsubmit=\"return checksubmit();\" onreset=\"return checkreset();\"";
			$this->build_resetcheck = true;
		}
		$special = $this->special_form.$special;
		$this->form['start'] = "<form name=\"myform\" action=\"".$aim."\" enctype=\"multipart/form-data\" method=\"post\" ".$onsubmit_reset." ".$special.">";
		$this->form['end'] = "</form>";
		$this->add_hidden_field("send", "1");
	}

	// public
	function add_form_field($info){
		$this->DBG->enter_method();
		
		// check fieldtype
		if(!in_array($info['type'], $this->accepted_field_types)){
			$this->DBG->leave_method($info['type']." wird nicht als Feldtyp akzeptiert! STOPP");
			
			debug_print_backtrace2();die("wrong fieldtype");
		}
		$this->add_field($info);		
		//$this->form_row($info);
		$this->DBG->leave_method();
	}
	
	function leadin($text){ $this->leadin=$text; }
	function append($text){ $this->append=$text; }

	// private
	function add_field($field){
		$this->DBG->send_message("Field '".$field['name']."' added.");
		if (isset($field['name'])) {
			#$this->fieldlist = array_merge($this->fieldlist,array($field['name']=> $field));
			array_push($this->fieldlist,$field);
			 
			return true;
		}
		else {
			return false;
		}	
	}
	
	function add_help($name, $help="") {
		$this->help .= $help;
		array_push($this->helpers, $name);
	}

	function add_menu($menu) {
		$this->menu .= $menu;
	}

	function add_content($c) {
		$this->user_content .= $c;
	}

	function add_title($t) {
		$this->title .= $t; //htmlentities($t);
	}

	// private
	function build_resetcheck() {
		if ($this->build_resetcheck == false)
			return "";

		if (is_array($this->helpers)) {
			while (list (, $val) = each($this->helpers)) {
				$r .= "document.getElementById('a".$val."').style.visibility=\"hidden\";";
			}
		}

		return "<script type=\"text/javascript\">"."<!--\n"."function checkreset() {\n"." var chk = window.confirm(\"Wollen Sie alle Eingaben loeschen?\");"."if (chk==true) {".$r."}\nreturn (chk);}"."\n//-->"."</script>";
	}

	function show($do_echo = true) {
		$this->DBG->enter_method();
		
		//$this->DBG->watch_var("Fieldlist", $this->fieldlist);
		$this->DBG->watch_var("Results", $this->results);
		// content render
		while($v = array_shift($this->fieldlist))
			$this->user_content.=$this->form_row($v);	
		
		
		// menü
		$menu = "";
		if(strlen($this->menu)>1) $menu = "<span id=\"menu\">Men&uuml;</span><br />".$this->menu;
		
		
		// main 
		$content = $this->form['start']."<div class=\"tablelayer\">".
					$this->leadin.
					$this->user_content.
					$this->append."</div>".
					$this->form['hidden'].
					$this->form['end'];

		// head
		$head = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> 
		   		<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">".
				"<head><title>".$this->title."</title>".
				"<meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\">".
				$this->refresh.
				$this->get_js_startup().
				javascripts($this->js).
				$this->build_resetcheck().
				css($this->css).""
				."</head>";

		// body
		$body = "<body onload=\"startup();\"><div id=\"container\">"."<div id=\"tipcontainer\">".
		"<div id=\"navilayer\">".
			$menu.
		"</div>".
		$this->help."</div>".$content.
//"</div>".
		// footer
 			"    \n<a href=\"http://validator.w3.org/check?uri=referer\"><img border='0'" . 
            "        src=\"images/valid-xhtml.png\"" . 
            "        alt=\"Valid XHTML 1.0 Transitional\" height=\"19\" width=\"53\" /></a>" . 
            "  </p>\r\n" .
            "</div>". 
			"</body></html>";

		$this->DBG->leave_method();
		if ($do_echo == true)
			echo $head.$body;
		else
			return $head.$body;
	}

	// special
	// give me a button combi e.g. "ok_reset" or "ex__ok_reset"

	// TODO: Build set_examples from given examples..
	function form_button($buttons = "") {
		if ($this->block == true)
			$buttons = "fehler";

		$btn['submit'] = "<input accesskey='8' value=\"OK, Speichern\" class=\"submitbutton buttons\"  type=\"submit\" />";
		$btn['reset'] = "<input accesskey='9' value=\"Eingaben l&ouml;schen\" class=\"resetbutton buttons\"  type=\"reset\" />";
		$btn['ex'] = "<input accesskey='7' value=\"Beispiel..\" type=\"button\" class=\"examplebutton buttons\" onClick=\"set_examples();\" />";

		switch ($buttons) {
			default :
			case 'ok_reset' :
				$r = "<td colspan=\"3\" align=\"right\">".$btn['submit']."&nbsp;".$btn['reset']."</td>";
				break;
			case 'ex__ok_reset' :
				$r = "<td>".$btn['ex']."</td><td colspan=\"2\" align=\"right\">".$btn['submit']."&nbsp;".$btn['reset']."</td>";
				$this->add_hidden_field("hiddenexample", 0); // for examplebutton
				$this->special_form = "onChange=\"document.myform.hiddenexample.value=0\"";
				break;
			case 'fehler' :
				$r = "<td colspan=\"3\" align=\"center\" class=\"error\">Ich konnte notwendige Daten f&uuml;r dieses Formular nicht laden. <br />L&ouml;sung: ".$this->block."</td>";
				break;
		}
		return "<tr>".$r."</tr>";
	}
	/*
	 * $name = "bereichsname"
	 * form_row( array("name"=>$name, "label"=>"<u></u>", "key"=>"", "help"=>""),$formerror[$name], $pre[$name]);
	 */
	 //todo: refactor as single funtions
	function form_row($field) {
		$this->DBG->enter_method();
		require_once "form.class.php";
		$hoverColor = "#FFFFF0"; //TODO: should not stand here
		// DO NOT CHANGE!!! next two lines
		$tip = "t".$field['name']; // field id for js
		$aPrefix = "a". // Attention prefix for div layer
			$field['name'];
		// ok.
		$aImage = "images/help/attention.gif"; // att. image
		$divclass = "class=\"attention\"";

		$this->set_startup_focus($field['name']);
		//$this->add_field($field);
		
		// error
		if (array_key_exists($field['name'], $this->fielderrors)
			and $this->fielderrors[$field['name']]!=false) {
			$error = "<span style='color:white;background:red'>".$this->fielderrors[($field['name'])]."</span>".BR;
			$bgcolor = " bgcolor='red'";
			$divclass = "";
		} else {
			$error = "";
			$bgcolor = "";	
		}
		
		// prefill, first: posted, 2nd: formfilled (code)
		// remove [] for multiple choice fields
		$fname =  (eregi("\[\]$",$field['name'])) ?  substr($field['name'],0,-2) : $field['name'];
		if (array_key_exists($fname, $_REQUEST)) {
			$prefill = $_REQUEST[($fname)];
		} elseif (array_key_exists("prefill", $field)) { 
			$prefill = $field["prefill"];
		} else {
			$prefill = "";
		}
		$this->DBG->watch_var("prefill(".$field['name'].")", $prefill);

		if (array_key_exists("help", $field)) {
			$helpdiv = "<div class=\"tip\" id=\"".$tip."\">"."<span id=\"menu\">Hilfe"." zu ".strip_tags($field['label'])." </span><br />".nl2br($field['help'])."</div>";
			$this->add_help($field['name'], $helpdiv); // add to local help
			$helptip_focus = "onfocus=\"zeige('".$tip."');\"";
			$helptip_mouse = "zeige('".$tip."');";
		}

		#$error = check_field($field);

		$prepare_row_start = "<tr ".$helptip_focus." onmouseover=\"".$helptip_mouse
		."this.style.backgroundColor='".$hoverColor."';\""
		."onblur=\"this.style.backgroundColor=''\""
		."onmouseout=\"this.style.backgroundColor=''\">"
		."<td>"."<label for=\"".$field['name']."\" accesskey=\"".$field['key']."\">"
		.$field['label'].":</label>"."</td><td>"
		."<div $divclass id=\"".$aPrefix."\">"
		."<img src=\"".$aImage."\" height=\"10\" width=\"10\" alt=\"Fehler\""
		."title=\"Fehler: "
		.strip_tags($error)."\"/>"."</div>"."</td><td>"
		;
		$prepare_row_end = BR.HINT.$error.CSPAN."</td></tr>";

		switch ($field['type']) {
			default :
			case 'dummy' :
				$r = "\n".$field['help'];
				break;
			
			case 'link' :
			case 'text' :
				$prefill = ($prefill == "") ? "" : " value=\"".$prefill."\"";
				$r = "\n<input type=\"text\" size=\"40\" name=\"".$field['name']."\" ".$prefill." id=\"".$field['name']."\" ".$helptip_focus." />";
				break;

			case 'password' :
				//$prefill = ($prefill == "") ? "" : " value=\"".$prefill."\"";
				$prefill = "";
				$r = "\n<input type=\"password\" size=\"40\" name=\"".$field['name']."\" ".$prefill." id=\"".$field['name']."\" ".$helptip_focus." />";
				break;
				
			case 'file' :
				$prefill = $this->_get_result($field['name']);
			
				if ($prefill == "") {
					$r = "\n<input type=\"file\" name=\"".$field['name']."\" ".$helptip_focus." />";
				} else {
					$r =  "<input type=\"hidden\" name=\"".$field['name']."\" value=\"".$prefill."\">$prefill";	
				}
				break;


		
			case 'select':
				// using "values"
				
				$r = "prefill: $prefill " . FORM :: select($field['name'], $field['values'], array(0=>$prefill), 1);
				break;
				
			case 'source_select' :
				$list = $field['values'];
				$pre = array ($prefill);
				if (!is_array($list) || count($list) < 1) {
					$r = "Es existieren keine Quellen. Bitte legen Sie eine an."."<input type=\"hidden\" name=\"source\" value=\"-1\" />";
				}
				else {
					$list = array_merge(array (0 => array ("value" => "-1", "name" => "Neue Quelle")), $list);
					$r = "\n".FORM :: select("source", $list, $pre, 1);
				}
				break;
			case 'site_select' :
				$pre = $prefill;
				$list = $field['values'];
				//if ($pre==0)	$pre	=	array(9999);
				if (!is_array($list) || count($list) < 1) {
					$r = "Es existieren keine Bereiche.";
					$this->block = "\n<a class=\"errorlink\" href='?menu=news'>"."- Bereich anlegen</a><br>";
				}
				else {
					$this->DBG->watch_var("site_select, prefill", $pre);
					$r = FORM :: select("sites[]", $list, $pre, 6);
				}
				break;
				
			case 'radio' :
				$r = ""; $br ="";
				$this->DBG->watch_var("radio, values", $field['values']);
				$this->DBG->watch_var("radio, prefill", $field['prefill']);
				foreach($field['values'] as $val) {
					if (array_key_exists('name',$val) && array_key_exists('value',$val)) {
						$value	= $val['value']; 
						$name	= $val['name'];
						$r .="\n".$br. FORM :: radio($field['name'], $value, "", $prefill).$name;
						$br = "<br />";
					}
				}
				break;
				
			case 'yn_radio' :
				$this->DBG->watch_var("yn_radio, prefill", $prefill);
				if ($prefill!="ja" && $prefill!="nein") { 
					$prefill = ($prefill==true || $prefill==1) ? "ja" : "nein";
				}
				$this->DBG->watch_var("yn_radio, prefill_after", $prefill);
				$r = FORM :: radio($field['name'], "ja", "", $prefill)."ja<br />".FORM :: radio($field['name'], "nein", "", $prefill)."nein";
				break;
			case 'date' :
				$prefill = ($prefill == "") ? "" : " value=\"".$prefill."\"";
				$r ="\n<input type=\"text\" size=\"28\" name=\"".$field['name']."\"".
					" ".$prefill." id=\"".$field['name']."\" ".$helptip_focus." />" .
				
					"<a href=\"javascript:cal_".$field['name'].".popup()\" title=\"Datum\">" .
					"&nbsp;<img src=\"images/b_calendar.gif\" width=\"16\"" .
					" height=\"16\" border=\"0\" alt=\"Datum\"></a>\n" .
					 
					"\n\n<script type=\"text/javascript\"><!--\n".
					"var cal_".$field['name']." = ".
					"new calendar1(document.forms['myform'].elements['".$field['name']."']); ".
					"cal_".$field['name'].".year_scroll = true;"."// --></script>\n";
				$this->add_js("js/calendar.js");
				break;

		} // switch
		$this->DBG->leave_method();
		return $prepare_row_start.$r.$prepare_row_end;
	}

	// TODO: in einzelfunktionen auflösen!
	function check_form() {
		$this->DBG->enter_method();
		
		#while (list($key, $val)=each($this->fieldlist)) {
		foreach($this->fieldlist as $val) {
			$formval = init($val['name'],"r",false); // wert holen aus "request"
			$this->DBG->watch_var("!!Found ".$val['type'],$formval);
			
			if (array_key_exists('optional', $val) && $val['optional']==true) {
				if(!($formval!="" || array_key_exists('minmax', $val))) {
					$this->DBG->send_message("-optional field ignored");
					continue;	
				}
				$this->DBG->send_message("-optional field processed!");
			} 
			
			
			
			switch ($val['type']) {
				case "file":	
					$this->DBG->watch_var("-File uploaded/".$formval." ex?",file_exists("uploaded/".$formval));
					if (strlen($formval)>1) {
						if( !file_exists("uploaded/".$formval)) {
							$this->DBG->send_message("-file upload error [file1]");
							$this->fielderrors[$val['name']]="Fehler beim Hochladen?";
						} else {
							$this->DBG->send_message("-file already uploaded [file2]");
							array_push($this->results, array($val['name']=>$formval));
						}
						
						continue;
					} else {
						$reti = FORM :: upload_file($val['name'], "uploaded/");
						if (substr($reti,0,5)=="ERROR") {
							// error occured with uploaded file
							$this->DBG->send_message("-error during file upload [file3]");
							$this->fielderrors[$val['name']]= substr($reti,6);
						} else {
							//echo $reti; // is filename
							$this->DBG->send_message("-file successfully uploaded [file4]");
							array_push($this->results, array($val['name']=>$reti));
						}
					}
					break;
					
				case "link":
					if (eregi("^http\:",$formval)) {
						$this->fielderrors[$val['name']]= "Links bitte ohne Protokoll (http://) angeben.";
					} else {
						array_push($this->results, array($val['name']=>trim($formval)));
					}
					break;
				
				case "date":
					$this->DBG->watch_var("-input", $formval);
					$r = $this->check_date($formval);
					$this->DBG->watch_var("-output", $r); 
					if (!eregi("^[0-9]",$r))	{
						$this->DBG->send_message("-invalid date!");
						$this->fielderrors[$val['name']]= $r;
					}
					else {
						array_push($this->results, array($val['name']=>$r));
						$_REQUEST[$val['name']]= $r;
					}
					break;
					
				case "site_select":
					$formval = init(substr($val['name'],0,-2),"r"); // wert holen aus "request"
					$this->DBG->watch_var("-SiteSelect:".$val['name'],$formval);
					// trivial
					if (is_array($formval)) {
						array_push($this->results, array($val['name']=>$formval));
						
						break;
					}
					$this->fielderrors[$val['name']]= "Bitte w&auml;hlen Sie mindestens einen Bereich aus!";
					break;
				
				case "source_select":
					// trivial
					$this->DBG->watch_var("-F:".$val['name'],$formval);
					if ($formval>0) {
						array_push($this->results, array($val['name']=>$formval));
						break;
					}
					$this->fielderrors[$val['name']]= "Bitte w&auml;hlen Sie eine Quelle aus oder erstellen eine neue!";
					break;
					
					
				default:
					$this->DBG->watch_var("-Default, Type is",$val['type']);
				case "text":
					// trivial
					$this->DBG->watch_var("-Text-Field",$val);
					$this->DBG->watch_var("-OR-Field",$val['_or']);
					
					/* Bedingungsliste */
					// wert?
					(boolean) $wert = ( strlen(trim($formval)) > 0 ); $this->DBG->watch_var("wert",$wert);
					// abhängigkeit
					(boolean) $or = array_key_exists('_or', $val);$this->DBG->watch_var("or",$or);
					// abhängiges feld hat wert (hat keinen fehler)
					(boolean) $or_wert = $or ? !array_key_exists($val['_or'],$this->fielderrors) : false; $this->DBG->watch_var("or_wert",$or_wert);
					// besteht eine min/max bedingung?
					(boolean) $minmax = array_key_exists('minmax',$val) ? true : false;
					// ist feld optional? (dann darf es leer sein, oder muss die min/max bedingung erfüllen)
					(boolean) $optional = (array_key_exists('optional',$val) && $val['optional']==true)? true : false;
					
					
					// wert vorhanden, schreiben!
					$error=false;
					if ( $wert && $minmax) {
						$error = $this->_check_minmax_length($val, $formval);
					}
					// XOR Bedingung
					if ( $wert and $or ) {
						if ($or_wert) {
							// zuviel
							$or_field_name = $this->fieldlist[$val['_or']]['label'];
							//$this->fielderrors[$val['name']]
							$error = "Dieses Feld wird ignoriert werden, da bereits '".$or_field_name."' ausgef&uuml;llt ist.";
						} else {
							// genau richtig, fehler löschen
							$error = $this->_check_minmax_length($val, $formval);
							if(!$error) $this->fielderrors[$val['_or']]= false; // fehler löschen
						}
					} 
					if ( !$wert && $or && !$or_wert ) {
						$or_field_name = $this->get_field_property($val['_or'],'label');
						$error = "Dieses Feld oder '".$or_field_name."' muss ausgef&uuml;llt werden. ";
					}
					// kein Wert, keine Alternative
					if ( !$wert && !$or && !$optional) {
						$error= "Dieses Feld muss  ausgef&uuml;llt werden. ";							
					}
					// kein Wert aber Längendefinition vorhanden 
					if ( !$wert && $error && $minmax) $error.=" (min. ".$val['minmax']['min']." und max. ".$val['minmax']['max']." Zeichen)";
					
					// central error or value setter
					$this->DBG->watch_var("Text-Errors",$error);
					if ($error) $this->fielderrors[$val['name']]=$error;
					else array_push($this->results, array($val['name']=>$formval));
					
					break; // final break;
			} // switch
		} // while
		$this->DBG->send_message("!!END Fields;");
		$thereareerrors = !empty($this->fielderrors);
		$this->DBG->watch_var("!Any errors",$thereareerrors);
		
		$realerrors=false;
		while(list($key,$val)=each($this->fielderrors)) {
			if($val!=false) $realerrors==true;
		}
		
		$this->DBG->watch_var("!Only real!errors",$realerrors);
		
		if ($thereareerrors && $realerrors) {
			// Fehlerfall
			$this->DBG->watch_var("!Errors (should be some)",$this->fielderrors);
			$this->DBG->send_message("Errors, showing form...");
			$this->show(); // if errors
			$reti = false;

		} else {
			// kein Fehler oder kein echter
			//!(count($this->fielderrors)==1 && $this->fielderrors['source']==FALSE) ) {			
			$reti = $this->results;
			$this->DBG->watch_var("!Result",$reti);
			$this->DBG->watch_var("!Errors (should be null)",$this->fielderrors);
		}
		$this->DBG->leave_method($reti);
		return $reti;
	}

	// val = field (Feld)
	// wert = Feld-wert per REQUEST
	// val[minmax]=arrax("min"=> .., "max"=>..)
	// WENN ALLES OK -> false, ansonsten fehlermeldung
	function _check_minmax_length($val, $wert) {
		$this->DBG->enter_method();
		$this->DBG->watch_var("minmax",$val['minmax']);
		if(!is_array($val['minmax'])) return false; // Kein Fehler, da keine Grenze
		$min = $val['minmax']['min'];
		$max = $val['minmax']['max'];
		
		$mm_error=false;
		if ( strlen($wert)>$max)  $mm_error = "Dieses Feld darf höchstens ".$max." Zeichen lang sein.";		
		if ( strlen($wert)<$min)  $mm_error = "Dieses Feld muss mindestens ".$min." Zeichen lang sein.";
		$this->DBG->leave_method($mm_error);
		return $mm_error;
	}

	function get_field_property($fieldname, $property) {
		$this->DBG->enter_method();
		$value = false;
		
		if (!is_array($this->fieldlist)) {
			$this->DBG->leave_method($value);
			return $value;	
		}
		
		foreach($this->fieldlist as $field) {
			if ($field['name']==$fieldname && array_key_exists($property,$field)) {
				$value = $field[$property];
			}	
		}
		
		$this->DBG->leave_method($value);
		return $value;
	}

	// dateform = inpput variant of date
	function check_date($d, $dateform="german") {
		$this->DBG->enter_method();
		//$dateform	=	"german"; 		// what do i suppose, which form i got
		$buffer 	=	$d;				// save for error_msg
		$preset_year=	strftime("%Y");	// with current year
		
		$d = trim($d);
		if (strlen($d)<3) {
			$ret = "Datum ($d) ung&uuml;ltig da zu kurz.";
			$this->DBG->leave_method($ret);
			return $ret;
			
				
		}	
		// make some tests
		if (!ereg("\.",$d)) $dateform = "other"; // sonst: german
		
		// transform into englisch form with "-"	
		$d = ereg_replace("\.|/","-",$d);
		
		// split
		$d = explode("-", $d);
		$c = count($d);
		$this->DBG->watch_var("exploded date",$d);
		$this->DBG->watch_var("count date",$c);
		
		
		if ($c<2 || $c>3) {
			$ret = "Datum ($buffer) ungueltig da zuwenige/zuviele Trennzeichen ($c).";
			$this->DBG->leave_method($ret);
			return $ret;
		}

		switch ($dateform) {
			case 'german':	// i got dd.mm.yy? make: yy.mm.dd
				switch ($c) {
					case 2: // dd-mm into mm-dd and add Year
						$day		=	$d[0];
						$month		=	$d[1];
						$year		=	$preset_year; // current year?
					break;
					case 3:
						$day		=	$d[0];
						$month		=	$d[1];
						
						
						switch (strlen($d[2])) {
							case 2:
								$year = substr(0,2,strftime("%Y")).substr(-2,2,$d[2]);
							break;
							case 4:
								$year = $d[2];
								
							break;
							default:
								$year		=	$preset_year; // current year?
						}
					break;
				}
			break;
			default:
				switch ($c) {
					case 2: // no transform
						$day		=	$d[1];
						$month		=	$d[0];
						$year		=	$preset_year; // current year?
					break;
					case 3:
						$day		=	$d[2];
						$month		=	$d[1];
						switch (strlen($year)) {
							case 2:
								$year = substr(0,2,strftime("%Y")).substr(-2,2,$d[0]);
							break;
							case 4:
								$year = $d[0];
								
							break;
							default:
								$year		=	$preset_year; // current year?
						}
					break;
				}
				
		}
	
		if (checkdate(1*$month, 1*$day, 1*$year)!=true) {
			$ret = "Dieses Datum existiert nicht: $day $month $year";
			$this->DBG->leave_method($ret);
			return $ret;
		}
		//ok
		//$ret = sprintf("%4d-%02d-%02d", $year, $month, $day);
		$ret = sprintf("%02d.%02d.%04d", $day,$month,$year);
		// set local
		$this->date = $ret;
		$this->DBG->leave_method($ret);
		return $ret; // return for checking or as true
		
		
	}
	
	function _get_result($fieldname) {
		$this->DBG->enter_method();
		$value = false;
		
		$this->DBG->watch_var("xxx",$this->results);
		
		if (!is_array($this->results)) {
			$this->DBG->leave_method($value);
			return $value;	
		}
		
		foreach($this->results as $field) {
			if (array_key_exists($fieldname,$field)) {
				$value=$field[$fieldname];	
			}
		}
				
		$this->DBG->leave_method($value);
		return $value;
	}
}
?>