<?php
/* $Id$
 * 
 * Created on 22.02.2006 as part of unipress
 */
 
require_once("../include/auth.class.php");
require_once("../include/init.inc.php");
function loginFunction()
{
    /*
     * Change the HTML output so that it fits to your
     * application.
     */
    echo "<form method=\"post\" action=\"myauth.php\"><table>";
    echo "<tr><td>User</td><td><input type=\"text\" name=\"username\"></td></tr>";
    echo "<tr><td>Pass</td><td><input type=\"password\" name=\"password\"></td></tr>";
    echo "<tr><td valign=\"top\">Method</td><td><input type=\"radio\" name=\"method\" value=\"imap\">IMAP<br>"
    		."<input type=\"radio\" name=\"method\" value=\"imapssl\">IMAP/SSL<br>"
    		."<input type=\"radio\" name=\"method\" value=\"pop3\">POP3<br>"
    		."<input type=\"radio\" name=\"method\" value=\"simap\">simple IMAP<br>";
    echo "<tr><td>&nbsp;</td><td><input value=\"Ok\" type=\"submit\"></td></tr></table>";
    echo "</form>";
}


?><html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta name="GENERATOR" content="PHPEclipse 1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>myauth test</title>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#FF9966" vlink="#FF9966" alink="#FFCC99">
<?php
loginFunction();
$pass = init("password","p",FALSE);
$user = init("username","p",FALSE);
$meth = init("method","p","simap");

if($user && $pass) {
	$a = new auth($meth, "mail.uni-rostock.de");
	if ($a->tested) {
		if($a->check($user, $pass)==true) echo "ok."; else echo "nönö";
	} else {
		echo "Verbindungstest fehlgeschlagen";	
	}
}
?>
</body>
</html>
  


