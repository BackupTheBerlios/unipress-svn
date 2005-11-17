<?php
require_once I_PATH . "htmlscripts.inc.php";

class template {
	// all private
	var $js = array();
	var $css = array();
	var $form = array("start"=>"", "end"=>"", "hidden"=>"");
	var $user_content =	"";
	var $menu = "";
	var $help = "";
	var $js_startup = "";
	var $title = "";
	var $startup_focus =false;
	var $refresh = "";
	
	// private
	var $build_resetcheck = false;
	var $linebreak = "\n";
	var $helpers = array();
	var $block = false;
	var $special_form="";
	
	// all public
    function template() {
    	$static_helpfile = "help/index.html";
    	$this->add_help("<div id=\"helpicon\">" .
    			"<a href=\"".$static_helpfile."\" title=\"Hilfeseite in einem neuen Fenster &ouml;ffnen\" target=\"_blank\">" .
    			"<img border=\"0\" src=\"images/help/helpIcon.png\" height=\"30\" width=\"32\" alt=\"hilfe\" /></a>" .
    			"</div>" .
    			"<div class=\"tip\" id=\"tjavascript\">" .
    			"<span id=\"menu\">Hilfe </span>" .
    			"<span style=\"color:red\">Sie sollten Javascript aktivieren um die Online-Hilfe nutzen zu k&ouml;nnen.<br />" .
    			"<a href=\"".$static_helpfile."\" target=\"_blank\">Die Hilfeseite in einem neuen Fenster &ouml;ffnen.</a></span>" .
    			"</div>");
    	$this->add_js_startup(//	"// hide javascript not avaible error" .
				"document.getElementById('tjavascript').style.visibility=\"hidden\";\n" .
				//"// hide helpicon" .
				"document.getElementById('helpicon').style.visibility=\"hidden\";");
				
    	$this->add_js('js/std.js'); // show/hide
    	
    	$this->add_css('css/main.css'); // mainstyles
    	
    }
    function add_js_startup($js) {
    	$this->js_startup .= $js.$this->linebreak;
    }
    
    function set_startup_focus($field) {
    	if ($this->startup_focus==false) {
	    	$this->startup_focus = true;
	    	$this->add_js_startup("document.forms[0].".$field.".focus();");
    	}
    }
    
    function add_refresh($sec, $url) {
    	$this->refresh = "<meta http-equiv=\"refresh\" content=\"".$sec.",URL=".$url."\">";	
    }
    // private
    function get_js_startup() {
    	//if ($this->js_startup!="") {
	    	return  "<script type=\"text/javascript\">" .
	    			"<!--\n" .
	    			"function startup() {" .
	    			$this->js_startup .
	    			"}" .
	    			"\n//-->" .
	    			"</script>";
    	//} else return "";
    }
    
    function add_js($string) {
    	array_push($this->js, $string);
    }
    
    function add_css($string) {
    	array_push($this->css, $string);
    }
    
    function add_hidden_field($name, $value) {
    	$this->form['hidden'].= "<input type=\"hidden\" name=\"".$name."\" value=\"".$value."\" />";	
    }
    function add_form($aim, $subres_check=false, $special="") {
    	if ($subres_check==false) {
    		$onsubmit_reset = "";
    	} else {
    		$onsubmit_reset = " onsubmit=\"return checksubmit();\" onreset=\"return checkreset();\"";
    		$this->build_resetcheck=true;
    	}
    	$special = $this->special_form.$special;
    	$this->form['start']	=	"<form name=\"myform\" action=\"".$aim."\" enctype=\"multipart/form-data\" method=\"post\" ".$onsubmit_reset." ".$special.">";
    	$this->form['end']		=	"</form>";	
    	$this->add_hidden_field("send","1");
    }
    
    function add_help($help) {
    	$this->help .= $help;
    }
    
    function add_menu($menu) {
    	$this->menu .= $menu;
    }
    
    function add_content($c) {
    	$this->user_content .= $c;
    }
    
    function add_title($t) {
    	$this->title .= $t;//htmlentities($t);
    }
            
    // private
    function build_resetcheck() {
    	if ($this->build_resetcheck==false) return "";
    		
    	if (is_array($this->helpers)) {
    	    while (list(, $val) = each($this->helpers)) {
    			$r .="document.getElementById('a".$val."').style.visibility=\"hidden\";";
    	    }	
    	}

    	return  "<script type=\"text/javascript\">" .
	    			"<!--\n" .
	    			"function checkreset() {\n" . 
	    			" var chk = window.confirm(\"Wollen Sie alle Eingaben loeschen?\");" .
    				"if (chk==true) {" .
	    			$r .
	    			"}\nreturn (chk);}" .
	    			"\n//-->" .
	    			"</script>";	
    }
            
    function show($do_echo=true) {
		// content
		// main 
		$content = $this->form['start'] .
				"<div class=\"tablelayer\">" .
				$this->user_content .
				"</div>" .
				$this->form['hidden'] .
				$this->form['end'];

    	// head
		$head = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> 
    		<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">" .
    		"<head><title>".$this->title."</title>" .
    		"<meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\">" .
    		$this->refresh .
    		$this->get_js_startup() .
    		javascripts($this->js) .
    		$this->build_resetcheck() .
			css($this->css) .
    		"" .
    		"</head>";
    		
		// body
		$body = "<body onload=\"startup();\"><div id=\"container\">" .
		"<div id=\"tipcontainer\">" .
		"<div id=\"navilayer\">" .
		"<span id=\"menu\">Men�</span><br />" .
		$this->menu .
		"</div>" .
		$this->help .
		"</div>" .
		$content .
		"</div>" .
		"</body></html>";
		
		if ($do_echo==true) echo $head . $body; else return $head . $body;
    }
    
    // special
    // give me a button combi e.g. "ok_reset" or "ex__ok_reset"
	
	// TODO: Build set_examples from given examples..
    function form_button($buttons="") {
    	if ($this->block==true) $buttons="fehler"; 
    	
    	$btn['submit']	=	"<input accesskey='8' value=\"OK, Speichern\" class=\"submitbutton buttons\"  type=\"submit\" />";
    	$btn['reset']	=	"<input accesskey='9' value=\"Eingaben l&ouml;schen\" class=\"resetbutton buttons\"  type=\"reset\" />";
    	$btn['ex']		=	"<input accesskey='7' value=\"Beispiel..\" type=\"button\" class=\"examplebutton buttons\" onClick=\"set_examples();\" />";
    	
		switch ($buttons) {
			default:
			case 'ok_reset':
				$r = "<td colspan=\"3\" align=\"right\">" . $btn['submit'] ."&nbsp;".$btn['reset'] . "</td>";
			break;	
			case 'ex__ok_reset':
				$r = "<td>".$btn['ex']."</td><td colspan=\"2\" align=\"right\">" . $btn['submit'] ."&nbsp;".$btn['reset'] . "</td>";
				$this->add_hidden_field("hiddenexample",0); // for examplebutton
				$this->special_form="onChange=\"document.myform.hiddenexample.value=0\"";
			break;
			case 'fehler':
				$r = "<td colspan=\"3\" align=\"center\" class=\"error\">Ich konnte notwendige Daten f�r dieses Formular nicht laden. <br />L�sung: ".$this->block."</td>";
			break;	
		}
    	return "<tr>" . $r . "</tr>";	
    }
    /*
	 * $name = "bereichsname"
	 * form_row( array("name"=>$name, "label"=>"<u></u>", "key"=>"", "help"=>""),$formerror[$name], $pre[$name]);
	 */
	function form_row($field, $error="", $prefill="") {
		require_once "form.class.php";
		$hoverColor = "#FFFFF0";
		// DO NOT CHANGE!!! next two lines
		$tip		= "t" . $field['name'];	// field id for js
		$aPrefix	= "a";	// Attention prefix for div layer
		// ok.
		$aImage		= "images/help/attention.gif";	// att. image
		 
		$this->set_startup_focus($field['name']); 
		
		if (array_key_exists("help", $field)) {
			$helpdiv = "<div class=\"tip\" id=\"".$tip."\">" .
					"<span id=\"menu\">Hilfe" .
	    			" zu ".strip_tags($field['label'])." </span><br />" .
	    			nl2br($field['help']) .
	    			"</div>";
	    	$this->add_help($helpdiv);	// add to local help
	    	array_push($this->helpers, $field['name']);
	    	$helptip_focus = "onfocus=\"zeige('".$tip."');\"";
	    	$helptip_mouse = "zeige('".$tip."');";
	    		
		}
		
		$prepare_row_start = "<tr ".$helptip_focus." onmouseover=\"".$helptip_mouse."this.style.backgroundColor='".$hoverColor."';\"" .
						"onblur=\"this.style.backgroundColor=''\"" .
						"onmouseout=\"this.style.backgroundColor=''\">" .
					"<td>" .
						"<label for=\"".$field['name']."\" accesskey=\"".$field['key']."\">".$field['label'].":</label>" .
					"</td><td>" .
						"<div class=\"attention\" id=\"".$aPrefix . $field['name']."\">" .
							"<img src=\"".$aImage."\" height=\"10\" width=\"10\" alt=\"Fehler: ".$error."\" />" .
						"</div>" .
					"</td><td>";
		$prepare_row_end = BR . HINT . $error . CSPAN .	"</td></tr>";
		
		switch ($field['type']) {
			default:
			case 'text':
				$prefill	=	($prefill=="")? "" : " value=\"".$prefill."\"";	
				$r =  "<input type=\"text\" size=\"40\" name=\"".$field['name']."\" ".$prefill." id=\"".$field['name']."\" ".$helptip_focus." />";
			
			break;
			case 'file':
				$r = "<input type=\"file\" name=\"".$field['name']."\" ".$helptip_focus." />";
			break;
			case 'source_select':
				$list 	= 	$field['values'];
				$pre	=	array($prefill);
				if (!is_array($list) || count ($list)<1) {$r="Es existieren keine Quellen. Bitte legen Sie eine an.";} 
				else {
					$list	=	array_merge(array(0=>array("value"=>"-1","name"=>"Neue Quelle")),$list);
					$r = FORM::select("source",$list,$pre,1);
				}
			break;
			case 'site_select':
				$pre	=	$prefill;
				$list 	= 	$field['values'];
				if ($pre==0)	$pre	=	array(9999);
				if (!is_array($list) || count ($list)<1) {
					$r="Es existieren keine Bereiche.";
					$this->block="<a class=\"errorlink\" href='?menu=news'>- Bereich anlegen</a><br>";
				}
				else {
					$list	=	array_merge(array(0=>array("value"=>"9999","name"=>"Bitte w�hlen Sie mindestens einen Zielbereich")),$list);
					$r = FORM::select("sites[]",$list,$pre,4);
				}
			break;
			case 'yn_radio':
				$r = FORM::radio($field['name'],"ja","",$prefill) ."ja<br />".
					 FORM::radio($field['name'],"nein","",$prefill) ."nein";
			break;
			case 'date':
				$r = "<input type=\"text\" size=\"28\" name=\"".$field['name']."\" ".$prefill." id=\"".$field['name']."\" ".$helptip_focus." />";
				$r.= FORM::button_popup($field['name'],"Kalender") ;
				
				#$r="_not_implemented_yet_";
				
			break;
			
		}// switch
		return $prepare_row_start . $r . $prepare_row_end;
	}
}





?>