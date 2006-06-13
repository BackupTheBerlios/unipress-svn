<?php
/*
 * $Id:create_database.php 18 2006-03-20 12:50:53Z tuergeist $
 *
 * provides sql query to generate the database
 */
if (!$databasename) $databasename = "bsc";
$sql = "CREATE DATABASE `" . $databasename . "` DEFAULT CHARACTER " .
		"SET utf8 COLLATE utf8_general_ci;";
$SQL->query($sql);
?>
