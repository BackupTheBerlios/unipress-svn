<?php
// $Id$
// lokaler Aufruf?
if($_SERVER['REMOTE_ADDR']!="") 
	die("Nur lokale Aufrufe erlaubt! ".
		"Benutze \$php statiker.php an der Konsole oder als Cronjob");



// Statische Seiten erzeugen
error_reporting(E_ALL);
require("init.php");
require(I_PATH."press_output.class.php");
require(I_PATH."press_sites.class.php");

echo "init.\n";
$PO = new press_output($SQL,$DBG);
$PS = new press_sites($SQL,$DBG);
//echo $PO->show_all4(init("kuerzel","r",""));	// unvollst. dokument anzeigen!

echo "making static\n";
$PO->make_static("");  // schreibe cache file für ALLE einträge

echo "getting sites...\n";
// alle kuerzel besorgen
$kuerzel = $PS->get_all_kuerzel();
echo "making static version for ...\n";
foreach($kuerzel as $institut) {
	echo " - ".$institut."\n";
	$PO->make_static($institut);  // dto schreibe cache file	
}
echo "cache refreshed\ndone.\n.";	

include ("init.php");
?>