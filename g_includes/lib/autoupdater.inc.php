<?php 
if ('admin.scripts.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

define('PHPFANLIST_AUTOUPDATEVERSION', '3.1.1.');

$sql->query('SELECT value FROM ' . $table_name . '_settings WHERE setting=\'version\'');
if ($sql->query_ok()) {
	$row = $sql->fetch_array();
	$version = $row['value'];
	if (PHPFANLIST_AUTOUPDATEVERSION > $version) {
		if (autoupdate($version)) {
			$message = "\n" . 'Your phpFanList installation has succesfully been updated to version ' . PHPFANLIST_AUTOUPDATEVERSION;
			register();
		}
	}
}

function autoupdate($version) {
	global $table_name, $sql;
	$result = false;
	$queries = array();
	switch($version) {
		case '3.0.0.':
			array_push($queries, 'INSERT INTO ' . $table_name . '_settings VALUES (\'is_xhtml\', \'1\')');
		case '3.0.1.':
			array_push($queries, 'INSERT INTO ' . $table_name . '_settings VALUES (\'advanced_mailcheck\', \'1\')');

			// This to compensate for a bug when updating from 2.x (fixed in 3.1.0)
			array_push($queries, 'ALTER TABLE ' . $table_name . '_temp CHANGE dateadd dateadd TIMESTAMP( 14 ) NOT NULL default CURRENT_TIMESTAMP') ;
		case '3.1.0.':
			array_push($queries, 'INSERT INTO ' . $table_name . '_settings VALUES (\'spam_words\', \'\')');
		
			// Required in every verion
			array_push($queries, 'UPDATE ' . $table_name . '_settings  SET value=UNIX_TIMESTAMP(NOW()) WHERE setting=\'last_update\'');
			array_push($queries, 'UPDATE ' . $table_name . '_settings SET value=\'' . PHPFANLIST_AUTOUPDATEVERSION . '\' WHERE setting=\'version\'');
		break;
	}
	if (count($queries) > 0) {
		$size = count($queries);
		$i = 0;
		foreach ($queries as $query) {
			$sql->query($query);
			$sql->query_ok() ? $i++ : NULL;				
			}
		$result = ($i == $size);
	}
	return $result;
}

function register() 
{
	$url = 'http://www.phpfanlist.com/register.php?t=phpfl&v=' . urlencode(PHPFANLIST_AUTOUPDATEVERSION) . '&u=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	
	$fp = fsockopen('www.phpfanlist.com', 80, $err_no, $err_str, 5);
	if ($fp) {
	    $out = "GET " . $url . " HTTP/1.1\r\n";
	    $out .= "Host: www.phpfanlist.com\r\n";
		$out .= "User-Agent: phpFanList Autoupdater\r\n";
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