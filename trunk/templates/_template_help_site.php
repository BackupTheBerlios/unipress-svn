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
	 Typischerweise w�hlen Sie hier den Instituts- oder Wissenschaftsbereichsnamen wie z. Bsp.: Nachrichtentechnik.
</div>

<div class="tip" id="tsiteabr"><span id="menu">Hilfe </span>
	[K�rzel] Wie soll der Bereich abgek�rzt werden?<br />
	 Dieses K�rzel wird sp�ter zur Identifikation des Bereichs genutzt. Z. Bsp.: NT f�r Nachrichtentechnik<br />
	 Sie k�nnen maximal 5 Zeichen verwenden, GROSS/kleinschreibung ist egal.
</div>
<?php
} elseif ($actual=="edits") {
 ?>
<div class="tip" id="tsitename"><span id="menu">Hilfe </span>
	[Bereichsname] Wie soll der Bereich heissen?<br />
	 Typischerweise w�hlen Sie hier den Instituts- oder Wissenschaftsbereichsnamen wie z. Bsp.: Nachrichtentechnik.
</div>

<div class="tip" id="tsiteabr"><span id="menu">Hilfe </span>
	[K�rzel] Wie soll der Bereich abgek�rzt werden?<br />
	 Dieses K�rzel wird sp�ter zur Identifikation des Bereichs genutzt. Z. Bsp.: NT f�r Nachrichtentechnik<br />
	 Sie k�nnen maximal 5 Zeichen verwenden, GROSS/kleinschreibung ist egal.
</div>
<?php	
}
?>