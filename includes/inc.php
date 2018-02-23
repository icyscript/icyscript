<?php 
// Gets the global includes directory and loads the basic needs.
$err_setting = error_reporting(0);
if ('inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

require_once('config.inc.php');

$connect = mysql_connect($db_host, $db_user, $db_pass);
if ($connect) { mysql_select_db ($db_name); } else { die('No database connection.'); }
$result = mysql_query('SELECT value FROM ' . $table_name . '_settings WHERE setting=\'global_includedir\'');
if ($result && (mysql_num_rows($result) > 0)) {
	$row = mysql_fetch_array($result);
	define('PHPFANLIST_INCLUDES', $row['value']);
} 
mysql_close($connect);

if (!defined('PHPFANLIST_INCLUDES')) {
	die('phpFanList was not installed correctly!');
}

if (!is_dir(PHPFANLIST_INCLUDES)) { die('Security Error - Invalid Directory.'); }
require_once(realpath(PHPFANLIST_INCLUDES . 'lib/base.inc.php'));
?>