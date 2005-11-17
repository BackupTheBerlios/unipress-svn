<?php
/*
 * Created on 07.11.2005
 *
 * provides sql query to generate the database
 */
if (!$databasename) $databasename = "bsc";
$sql = "CREATE DATABASE `" . $databasename . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
?>
