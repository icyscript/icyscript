<?php 
// Include file for front pages.
if ('front.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

// Get the database connection.
require_once('inc.php');
require_once(realpath(PHPFANLIST_INCLUDES . 'lib/fanlisting.front.inc.php'));
$fanlisting = new FrontFanlisting($sql, $table_name);

// Load functions
require_once(realpath(PHPFANLIST_INCLUDES . 'lib/functions.front.inc.php'));
?>