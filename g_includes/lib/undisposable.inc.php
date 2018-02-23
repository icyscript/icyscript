<?php 
// http://undisposable.org
// Version 0.3

if ('undisposable.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

// From undisposable_clients.zip/php/undisposable.inc.php	
// Rewritten to allow for servers that don't support file_get_contents
function undorg_isValidEmail($email) {
	$url = "http://www.undisposable.org/services/php/";
	$url .= "isValidEmail/?email=".addslashes($email);
	
	$fp = fsockopen('www.undisposable.org', 80, $err_no, $err_str, 15);
	$buffer = '';
	if ($fp) {
	    $out = "GET " . $url . " HTTP/1.1\r\n";
	    $out .= "Host: www.undisposable.org\r\n";
	    $out .= "Connection: Close\r\n\r\n";

	    fwrite($fp, $out);
		stream_set_timeout($fp, 5);
	    while (!feof($fp)) {
	        $buffer .= fgets($fp, 256);
	    }
	    fclose($fp);
	}
	$buffer = str_replace("\r", "", $buffer);
	$result = explode("\n\n", $buffer, 2);
	$uns = @unserialize($result[1]);
	if($uns['stat']=='ok')
		return $uns['email']['isvalid'];
	else
	    return false;
}
?>