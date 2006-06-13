<?php
/*
 * Created on 08.09.2005
 *
 * Copyright by Christoph Becker <cbecker@nachtwach.de>
 */

function javascripts($array) {
	if (!is_array($array)) {return ""; }
	if (count($array)<0) {return ""; }
	$out = "";
	reset ($array);
	while (list (, $val) = each ($array)) {
		$out .= "<script src=\"".$val."\" type=\"text/JavaScript\"></script>";
	}
	return $out;
}

function css($array) {
	if (!is_array($array)) {return ""; }
	if (count($array)<0) {return ""; }
	$out = "";
	reset ($array);
	while (list (, $val) = each ($array)) {
		$out .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$val."\" />";
	}
	return $out;
}
?>