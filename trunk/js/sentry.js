function checksubmit() {
	return true;
}

function checkreset () {
  var chk = window.confirm("Wollen Sie alle Eingaben loeschen?");
  	/* hide all attentiontips 
	document.getElementById('aquelle').style.visibility="hidden";
	document.getElementById('adatei').style.visibility="hidden";
	document.getElementById('atitel').style.visibility="hidden";
	document.getElementById('akeywords').style.visibility="hidden";
	document.getElementById('ainst').style.visibility="hidden";
	*/
  return (chk);
}

/* Start; set focus to first field
function startup()
{
	// focus to 
	// document.forms[0].sitename.focus();
	
}
 */
