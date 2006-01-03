<?php
/*
 * $Id$
 *
 * provides query for user creation
 */
if (!$databasename) $databasename = "bsc";
if (!$username) $username = "bsc";
if (!$userpass) $userpass = "bsc";

$sql = "GRANT USAGE ON * . * TO '". $username ."'@'localhost' IDENTIFIED BY " .
		"'". $userpass ."' WITH MAX_QUERIES_PER_HOUR 0 " .
				"MAX_CONNECTIONS_PER_HOUR 0 " .
				"MAX_UPDATES_PER_HOUR 0 ;";
$SQL->query($sql);

$sql = "GRANT SELECT , INSERT , UPDATE , DELETE , CREATE , DROP , INDEX , " .
		"ALTER , CREATE TEMPORARY TABLES ON `". $databasename ."` . * TO '". $username ."'@'localhost';";
$SQL->query($sql);