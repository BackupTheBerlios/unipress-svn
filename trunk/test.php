<?php

echo 'Ihre PHP-Version: ' . phpversion() . '<br>';
$ver = (int) str_replace(".","",substr(phpversion(),0,5));

if (($ver >=380) && ($ver<442)) {
	echo "Ihre PHP Version ($ver) ist <b>alt</b>. M&ouml;glicherweise funktioniert nicht alles so, wie sie es erwarten.";
} elseif($ver<380) {
	echo "Die PHP Version ($ver) ist <b>zu alt</b>. <br>Bitte besorgen Sie eine neuere!";
}

echo '<br><br>MySQL: ';
if (function_exists("mysql_connect")){
	$link = mysql_connect('localhost', 'mpnq', 'elo6dir');
	if (!$link) {
	   die('keine Verbindung moeglich: ' . mysql_error());
	}
	echo 'Verbindung erfolgreich';
	mysql_close($link);
} else
{
		echo " 'mysql_connect' nicht gefunden. Bitte Test anpassen!";
		
}
?>
