<?php
// $Id$
// Statische Seiten erzeugen
error_reporting(E_ALL);
require("init.php");
require(I_PATH."press_output.class.php");
require(I_PATH."press_sites.class.php");

$PO = new press_output($SQL,$DBG);
$PS = new press_sites($SQL,$DBG);
//echo $PO->show_all4(init("kuerzel","r",""));	// unvollst. dokument anzeigen!


$PO->make_static("");  // schreibe cache file für ALLE einträge

// alle kuerzel besorgen
$kuerzel = $PS->get_all_kuerzel();
foreach($kuerzel as $institut) {
	$PO->make_static($institut);  // dto schreibe cache file	
}
echo "cache aktualisiert.";		
?>