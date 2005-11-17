<?php
function JavaScript_onMouseOverStatus($str)
{
	// übergeben wird eine Zeichenkette
	// zurückgegeben wird onMouseOverText für die Statuszeile und der html-Tag title="str"
	
	return "onMouseOver=\"window.status='".addslashes(str_replace("\"","",$str))."';return true\" title=\"".htmlentities($str)."\"";
}
?>