
	 
/* helper for select field 
http://www.dcljs.de/faq/antwort.php?Antwort=forms_radioselect
*/
function selectWert(sObj) {
    with (sObj) return options[selectedIndex].value;
 }
 function multipleWert(sObj, trenn) {
    var rVal = '';
    for (var i=0; i<sObj.options.length; i++) with (sObj.options[i])
      if (selected) rVal += trenn + value;
    return rVal.substring(trenn.length);
  }


/* Start; set focus to first field
function startup()
{
	// focus to quelle
    document.forms[0].quelle.focus();
	 // hide javascript not avaible error
	 document.getElementById('tjavascript').style.visibility="hidden";
	 // hide helpicon
	 document.getElementById('helpicon').style.visibility="hidden";
	 // hide "neue Quelle" in select field
	 document.forms[0].quelle[1] = null;
	 
}
 */

/* should I reset form? really? */
function ResetCheck () {
  var chk = window.confirm("Wollen Sie alle Eingaben loeschen?");
  	/* hide all attentiontips */
	document.getElementById('aquelle').style.visibility="hidden";
	document.getElementById('adatei').style.visibility="hidden";
	document.getElementById('atitel').style.visibility="hidden";
	document.getElementById('akeywords').style.visibility="hidden";
	document.getElementById('ainst').style.visibility="hidden";
  return (chk);
}

/* new source */
function qneu2() {
  //document.getElementById('Fquelle').style.visibility="hidden";
	document.getElementById('Fquelleneu').style.visibility="visible";
	zeige('tquelleneu');
	document.forms[0].quelleneu.focus();
	document.forms[0].quelleneu.select();
}

/* check entry form on submit */
function checkform() {
	var myform = document.forms[0];
	var alerttext = "";
	
	/* hide all attentiontips */
	document.getElementById('aquelle').style.visibility="hidden";
	document.getElementById('adatei').style.visibility="hidden";
	document.getElementById('atitel').style.visibility="hidden";
	document.getElementById('akeywords').style.visibility="hidden";
	document.getElementById('ainst').style.visibility="hidden";

	
	/* quelle */
	if (myform.quelle.value=="" && (myform.quelleneu.value.length<3 || myform.quelleneu.value=="Geben Sie die neue Quelle ein")) {
		alerttext = "\n* Quelle\n --> Bitte legen Sie ggf. eine neue an. ";
		stack2[k++] = "aquelle";
	}
	if (myform.datei.value=="") {
		alerttext += "\n* Datei zum Hochladen\n --> Bitte wählen Sie eine PDF, JPG, PNG oder GIF-Datei \n --> auf Ihrem Rechner aus. ";
		stack2[k++] = "adatei";
	}
	if (myform.titel.value=="") {
		alerttext += "\n* Titel\n --> Geben Sie einen Titel ein, der den Kern des Artikels trifft. ";
		stack2[k++] = "atitel";
	}
	if (myform.keywords.value=="") {
		alerttext += "\n* Stichwörter\n --> Geben Sie bitte mindestens ein Stichwort ein, dass den Artikel beschreibt. ";
		stack2[k++] = "akeywords";
	}
	/*
	// selectcheck IE problems	
	if(myform.inst.value=="" || myform.inst.value=="Wählen Sie mind. einen Eintrag") {
		alerttext += "\n* Bereiche\n --> Bitte wählen Sie mindestens einen Bereich aus, auf dessen Seite der Artikel erscheinen soll. ";
		stack2[k++] = "ainst";
	}
	
	if(multipleWert(myform.inst, '')=="" || multipleWert(myform.inst, '')=="Wählen Sie mind. einen Eintrag") {
		alerttext += "\n* Bereiche\n --> Bitte wählen Sie mindestens einen Bereich aus, auf dessen Seite der Artikel erscheinen soll. ";
		alert(myform.inst.options[1].length);
		stack2[k++] = "ainst";
	}
	*/
	/* ok, send */
	if (alerttext=="") {
		return true;
	}
	/* alert */
	zeige2();
	alert("Leider sind folgende Felder nicht ausgefüllt:" + alerttext);
	return false;
}
