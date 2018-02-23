<?php
if ('inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
require('../includes/config.inc.php');

define('PHPFANLIST_VERSION_INSTALL', '3.1.1.');
define('PHPFANLIST_VERSION_UPDATE', '2.1.0.');
define('PHPFANLIST_REQ_PHP', '4.3');
define('PHPFANLIST_REQ_MYSQL', '4.1');

$connect = mysql_connect($db_host, $db_user, $db_pass);
$db_select = false;
if ($connect) { 
	$db_select = mysql_select_db ($db_name); 
	define('PHPFANLIST_MYSQL', mysql_get_server_info($connect));
} else {
	define('PHPFANLIST_MYSQL', '');
}


function do_insert ($do = NULL) {
	// Version 3.1.1.
	global $connect, $table_name;
	$x = pathinfo($_SERVER['PHP_SELF']);
	$pos = strpos(strtolower($x['dirname']), '/install');
	if ($pos > 0) {
		$x['dirname'] = substr($x['dirname'], 0, strlen($x['dirname']) - (strlen($x['dirname']) - $pos));
	}
	
	$settings_sql  = 'INSERT INTO ' . $table_name . '_settings (setting, value) VALUES ';
	$settings_sql .= "('doc_root', '" . $_SERVER['DOCUMENT_ROOT'] . "'), ";
	$settings_sql .= "('dir_name', '" . $x['dirname'] . "'), ";
	$settings_sql .= "('global_includedir', 'g_includes/'), ";
	$settings_sql .= "('list_type', '0'), ";
	$settings_sql .= "('approved', '0'), ";
	$settings_sql .= "('date_format', 'dS F Y'), ";
	$settings_sql .= "('last_update', UNIX_TIMESTAMP(NOW())), ";
	$settings_sql .= "('start_date', UNIX_TIMESTAMP(NOW())), ";
	$settings_sql .= "('allow_doublemail', '0'), ";
	$settings_sql .= "('allow_memberdelete', '1'), ";
	$settings_sql .= "('custom_field_name', ''), ";
	$settings_sql .= "('ask_custom', '0'), ";
	$settings_sql .= "('show_member_id', '1'), ";
	$settings_sql .= "('show_mail', '3'), ";
	$settings_sql .= "('site_name', ''), ";
	$settings_sql .= "('site_url', ''), ";
	$settings_sql .= "('site_css', 'style.css'), ";
	$settings_sql .= "('owner_name', ''), ";
	$settings_sql .= "('owner_mail', ''), ";
	$settings_sql .= "('mail_on_join', '1'), ";
	$settings_sql .= "('mail_admin', '1'), ";
	$settings_sql .= "('mail_approve', '1'), ";
	$settings_sql .= "('rules_question', 'Did you read the rules?'), ";
	$settings_sql .= "('rules_answer', 'yes'), ";
	$settings_sql .= "('ask_rules', '0'), ";
	$settings_sql .= "('ask_url', '1'), ";
	$settings_sql .= "('admin_name', 'admin'), ";
	$settings_sql .= "('admin_pass', 'admin'), ";
	$settings_sql .= "('version', '" . PHPFANLIST_VERSION_INSTALL . "'), ";
	$settings_sql .= "('ask_country', '2'), ";
	$settings_sql .= "('autoinicap', '1'), ";
	$settings_sql .= "('show_custom', '0'), ";
	$settings_sql .= "('show_url', '1'), ";
	$settings_sql .= "('first_run', '1'), ";
	$settings_sql .= "('cookie_lifetime', '60'), ";
	$settings_sql .= "('last_checked', UNIX_TIMESTAMP(NOW())), ";
	$settings_sql .= "('web_includedir', 'web_includes/'), ";
	$settings_sql .= "('is_expert', '0'), ";
	$settings_sql .= "('lastx', '10'), ";
	$settings_sql .= "('plugins', NULL), ";
	$settings_sql .= "('url_nofollow', '0'), ";
	$settings_sql .= "('show_legend', '1'), ";
	$settings_sql .= "('allow_pluralupdate', '0'), ";
	$settings_sql .= "('max_comment', '1000'), ";
	$settings_sql .= "('default_list_sort', 'all'), ";
	$settings_sql .= "('default_list_order', 'name'), ";
	$settings_sql .= "('timediff', '0'), ";
	$settings_sql .= "('show_num_newsitems', '10'), ";
	$settings_sql .= "('check_latest', '1'), ";
	$settings_sql .= "('custom_field_format', ''), ";
	$settings_sql .= "('is_xhtml', '0'), ";
	$settings_sql .= "('advanced_mailcheck', '1'), ";
	$settings_sql .= "('spam_words', '');";

	return (true & mysql_query($settings_sql, $connect));
}

function do_install() {
	// Version 3.1.1.
	global $connect, $table_name;
	$phpfl_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' (id mediumint(4) NOT NULL auto_increment, ';
	$phpfl_sql .= 'name varchar(100) NOT NULL default \'\', country varchar(150) default NULL, mail varchar(255) NOT NULL default \'\', ';
	$phpfl_sql .= 'url varchar(255) default NULL, custom varchar(255) default NULL, dateofadd date default NULL, lastupdate date default NULL, ';
	$phpfl_sql .= 'showmail tinyint(1) default \'1\', PRIMARY KEY  (id)) ENGINE=MyISAM COMMENT=\'PHPFANLIST\';';
	
	$phpfl_temp_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . '_temp (tempid mediumint(4) NOT NULL auto_increment, ';
	$phpfl_temp_sql .= 'name varchar(100) default NULL, country varchar(150) default NULL, mail varchar(255) default NULL, ';
	$phpfl_temp_sql .= 'url varchar(255) default NULL, comment text, custom varchar(255) default NULL, rules varchar(255) default NULL, ';
	$phpfl_temp_sql .= 'IP varchar(15) default NULL, showmail tinyint(1) default \'1\', dateadd timestamp NOT NULL default CURRENT_TIMESTAMP, ';
	$phpfl_temp_sql .= 'action mediumint(4) default \'0\', mid mediumint(4) default NULL, PRIMARY KEY  (tempid)) ENGINE=MyISAM;';	
	
	$phpfl_settings_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . '_settings (setting varchar(40) NOT NULL default \'\', ';
	$phpfl_settings_sql .= 'value varchar(255) default NULL, PRIMARY KEY setting (setting)) ENGINE=MyISAM;';
	
	$phpfl_affiliates_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . '_affiliates (id mediumint(6) NOT NULL auto_increment, ';
	$phpfl_affiliates_sql .= 'name varchar(50) NOT NULL default \'\', url varchar(255) default NULL, imageurl varchar(255) default NULL, ';
	$phpfl_affiliates_sql .= 'category varchar(100) default NULL, dateadd date default NULL, PRIMARY KEY  (id),';
	$phpfl_affiliates_sql .= ' UNIQUE KEY name (name)) ENGINE=MyISAM;';
	
	$phpfl_news_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . '_news (id mediumint(6) NOT NULL auto_increment, ';
	$phpfl_news_sql .= 'title varchar(255) NOT NULL default \'\', content text NOT NULL, dateadd timestamp NOT NULL default CURRENT_TIMESTAMP, ';
	$phpfl_news_sql .= 'PRIMARY KEY  (id) ) ENGINE=MyISAM;';
	
	$result = (($connect == true) & true);
	
	if ($result) { $result &= (true & mysql_query($phpfl_sql, $connect)); }
	if ($result) { $result &= (true & mysql_query($phpfl_temp_sql, $connect)); }
	if ($result) { $result &= (true & mysql_query($phpfl_settings_sql, $connect)); }
	if ($result) { $result &= (true & mysql_query($phpfl_affiliates_sql, $connect)); }
	if ($result) { $result &= (true & mysql_query($phpfl_news_sql, $connect)); }
	
	return $result;
}

function do_update() {
	// Version 3.0.1.
	global $connect, $table_name;
	$result = false;
	$sql = 'SELECT value FROM ' . $table_name . '_settings WHERE setting = \'version\'';
	$result = mysql_query($sql, $connect);
	if ($result && (mysql_num_rows($result) == 1)) {
		$row = mysql_fetch_array($result);
		$previous_version = $row['value'];
		$sql_array = array();
		switch($previous_version) {
			case '2.1.0.':
			case '2.1.1.':
			case '2.2.0.':
			case '2.2.1.':
				array_push($sql_array, 'ALTER TABLE ' . $table_name . '_temp CHANGE comment comment TEXT NULL DEFAULT NULL');
			case '2.3.0.':
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings values (\'last_checked\', UNIX_TIMESTAMP(NOW()))');
			case '2.3.1.':
				$phpfl_affiliates_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . '_affiliates (id mediumint(6) NOT NULL auto_increment, ';
				$phpfl_affiliates_sql .= 'name varchar(50) NOT NULL default \'\', url varchar(255) default NULL, imageurl varchar(255) default NULL, ';
				$phpfl_affiliates_sql .= 'category varchar(100) default NULL, dateadd date default NULL, PRIMARY KEY  (id),';
				$phpfl_affiliates_sql .= ' UNIQUE KEY name (name)) ENGINE=MyISAM;';
		
				$phpfl_news_sql  = 'CREATE TABLE IF NOT EXISTS ' . $table_name . '_news (id mediumint(6) NOT NULL auto_increment, ';
				$phpfl_news_sql .= 'title varchar(255) NOT NULL default \'\', content text NOT NULL, dateadd timestamp NOT NULL default CURRENT_TIMESTAMP, ';
				$phpfl_news_sql .= 'PRIMARY KEY  (id) ) ENGINE=MyISAM;';
				
				array_push($sql_array, $phpfl_affiliates_sql);
				array_push($sql_array, $phpfl_news_sql);
				
				array_push($sql_array, 'ALTER TABLE ' . $table_name . '_settings DROP INDEX setting , ADD PRIMARY KEY (setting);'); 
			
				array_push($sql_array, 'ALTER TABLE ' . $table_name . ' ADD lastupdate DATE NULL AFTER dateofadd') ;
				array_push($sql_array, 'ALTER TABLE ' . $table_name . '_temp CHANGE rulez rules VARCHAR( 255 )');
				array_push($sql_array, 'ALTER TABLE ' . $table_name . '_temp ADD mid MEDIUMINT( 4 ) NULL DEFAULT NULL') ;
				array_push($sql_array, 'ALTER TABLE ' . $table_name . '_temp ADD dateadd TIMESTAMP( 14 ) NOT NULL default CURRENT_TIMESTAMP') ;

				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'cookie_lifetime\', \'60\'), (\'list_type\', \'0\'), (\'is_expert\', \'0\')');
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'lastx\', \'10\'), (\'plugins\', NULL), (\'web_includedir\', \'web_includes/\')');
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'url_nofollow\', \'0\'), (\'allow_pluralupdate\', \'0\')');
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'max_comment\', \'1000\'), (\'default_list_sort\', \'all\'), (\'default_list_order\', \'id\')');
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'show_legend\', \'1\'), (\'timediff\', \'0\')');
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'show_num_newsitems\', \'10\'), (\'check_latest\', \'1\'), (\'custom_field_format\', \'\')');

				array_push($sql_array, 'UPDATE ' . $table_name . '_settings SET setting=\'ask_url\' WHERE setting=\'website_required\'');
				array_push($sql_array, 'UPDATE ' . $table_name . '_settings SET setting=\'ask_rules\' WHERE setting=\'rules_required\'');
				array_push($sql_array, 'UPDATE ' . $table_name . '_settings SET setting=\'ask_custom\' WHERE setting=\'custom_required\'');

				array_push($sql_array, 'DELETE FROM ' . $table_name . '_settings WHERE setting=\'beta\'');
				array_push($sql_array, 'DELETE FROM ' . $table_name . '_settings WHERE setting=\'clique\'');
				array_push($sql_array, 'DELETE FROM ' . $table_name . '_settings WHERE setting=\'plugin_countryflag\'');

			case '3.0.0.':
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'is_xhtml\', \'1\')');
			
			case '3.0.1.':
				array_push($sql_array, 'INSERT INTO ' . $table_name . '_settings VALUES (\'advanced_mailcheck\', \'1\')');
				// This to compensate for a bug when updating from 2.x (fixed in 3.1.0)
				array_push($sql_array, 'ALTER TABLE ' . $table_name . '_temp CHANGE dateadd dateadd TIMESTAMP( 14 ) NOT NULL default CURRENT_TIMESTAMP') ;
			case '3.1.0.':
				array_push($queries, 'INSERT INTO ' . $table_name . '_settings VALUES (\'spam_words\', \'\')');

				// Needed with all updates
				array_push($sql_array, 'UPDATE ' . $table_name . '_settings  SET value=UNIX_TIMESTAMP(NOW()) WHERE setting=\'last_update\'');
				array_push($sql_array, 'UPDATE ' . $table_name . '_settings SET value=\'' . PHPFANLIST_VERSION_INSTALL . '\' WHERE setting=\'version\'');
				
				array_push($sql_array, 'OPTIMIZE TABLE ' . $table_name);
				array_push($sql_array, 'OPTIMIZE TABLE ' . $table_name . '_temp');
				array_push($sql_array, 'OPTIMIZE TABLE ' . $table_name . '_settings');
				break;
			default:
				break;
		}
		$size = count($sql_array);
		$i = 0;
		foreach ($sql_array as $sql) {
			$sql_result = mysql_query($sql, $connect);
			$sql_result ? $i++ : NULL;				
			}
		$result = ($i == $size);
	}
	if ($result) { register(); }
	return $result;
}

function register() 
{
	$url = 'http://www.phpfanlist.com/register.php?t=phpfl&v=' . urlencode(PHPFANLIST_VERSION_INSTALL) . '&u=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	
	$fp = fsockopen('www.phpfanlist.com', 80, $err_no, $err_str, 5);
	if ($fp) {
	    $out = "GET " . $url . " HTTP/1.1\r\n";
	    $out .= "Host: www.phpfanlist.com\r\n";
		$out .= "User-Agent: phpFanList Installer\r\n";
	    $out .= "Connection: Close\r\n\r\n";

	    fwrite($fp, $out);
		$buffer = '';
		stream_set_timeout($fp, 5);
	    while (!feof($fp)) {
	        $buffer .= fgets($fp, 256);
	    }
	    fclose($fp);
	}
}
?>