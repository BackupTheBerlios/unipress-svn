<?php
function JavaScript_onMouseOverStatus($str)
{
	// �bergeben wird eine Zeichenkette
	// zur�ckgegeben wird onMouseOverText f�r die Statuszeile und der html-Tag title="str"
	
	return "onMouseOver=\"window.status='".addslashes(str_replace("\"","",$str))."';return true\" title=\"".htmlentities($str)."\"";
}
?>