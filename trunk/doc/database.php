<?php
//define prefix!
if (!isset($prefix)) {$prefix = "press_";}

// tables
$table = array();
$table['user'] = "
CREATE TABLE `".$prefix."user` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 12 ) NOT NULL ,
`pass` VARCHAR( 40 ) NOT NULL ,
`counter` INT( 2 ) NOT NULL ,
`session` VARCHAR( 40 ) NOT NULL,
`admin` SET( '1', '0' ) DEFAULT '0' NOT NULL,
PRIMARY KEY ( `id` ) ,
INDEX ( `pass` ) ,
UNIQUE (
`name`
)
) TYPE = MyISAM COMMENT = 'usertable';
";

// user extenstion table
$table['press_user'] = "
CREATE TABLE `".$prefix."press_user` (
`id` INT( 5 ) NOT NULL ,
`adminlevel` INT( 2 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `adminlevel` )
) TYPE = MyISAM ;
";

// sites
$table['press_sites'] = "
CREATE TABLE `".$prefix."press_sites` (
`id` INT( 4 ) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 70 ) NOT NULL ,
`kuerzel` VARCHAR( 5 ) NOT NULL,
PRIMARY KEY ( `id` ) ,
UNIQUE ( `name` ) ,
UNIQUE (`kuerzel`),
INDEX ( `name` )
) TYPE = MyISAM;
";

// entries
$table['press_entries'] = "CREATE TABLE `".$prefix."press_entries` (
`id` INT( 6 ) NOT NULL AUTO_INCREMENT,
`source` INT( 4 ) NOT NULL ,
`filename` VARCHAR( 150 ) NOT NULL ,
`link` VARCHAR( 150 ) NOT NULL ,
`title` VARCHAR( 150 ) NOT NULL ,
`date` DATE NULL DEFAULT 'CURDATE()',
PRIMARY KEY (`id` ),
FULLTEXT ( `title` )
) TYPE = MyISAM";

// keywords
$table['press_keywords'] = "CREATE TABLE `".$prefix."press_keywords` (
`id` INT( 6 ) NOT NULL AUTO_INCREMENT ,
`keyword` VARCHAR( 30 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `keyword` ) ,
FULLTEXT (
`keyword`
)
) TYPE = MYISAM ;";

// keyword-entry relation
$table['press_ke_rel'] = "CREATE TABLE `".$prefix."press_ke_rel` (
`eid` INT( 6 ) NOT NULL ,
`kid` INT( 6 ) NOT NULL ,
PRIMARY KEY ( `eid` , `kid` )
) TYPE = MYISAM ;";

// site-entry relation
$table['press_se_rel'] = "CREATE TABLE `".$prefix."press_se_rel` (
`eid` INT( 6 ) NOT NULL ,
`sid` INT( 4 ) NOT NULL ,
PRIMARY KEY ( `eid` , `sid` )
) TYPE = MYISAM ;";

// sources
$table['press_sources'] = "CREATE TABLE `".$prefix."press_sources` (
`id` INT( 4 ) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 80 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
UNIQUE (
`name`
)
) TYPE = MYISAM ;";

?>
