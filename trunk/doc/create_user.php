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

/*
# L�sche 'tester'@'localhost' ...
REVOKE ALL PRIVILEGES ON * . * FROM 'tester'@'localhost';
REVOKE ALL PRIVILEGES ON `mysql` . * FROM 'tester'@'localhost';
DROP USER 'tester'@ 'localhost';
*/