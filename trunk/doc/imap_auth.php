<?php
/* $Id$
 * 
 * Created on 21.02.2006 as part of unipress
 */

include ("../include/init.inc.php");




function loginFunction()
{
    /*
     * Change the HTML output so that it fits to your
     * application.
     */
    echo "<form method=\"post\" action=\"imap_auth.php\"><table>";
    echo "<tr><td>User</td><td><input type=\"text\" name=\"username\"></td></tr>";
    echo "<tr><td>Pass</td><td><input type=\"password\" name=\"password\"></td></tr>";
    echo "<tr><td valign=\"top\">Method</td><td><input type=\"radio\" name=\"method\" value=\"imap\">IMAP<br>"
    		."<input type=\"radio\" name=\"method\" value=\"imapssl\">IMAP/SSL<br>"
    		."<input type=\"radio\" name=\"method\" value=\"pop3\">POP3<br>"
    		."<input type=\"radio\" name=\"method\" value=\"simap\">simple IMAP<br>";
    echo "<tr><td>&nbsp;</td><td><input value=\"Ok\" type=\"submit\"></td></tr></table>";
    echo "</form>";
}


?>
<html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta name="GENERATOR" content="PHPEclipse 1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>title</title>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#FF9966" vlink="#FF9966" alink="#FFCC99">
<?php
loginFunction();
$pass = init("password","p",FALSE);
$user = init("username","p",FALSE);
$meth = init("method","p","imap");

if($user && $pass) {
	//$imap_server = "imap.ch-becker.de";
	$imap_server = "mail.uni-rostock.de";
	$pop3_server = $imap_server;
	switch($meth) {
		case 'simap':
			$mbox = fsockopen($imap_server,143,$errno, $errmsg,15);
			stream_set_blocking ( $mbox, 1 );
			$reti = fgetss($mbox); 	//echo "<br>".$reti;
			fputs($mbox,"a001 LOGIN ".$user." ".$pass."\n\r");
			$reti = fgetss($mbox); 	//echo "<br>".$reti;
			if (eregi("logged in",$reti)) $mbox=true; else $mbox=false; 
			break;
		case 'imap':
			$mbox = @imap_open("{".$imap_server."}", $user, $pass);
			break;
		case 'imapssl':
			$mbox = @imap_open ("{".$imap_server.":993/imap/ssl}INBOX", $user, $pass);
			break;
		case 'pop3':
			$mbox = @imap_open ("{".$pop3_server.":110/pop3}INBOX", $user, $pass);
			break;
	}
	if ($mbox==true) { echo "Authentifiziert!"; } else { echo "Nicht authentifiziert!"; }
} else echo $user. " - " . $pass ." - ".($user!=0 and $pass!=0);
?>
</body>
</html>
  

