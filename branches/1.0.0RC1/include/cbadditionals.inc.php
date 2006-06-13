<?php
/**
 * additional functions for MySQL Class V 2.9.0 & above
 * 
 * @link http://php.ch-becker.de/ some scripts
 * @see MySQL
 * @version 0.1.2
 * @copyright 2003-11-25 cbecker@nachtwach.de
 */
/**
 * some ToDo infos for developers
 * 
 * @todo -o"CB" -c"data2file" error handler? inlcude function into class?
 * @done -o"CB" -c"data2file" function created
 */

/**
 * array2csv()
 * 
 * converts any 2-d array into csv string
 * 
 * @param array $in_array 
 * @return string csv data
 */
function array2csv ( $in_array )
{
	$_lf = "\n"; // linefeed & carrige return
	$csv_data = "";
	$line_nr = 0;
	$stack = array();
	while ( list ( $r_key, $array2 ) = each ( $in_array ) ) {
		// exists a secound dimension ?
		if ( !is_array( $array2 ) ) {
			return false;
		} 
		$semikolon2 = "";
		while ( list ( $key, $val ) = each ( $array2 ) ) {
			// if there is another simension - quit with false
			if ( is_array( $val ) ) {
				return false;
			} 
			if ( !ereg( "^[0-9]", $key ) ) {
				if ( $line_nr == 0 && !in_array( $key, $stack ) ) {
					array_push( $stack, $key );
				} // table header					
				$csv_data .= $semikolon2 . "\"" . $val . "\"";
				$semikolon2 = ";";
			} 
		} // inner while
		$line_nr++;
		$csv_data .= $_lf;
	} // while 
	// print ("<br>");print_r($stack);
	return implode( ";", $stack ) . $_lf . $csv_data;
} 

/**
 * data2file() shows browsers file save dialog for generated data
 * 
 * @param string $data data to send
 * @param string $filename filename in dialog
 * @param string $kind kind of filename, e.g. csv data
 * @param string $create_file should i try to (1) create a file instead of (0) sending? or (2) append?
 * @param string $tablename only with $create_file=2 - as seperator
 * @return file .
 */
function data2file ( $data, $filename = "data", $kind = "csv", $create_file = 0, $tablename = "" )
{
	$download_size = strlen( $data );
	switch ( $create_file ) {
		default:
		case 0:
			header( "Content-type: application/" . $kind );
			header( "Content-Disposition: attachment; filename=$filename.$kind;" );
			header( "Accept-Ranges: bytes" );
			header( "Pragma: no-cache" );
			header( "Content-Length: $download_size" ); 
			// @readfile($file);
			echo $data;
			return true; // do not need following break
		case 1:
			$handle = fopen( $filename . "." . $kind, "w" );
			if ( $handle != false ) {
				if ( fwrite( $handle, $data ) ) {
					fclose( $handle );
					return true;
				} else {
					return false;
				} 
			} else {
				return false;
			} 
		case 2:
			$handle = fopen( $filename . "." . $kind, "a" );
			if ( $handle != false ) {
				fwrite( $handle, "\n###|$tablename|###\n" );
				if ( fwrite( $handle, $data ) ) {
					fclose( $handle );
				} else {
					return false;
				} 
			} else {
				return false;
			} 
			return true;
	} // switch 
	// unknown command
	return false;
} 

?>