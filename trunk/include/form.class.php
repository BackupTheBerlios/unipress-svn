<?php
/*
 * changes
 *  * 2005-09-06 cb, 
 * 		+ select hat neue option, 'onfocus'
 * 		 
 *
 */
// CD: 2002-10-17 readonly bei text hinzugef�gt
//                Sprungmen� bei select hinzugef�gt
// CD: 2002-09-11 select() angepa�t: Abfrage, ob vorausgew�hlt werden soll korrigiert
class FORM
{
	// Funktionen der Klasse FORM:
	// - text($name,$value,$size,$maxlength,$readonly)				=> generiert ein Eingabefeld Typ Text
	// - textarea($name,$value,$cols,$rows)							=> generiert ein Eingabefeld Typ Textarea
	// - select($name,$ar_option,$ar_selected,$size,$ar_option_0)	=> generiert select-Auswahl
	//																	name		Name des Feldes
	//																	ar_option	Array mit ar_option[$i][value] und ar_option[$i][name]
	//																	ar_selected	Array mit Werten, die mit ar_option �bereinstimmen und vorausgew�hlt werden sollen
	//																	size		Gr��e 1-Pulldown, 3-Selectliste
	//																	ar_option_0	Array wie o.g., da� vorangestellt werden soll
	// - checkbox($name,$value,$checked,$txt)						=> generiert Checkbox
	//																	name		Name
	//																	value		Wert
	//																	checked		wenn checked=value, dann wird es ausgew�hlt
	//																	txt			Text hinter Checkbox
	// - radio($name,$value,$selected,$txt)							=> generiert Radiobutton
	//																	selected	siehe checkbox
	// - hidden($name,$value)										=> generiert hidden-Feld
	// - submit($name,$value,$class,$title)							=> generiert Submitbutton
	// - button_popup($name,$value,$class,$JavaScript,$title)		=> generiert Button (wird mit JavaScript document.write erzeugt, so da� er nur bei aktiviertem JavaScript zu sehen ist)
	//																	JavaScript	JavaScript, da� ausgef�hrt werden soll
	// - file($name,$class,$size,$maxlength)						=> generiert Dateieingabefeld

	// Nutzung: static use
	// FORM::select ...
	
	// Nutzung alt:
	// include "inc.cla/form.class.php";
	// $FORM = new FORM;

	// gibt ein Formularelement zur�ck in der Form
	// <input type="text" name="name" value="value" size=size value=value readonly>
	function text($name,$value="",$class="",$size="",$maxlength="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$size		= $size>0		? " size=\"".round($size)."\""				: "";
		$maxlength	= $maxlength>0	? " maxlength=\"".round($maxlength)."\""	: "";
		$option		= $class.$size.$maxlength." ".$option;
		return "<input type=\"text\" name=\"".$name."\" value=\"".htmlentities($value)."\"".$option." />";
	}//# text()
	
	function password($name,$value="",$class="",$size="",$maxlength="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$size		= $size>0		? " size=\"".round($size)."\""				: "";
		$maxlength	= $maxlength>0	? " maxlength=\"".round($maxlength)."\""	: "";
		$option		= $class.$size.$maxlength." ".$option;
		return "<input type=\"password\" name=\"".$name."\" value=\"".htmlentities($value)."\"".$size.$maxlength.$class." />";
	}//# password()

	// gibt ein Formularelement zur�ck in der Form
	// <textarea name=name cols=cols rows=rows>value</textarea>
	function textarea($name,$value="",$class="",$cols="",$rows="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$cols		= $cols>0		? " cols=\"".round($cols)."\""				: " cols=\"25\"";
		$rows		= $rows>0		? " rows=\"".round($rows)."\""				: " rows=\"5\"";
		$option		= $class.$cols.$rows." ".$option;
		return "<textarea name=\"".$name."\" ".$option.">".htmlentities($value)."</textarea>";
	}//# textarea()

	// gibt ein Formularelement zur�ck in der Form
	// <input type=checkbox name=name value=value>
	// name			Name des Formularfeldes
	// value		Wert
	// checked		entspricht dem Wert, wenn beides gleich, dann wird es vorselektiert
	function checkbox($name,$value,$class="",$checked="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$checked	= $value==$checked ? " checked" 							: "";
		$option		= $class.$checked." ".$option;
		return "<input type=\"checkbox\" name=\"".$name."\" value=\"".htmlentities($value)."\"".$option." />";
	}//# checkbox()

	// gibt ein Formularelement zur�ck in der Form
	// <input type=radio name=name value=value>
	// name			Name des Formularfeldes
	// value		Wert
	// checked		entspricht dem Wert, wenn beides gleich, dann wird es vorselektiert
	function radio($name,$value,$class="",$checked="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$checked	= $value==$checked ? " checked" 							: "";
		$option		= $class.$checked." ".$option;
		return "<input type=\"radio\" name=\"".$name."\" value=\"".htmlentities($value)."\"".$option." />";
	}//# radio()

	// gibt ein Formularelement zur�ck in der Form
	// <input type=hidden name=name value=value>
	// name			Name des Formularfeldes
	// value		Wert
	function hidden($name,$value="") {
		return "<input type=\"hidden\" name=\"".$name."\" value=\"".htmlentities($value)."\" />";
	}//# hidden()

	// gibt ein Formularelement zur�ck in der Form
	// <input type=submit name=name value=value>
	// name			Name des Formularfeldes
	// value		Wert
	// class		css-Klasse
	function submit($name,$value="",$class="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$option		= $class." ".$option;
		return "<input type=\"submit\" name=\"".$name."\" value=\"".htmlentities($value)."\"".$option." />";
	}//# submit()
	
	// gibt ein Formularelement zur�ck in der Form
	// <input type=submit name=name value=value>
	// name			Name des Formularfeldes
	// value		Wert
	// class		css-Klasse
	function button($name,$value="",$class="",$option="") {
		$class		= $class!=""	? " class=\"".$class."\""					: "";
		$option		= $class." ".$option;
		return "<input type=\"button\" name=\"".$name."\" value=\"".htmlentities($value)."\"".$option." />";
	}//# submit()


	// gibt ein Formularelement zur�ck in der Form
	// <input type=button name=name value=value class=class onClick=JavaScript title=title>
	// name			Name des Formularfeldes
	// value		Wert
	// class		css-Klasse
	// JavaScript	JavaScriptFunktion, die beim anklicken ausgef�hrt werden soll
	// title		sieh alt-Tag bei Grafiken
	// Button wird durch JavaScript erzeugt, so da� er nur zu sehen ist, wenn JavaScript aktiviert ist
	function button_popup($name,$value="",$class="",$JavaScript="",$title="")
	{
		if(!function_exists("javascript")) require_once "javascript.inc.php";
		$class!="" 		? $class		= " class=\"".$class."\""						: $class		= "";
		$JavaScript!=""	? $JavaScript	= " onClick=\"".addslashes($JavaScript)."\""	: $JavaScript	= "";
		$title!="" 		? $title		= " title=\"".htmlentities($title)."\""			: $title		= "";
		return JavaScript("document.write('<input type=\"button\" name=\"".$name."\" value=\"".$value."\"".$class.$JavaScript.$title." />');")."<noscript>&nbsp;</noscript>";
	}//# button_popup()
	

	// gibt ein Formularelement zur�ck in der Form
	// <input type=file name=name size=size class=class>
	// name			Name des Formularfeldes
	// class		css-Klasse
	// size			Gr��e
	// maxlength	max Gr��e des dateiuploads (bei domainfactory 8 M)
	function file($name,$class="",$size=5,$maxlength=8000)
	{
		$class!="" ? $class=" class=\"".$class."\"" : $class="";
		return "<input type=\"file\" name=\"".$name."\" size=".$size." maxlength=".$maxlength."".$class." />";
	}//# file()
	
	// gibt ein Formularelement zur�ck in der Form
	// <select name=name size=size><option value=ar_option[0][value]>ar_option[0][name]</option></select>
	// name			Name des Formularfeldes
	// ar_option	Array mit Werten und Name, was drin stehen soll ar_option[n][value], ar_option[n][value]
	// ar_selected	Array mit Werten (stimmen mit denen von ar_option[n][value] �berein), die vorselektiert werden sollen
	// size			1   gibt ein Pulldown zur�ck
	//				1+n gibt eine Liste zur�ck
	function select($name,$ar_option,$ar_selected="",$size=1,$option="",$onfocus="")
	{
		$size = (int) $size;
		$optionen = "";
		if($ar_selected==true) {
			is_array($ar_selected)==false ? $array_selected[0] = $ar_selected : $array_selected = $ar_selected;	// wenn ar_selected kein Array ist, dann eines daraus machen
			is_array($array_selected)==true ? $array_selected = @array_flip($array_selected) : 1;				// Werte und Schl�ssel vertauschen
		} else {
			$array_selected = false;
		}
		is_array($ar_option)==false ? $ar_option = array() : 1;
		foreach($ar_option as $ar) {																	// alle �bergebenen Optionen durchlaufen
			if($array_selected && is_array($array_selected))											// wenn es etwas zum selektieren gibt
			{																							// und $ar_selected ein Array ist
				$selected = array_key_exists(@$ar["value"],$array_selected)==true ? " selected" : "";	// wenn die aktuelle Option in $ar_selected vorkommt,
			} else {																					// dann soll diese Option vorausgew�hlt werden
				$selected = "";
			}
			if(@$ar["value"]!="" || @$ar["name"]!="") {													// komplett leere Eintr�ge sollen nicht erzeugt werden
				$optionen .= "<option value=\"".htmlentities(@$ar["value"])."\"".$selected.">"
							.htmlentities($ar["name"])
							."&nbsp;</option>";
			}
		}//# foreach(ar_option)
		$onfocus = ($onfocus=="") ? "" : " onfocus='$onfocus'";
		$r = "<select name=\"".$name.(($size>1 && eregi("\[",$name)==false) ?  "[]" : "")
				."\" size=\"".$size."\" ".trim(($size>1 ? " multiple " : "").$option)."$onfocus>"
				.$optionen
				."</select>";
		return $r;
	}//# select()
	/*
		Hinweis: zu voran- oder nachgestellten Eintr�gen
				�bergeben wird bsp.:	ar[0][value] = 1 ar[0][name]  = A 
										ar[1][value] = 2 ar[1][name]  = B 
										ar[2][value] = 3 ar[1][name]  = C 
				vorangestellte mit		ar[-1][value]
	*/
	



} //# class FORM
?>