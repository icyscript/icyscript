<?php 
// Functions only required by the Admin UI
if ('functions.admin.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

require_once('functions.inc.php');

/* ASYNCHONOUS??? */
function checkVersion() {
	if (isset($_SESSION) && isset($_SESSION['phpfanlist_latest'])) {
		return $_SESSION['phpfanlist_latest'];
	}
	$result = false;
	$buffer = '';
	$err_no = -1;
	
	$fp = fsockopen('www.phpfanlist.com', 80, $err_no, $err_str, 5);
	if ($fp) {
	    $out = "GET http://www.phpfanlist.com/latestversion.txt HTTP/1.1\r\n";
	    $out .= "Host: www.phpfanlist.com\r\n";
		$out .= "User-Agent: phpFanList\r\n";
	    $out .= "Connection: Close\r\n\r\n";

	    fwrite($fp, $out);
		stream_set_timeout($fp, 5);
	    while (!feof($fp)) {
	        $buffer .= fgets($fp, 256);
	    }
	    fclose($fp);
	}

	if ($buffer != '' && $err_no == 0) {
		$buffer=split("\r\n\r\n",$buffer);
		if (count($buffer) > 1) {
        	$result = $buffer[1];
		}
	}

	if (isset($_SESSION)) {
		$_SESSION['phpfanlist_latest'] = $result;
	}

	return $result;
}

function addMessage($new_message) {
	global $message;
	if (($new_message != NULL) && (trim($new_message) != '')) {
		if ($message != '') {
			$message .= "\n";
		}
		$message .= $new_message;
	}
	return $new_message;
}

// Some functions to easy code to the eyes
function is_active($setting, $value=true) {
	if ($setting == $value) { echo ' selected="selected"'; }
	return true;
}
function is_checked($setting) {
	if ($setting) { echo ' checked="checked"'; }
	return true;
}
function is_disabled($setting) {
	if ($setting) { echo ' disabled="disabled"'; }
	return true;
}
?>