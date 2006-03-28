<?php
// $Id$
// Statische Seiten erzeugen
require("init.php");
require(I_PATH."press_output.class.php");

$PO = new press_output($SQL,$DBG);

echo $PO->show_all4(init("kuerzel","r",""));	// unvollst. dokument
	 $PO->make_static(init("kuerzel","r",""));  // dto schreibe
		
		?>