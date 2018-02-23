<?php 
// Cleans up input variables
if ('clean.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
// Removes quotes when they are automatically added by Magic Quotes.
// This is unsafe for the common n00b, but we'll handle the quotes ourselves.
function remove_quotes($value) {
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	return $value;
}

// Protect against mallicious input from both get and post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	reset ($_POST);
	while (list ($key, $val) = each ($_POST)) {
		$_POST[$key] = str_replace('">', "", strip_tags(remove_quotes($val)));
	} 
} if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	reset ($_GET);
	while (list ($key, $val) = each ($_GET)) {
		$_GET[$key] = str_replace('">', "", strip_tags(remove_quotes($val)));
	}
}
?>