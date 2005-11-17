<?php
// Version 1.1
function JavaScript_include($file,$dir="",$no_files="ich_nicht.js|ich_auch_nicht.js")
{
	GLOBAL $INCLUDE_PATH;

	__include(array("JavaScript","f_read"));
	
	/* notwendige Funktionen:
	f_read()
	f_write()
	JavaScript()
	*/
	GLOBAL $DBG;												// Debug-Modus, wenn =1, dann wird der Code direkt in HTML-Code geschrieben
	if($dir==false && file_exists($file)==true)					// einzelne Datei inkludieren
	{
		if($DBG==false)											// wenn kein DBG, dann einfach nur inkludieren
		{
			$r = "<script language=\"JavaScript\" src=\"".$file."\" type=\"text/JavaScript\"></script>";
		}
		else
		{
			$r = JavaScript(f_read($file));						// wenn DBG gesetzt, dann JS-Code in HTML-Code
		}
	}
	elseif(is_dir($dir)==true && $file==false)					// ganzes Verzeichnis inkludieren
	{
	
		$han=opendir($dir);										// Verzeichnis öffnen
		$dir!="." ? $dir .= "/" : $dir="";						// Slash an übergebnen Verzeichnisnamen anhängen
		$c = "";
		while($file=readdir($han))								// Verzeichnis mit allen Dateien durchlaufen
		{ 
			if(
				$file!="."			&&							// auslassen von ., .. und der Includedatei
				$file!=".."			&&
				eregi($no_files,$file)==false &&				// auslassen der o.a. Dateien
				substr($file,strrpos($file,".")-3)=="inc.js"	// Endung muß inc.js sein für automatisches includieren
			  )
			{
				$c .= "\r\n".f_read($dir.$file);				// lesen der Datei
			}
		} //# while()
		closedir($han);											// Verzeichnis-Handle schließen
		if($DBG==false)											// wenn DBG nicht gesetzt, dann alles in eine Datei schreiben
		{
			if(file_exists($INCLUDE_PATH."js.js")==false)						// nur schreiben, wenn Datei nicht existiert
			{
				// hier sollten noch überflüssige Elemente entfernt werden
				// wie // oder /* */ und Leerzeichen und Zeilenumbrüche
				$c = "/*\r\nAchtung: dynamisch erzeugte Datei!\r\nfür Neugenerierung einfach löschen\r\n*/\r\n".$c;
				f_write($INCLUDE_PATH."js.js",$c);
			}
			$r = "<script language=\"JavaScript\" src=\"".$INCLUDE_PATH."js.js\" type=\"text/JavaScript\"></script>";
		}
		else													// DBG-Modus, alles in HTML-Code einfügen
		{
			$r = JavaScript($c);
		}
	}
	else
	{
		$r = JavaScript("alert(\"Aufruf JavaScript_include(".addslashes(htmlentities($file.",".$dir)).") nicht korrekt\");");
	}
	return $r;
}
// 2003-04-24 CD: inkludieren von Verzeichnissen hinzugefügt
?>