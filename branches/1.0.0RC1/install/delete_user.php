<?php
/*
 * $Id$
 *
 * provides query for user deletion
 */
if (!$databasename) $databasename = "bsc";
if (!$username) $username = "bsc";
if (!$userpass) $userpass = "bsc";

$sql = "REVOKE ALL PRIVILEGES ON * . * FROM '".$username."'@'localhost'" .
$SQL->query($sql);

$sql = "REVOKE ALL PRIVILEGES ON '".$databasename."'.* FROM '".$username."'@'localhost'";
$SQL->query($sql);

$sql = "DROP USER '".$username."'@'localhost'";
$SQL->query($sql);

$sql = "FLUSH PRIVILEGES";
$SQL->query($sql);