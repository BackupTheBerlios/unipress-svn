/* check entry form on submit */
function checksubmit () {
	f = document.myform;
	if(f.hiddenexample.value==1) { 
		alert("Sie duerfen das Beispiel nicht eintragen!\nEs dient nur zur Veranschaulichung.\nBitte machen Sie sinnvolle Angaben."); 
		return false; 
	}

	var myform = f; //document.forms[0];
	var alerttext = "";
	
	/* hide all attentiontips */
	document.getElementById('aname').style.visibility="hidden";
	document.getElementById('akuerzel').style.visibility="hidden";

	if (myform.name.value=="") {
		alerttext += "\n* Bereichsname\n --> Wählen Sie bitte einen Namen, der den Bereich beschreibt. z.Bsp: Institut für angewandte Forschung.-";
		stack2[k++] = "aname";
	}
	if (myform.kuerzel.value=="") {
		alerttext += "\n* Kürzel\n --> Geben sie ein Kürzel für den Bereich ein. z.Bsp.: IAF";
		stack2[k++] = "akuerzel";
	}

	
	/* ok, send */
	if (alerttext=="") {
		return true;
	}
	/* alert */
	zeige2();
	alert("Leider sind folgende Felder nicht ausgefüllt:" + alerttext);
	return false;
}
