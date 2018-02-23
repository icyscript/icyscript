<?php 
// Functions used by Front-End and Admin
// Should only be called from functions.admin.inc.php and functions.front.inc.php
if ('functions.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
$mail_pattern = '/^[\w0-9_.-]+@([\w0-9._-]*)?[\w0-9_]+(\.[a-z]{2,4}){1,2}$/i';
$web_pattern  = '/^(?:http:\/\/)?((?:[\w0-9-]+\.)*(?:[\w0-9-]{3,})(?:\.[a-z]{2,4}){1,2}){1}((?:\/)(?:[\w0-9~.?][\w0-9.\/?=-]*)*)?$/i';

// Version 1.0
function scramble_email($inaddr) {
	$s = str_replace('@', '&#37;40', $inaddr);
	return str_replace('.', '&#46;', $s);
}

if ($fanlisting->settings['advanced_mailcheck']) {
	include_once('undisposable.inc.php');

	function validate_mail($email) {
		return undorg_isValidEmail($email);
	}
} else {
	function validate_mail($email) {
		global $mail_pattern;
		return preg_match($mail_pattern, $email);
	}
}

function validate_site($site) {
	global $web_pattern;
	return preg_match($web_pattern, $site);
}

function handle_site($site) {
	global $web_pattern;
	$s = preg_replace($web_pattern, 'http://\\1\\2', $site);
	$s = strtolower($s); // Danger when locale doesn't support UTF-8
	return $s;
}

function defaultValue($fieldname, $value=NULL, $default_select=false) {
	if (isset($_POST[$fieldname])) {
		if (is_null($value)) {
			echo ' value="' . htmlentities(trim($_POST[$fieldname]), ENT_QUOTES, 'UTF-8') . '"';
			return true;
		}
		elseif ($value == 'textarea') {
			echo htmlentities(trim($_POST[$fieldname]), ENT_QUOTES, 'UTF-8');
			return true;
		}
		elseif ($value == 'checked') { echo ' checked="checked"'; return true; }
	} elseif (($value == 'checked') && ($_SERVER['REQUEST_METHOD'] != 'POST') && $default_select) { echo ' checked="checked"'; return true; }
	return false;
}
	
// Determines whether to show the mail or not 
function ShowMail($member_showmail, $fanlisting_setting, $admin_can_override = false) {
	switch ($fanlisting_setting) {
		case 1: // Yes
			return true;
		case 2: // No
			return false;
		case 3: // Member decides
			return ($member_showmail == true);
		case 4: // Yes, admin can override.
			if ($admin_can_override) { return true; }
			else { return ($member_showmail == true); }
		default:
			return false;
	}
}

// Set post-init settings (needs to be after functions for fanlisting to be available)
if (isset($fanlisting) && is_object($fanlisting) && is_a($fanlisting, 'Fanlisting')) { // is_a() depreciated in PHP 5
	define('PHPFANLIST_VERSION', substr($fanlisting->settings['version'], 0, 4));
}
?>