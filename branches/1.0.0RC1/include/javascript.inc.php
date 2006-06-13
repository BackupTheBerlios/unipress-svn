<?php
function JavaScript($str)
{
	return	"\n<script type=\"text/javascript\"><!--"
			."\n".trim($str)
			."\n// --></script>";
}
?>