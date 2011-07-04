<?php

/**
 * Creates a column array for writing new drivers
 * @author Marius Hein <mhein@netways.de>
 * @copyright (c) 2009 Icinga Developer Team
 * @version 1.0
 */

$o = getopt('d:u:p:t:s:P:h');

$h		= (isset($o['h'])) ? true : false;
$dsn	= (isset($o['d'])) ? $o['d'] : null;
$user	= (isset($o['u'])) ? $o['u'] : null;
$pass	= (isset($o['p'])) ? $o['p'] : null; 
$table	= (isset($o['t'])) ? $o['t'] : null;
$space	= (isset($o['s'])) ? $o['s'] : null;
$prefix	= (isset($o['P'])) ? $o['P'] : null;

if ($h || $dsn === null || $user === null || $pass === null || $table === null || $space === null || $prefix === null) {
	print_help();
	exit(1);
}

$pdo = new PDO($dsn, $user, $pass);

$query = $pdo->prepare(sprintf('DESCRIBE %s', $table));
$re = $query->execute();

if ($re) {
	$c = false;
	$out = 'array (';
	while ( ($o = $query->fetch(PDO::FETCH_OBJ)) ) {
		
		if ($c === true) $out .= ',';
		
		$out .= chr(10). chr(9);
		$out .= quoteVal( strtoupper(sprintf('%s_%s', $prefix, $o->Field)) );
		$out .= sprintf(' => array(%s, %s)', quoteVal($space), quoteVal(($o->Field)));
		
		if ($c === false) $c = true;
	}
	$out .= chr(10). ');';
	
	echo $out;
}

unset ($pdo);

exit (0);

function quoteVal($val) {
	return sprintf('\'%s\'', $val);	
}

function print_help() {
	echo 'create-col-array.php - v1.0'. chr(10);
	echo chr(10);
	echo 'Create a php array list based on a table to'. chr(10);
	echo 'create to drivers or add collumns, ...'. chr(10);
	echo chr(10);
	echo 'create-col-array.php -d <DSN> -u <user> -p <pass> -t <table> \\'. chr(10);
	echo chr(9). '-s <table_name_space> -P <column_prefix> [-h]'. chr(10);
	echo chr(10);
	echo chr(9). '-d  The dsn e.g. mysql:host=127.0.0.1;dbname=icinga'. chr(10);
	echo chr(9). '-u  Username to connect'. chr(10);
	echo chr(9). '-p  Password for the user'. chr(10);
	echo chr(9). '-s  Table alias based on query e.g. hs or h'. chr(10);
	echo chr(9). '-P  Field prefix, like HOST or SERVICE'. chr(10);
	echo chr(9). '-h  Displays this message and exit'. chr(10);
	echo chr(10);
}

?>