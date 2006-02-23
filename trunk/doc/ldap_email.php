<?php
// Grundlegende Abfolge bei LDAP ist verbinden, binden, suchen,
// interpretieren des Sucheergebnisses, Verbindung schließen

/* samaccountname=cbecker */
$path	=	"c:/programme/redmon/";
$fh = fopen ($path."php.log","a");
$fh2 = fopen ($path."setmailadr.bat","w");

$user	=	$argv[1];
if ($user=="") {$user ="cbecker";}
fwrite($fh, "User: ".$user."\n");

$search="samaccountname=".$user;

$ds	=	ldap_connect("139.30.211.164");  // muss ein gültiger LDAP Server
$r	=	ldap_bind($ds, "cbecker@jura.uni-rostock.de", "19521914"); // authorisierter zugriff
$sr	=	ldap_search($ds,"dc=jura, dc=uni-rostock, dc=DE", $search);

#echo "Anzahl gefundenen Einträge ".ldap_count_entries($ds,$sr)."<p>";

$info = ldap_get_entries($ds, $sr);	// einträge holen

#echo "Daten für ".$info["count"]." Items gefunden:<p>";
fwrite($fh, "items found: ".$info["count"]."\n");

fwrite($fh2, "set mailadress=".$info[0]["mail"][0]); // emailadresse
fwrite($fh,  "mail: ".$info[0]["mail"][0]."\n");

// Verbindung schließen
fclose($fh);
fclose($fh2);
ldap_close($ds);
?>