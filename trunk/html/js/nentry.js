/* little tiny bad stack for hide/show */
stack = new Array();
stack2 = new Array();
var i=0;
var k=0;
    
	 
/* show actual div */
function zeige(id)
{
    verstecke();
    document.getElementById(id).style.visibility="visible";
    stack[i++] = id;
}
/* hide all divs */
function verstecke()
{
    for (j=0; j<i;j++) {
        document.getElementById(stack[j]).style.visibility="hidden";
    }
    i=0;
}
/* show stacked divs*/
function zeige2()
{
    for (j=0; j<k;j++) {
        document.getElementById(stack2[j]).style.visibility="visible";
    }
   k=0;
}

/* Start; set focus to first field */
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
/* should I reset form? really? */
function ResetCheck () {
  var chk = window.confirm("Wollen Sie alle Eingaben loeschen?");
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

/* check form on submit */
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
		
	/* ok, send */
	if (alerttext=="") {
		return true;
	}
	/* alert */
	zeige2();
	alert("Leider sind folgende Felder nicht ausgefüllt:" + alerttext);
	return false;
}