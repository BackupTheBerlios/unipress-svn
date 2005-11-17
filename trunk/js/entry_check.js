/* js file for new entry */


/**
 * helper for select field 
 * from: http://www.dcljs.de/faq/antwort.php?Antwort=forms_radioselect
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


/* check entry form on submit */
function checksubmit () {
	f = document.myform;
	if(f.hiddenexample.value==1) { 
		alert("Sie dürfen das Beispiel nicht eintragen!\nEs dient nur zur Veranschaulichung.\nBitte machen Sie sinnvolle Angaben."); 
		return false; 
	}

	var myform = f; //document.forms[0];
	var alerttext = "";
	
	/* hide all attentiontips */
	//document.getElementById('asource').style.visibility="hidden";
	document.getElementById('apressfile').style.visibility="hidden";
	document.getElementById('atitle').style.visibility="hidden";
	document.getElementById('akeywords').style.visibility="hidden";
	document.getElementById('asource').style.visibility="hidden";

	
	/* quelle
	if (myform.quelle.value=="" && (myform.quelleneu.value.length<3 || myform.quelleneu.value=="Geben Sie die neue Quelle ein")) {
		alerttext = "\n* Quelle\n --> Bitte legen Sie ggf. eine neue an. ";
		stack2[k++] = "aquelle";
	} */
	if (myform.pressfile.value=="") {
		alerttext += "\n* Datei zum Hochladen\n --> Bitte wählen Sie eine PDF, JPG, PNG oder GIF-Datei \n --> auf Ihrem Rechner aus. ";
		stack2[k++] = "apressfile";
	}
	if (myform.title.value=="") {
		alerttext += "\n* Titel\n --> Geben Sie einen Titel ein, der den Kern des Artikels trifft. ";
		stack2[k++] = "atitle";
	}
	if (myform.keywords.value=="") {
		alerttext += "\n* Stichwörter\n --> Geben Sie bitte mindestens ein Stichwort ein, dass den Artikel beschreibt. ";
		stack2[k++] = "akeywords";
	}

	if ( myform.source.value=="-1" && myform.newsource.value=="") {
		alerttext += "\n* Quelle\n --> Bitte wählen Sie eine Quelle aus, oder erstellen Sie eine Neue.";
		stack2[k++] = "asource";
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
