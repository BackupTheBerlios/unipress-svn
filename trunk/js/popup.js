function qneu() { 
	var breite=400; 
	var hoehe=400; 
	var datei = 'neu.php'; 
	var fname = 'popup'; 
	var posX=(screen.width/2)-(breite/2); 
	var posY=(screen.height/2)-(hoehe/2); 
	window.open(''+datei+'',
		''+fname+'',
		"toolbar=no,directories=no,status=no,scrollbars=yes,resize=no,resizable=no,menubar=no,width=" 
		+ breite + ",height=" + hoehe + ",screenX=" + posX + ",screenY=" 
		+ posY + ",left=" + posX + ",top=" + posY + "");
}