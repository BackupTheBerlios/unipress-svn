<?php
/*
 * Created on 30.08.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "_template_help.php";
 
if ($actual=="news") {
 ?>
<div class="tip" id="tsitename"><span id="menu">Hilfe </span>
	[Bereichsname] Wie soll der Bereich heissen?<br />
	 Typischerweise wählen Sie hier den Instituts- oder Wissenschaftsbereichsnamen wie z. Bsp.: Nachrichtentechnik.
</div>

<div class="tip" id="tsiteabr"><span id="menu">Hilfe </span>
	[Kürzel] Wie soll der Bereich abgekürzt werden?<br />
	 Dieses Kürzel wird später zur Identifikation des Bereichs genutzt. Z. Bsp.: NT für Nachrichtentechnik<br />
	 Sie können maximal 5 Zeichen verwenden, GROSS/kleinschreibung ist egal.
</div>
<?php
} elseif ($actual=="edits") {
 ?>
<div class="tip" id="tsitename"><span id="menu">Hilfe </span>
	[Bereichsname] Wie soll der Bereich heissen?<br />
	 Typischerweise wählen Sie hier den Instituts- oder Wissenschaftsbereichsnamen wie z. Bsp.: Nachrichtentechnik.
</div>

<div class="tip" id="tsiteabr"><span id="menu">Hilfe </span>
	[Kürzel] Wie soll der Bereich abgekürzt werden?<br />
	 Dieses Kürzel wird später zur Identifikation des Bereichs genutzt. Z. Bsp.: NT für Nachrichtentechnik<br />
	 Sie können maximal 5 Zeichen verwenden, GROSS/kleinschreibung ist egal.
</div>
<?php	
}
?>