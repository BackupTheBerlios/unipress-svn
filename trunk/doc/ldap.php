<?php
// Grundlegende Abfolge bei LDAP ist verbinden, binden, suchen,
// interpretieren des Sucheergebnisses, Verbindung schlie�en

/* samaccountname=cbecker */

error_reporting(E_ERROR | E_WARNING);
//dl('ldap.so');

$search=$_POST['search'];

echo "LDAP query Test\n";
echo " Verbindung ...";
#$ds=ldap_connect("139.30.211.164");  // muss ein g�ltiger LDAP Server
$ds=ldap_connect("ldaps://nt1.rz.uni-rostock.de",678);  // muss ein g�ltiger LDAP Server
#$ds=ldap_connect("ldaps://localhost",678);  // muss ein g�ltiger LDAP Server
                               // sein!
if (!$ds) die (" fehlgeschlagen!");

#ldap_set_option($ds, GSLC_SSL_ONEWAY_AUTH, true);

   echo "Ergebnis der Verbindung: ".$ds."<p>";

if ($ds) {
   echo "Bindung ...";

	$r = ldap_bind($ds, "kf031", "theo05"); // authorisierter zugriff
   echo "Ergebnis der Bindung ".$r."<p>";

   echo "Suche nach ... ".$search."<br>";
   // Suchen des Nachnamen-Eintrags
#  $sr=ldap_search($ds,"o=jura, dc=DE", "sn=B*");
   $sr=ldap_search($ds," dc=uni-rostock, dc=DE", $search);
   echo "Ergebnis der Suche ".$sr."<p>";

die("died");

   echo "Anzahl gefundenen Eintr�ge ".ldap_count_entries($ds,$sr)."<p>";

   echo "Eintr�ge holen ...<p>";
   $info = ldap_get_entries($ds, $sr);
   echo "Daten f�r ".$info["count"]." Items gefunden:<p>";

   echo "<pre>";
   for ($i=0; $i<$info["count"]; $i++) {
       echo "dn ist: ". $info[$i]["dn"] ."<br>";
       echo "erster cn Eintrag: ". $info[$i]["cn"][0] ."<br>";
       echo "erster email Eintrag: ". $info[$i]["mail"][0] ."<p>";


      # print_r($info[$i]);
   }
   echo "</pre>";

   echo "Verbindung schlie�en";
   ldap_close($ds);

} else {
   echo "<h4>Verbindung zum LDAP Server nicht m�glich</h4>";
}
?>
<html><head><title>ldap test</title></head>
<body>
 <form action="ldap.php" method="post">
 <input type="text" name="search">
 <input type="submit">
 </form>
</body>
</html>
<?php
phpinfo();
?>
