<?php 
// Includes all the required things needed on every page (admin and front) AND don't need a $fanlisting object
if ('base.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
require_once(realpath(PHPFANLIST_INCLUDES . 'lib/mysql.inc.php'));

$db = &new DB($db_host, $db_user, $db_pass, $db_name);
$sql = &new SQL($db->handle);

require_once(realpath(PHPFANLIST_INCLUDES . 'lib/clean.inc.php'));
require_once(realpath(PHPFANLIST_INCLUDES . 'lib/display.inc.php'));
$display = &new Display();
?>