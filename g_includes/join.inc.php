<?php 
if ('join.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

function is_required($display, $field) {
	global $fanlisting;
	if ($fanlisting->CheckRequired($field)) {
		echo $display;
	}
}

// Setting values
$join_success	= false;
$message		= '';

if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['dojoin'])) {
	$member = fillMember();
	$result = $fanlisting->CheckRequired($member);
	if (is_null($result))
	{
		if (isClean($member->name) && isClean($member->url) && isClean($member->mail) && (!isset($member->extra['comment']) || isClean($member->extra['comment']))) {
			if (!is_empty($member->url) && !validate_site($member->url)) { $message .= 'The website you supplied (' . $member->url . ') is not valid.'; }
			// we changed this line to avoid an error // if (!is_empty($member->mail) && !validate_mail($member->mail)) { $message .= "\n" . 'The email address you supplied (' . $member->mail . ') is not valid.'; } //
			if (isset($member->extra['comment']) && (strlen($member->extra['comment']) > $fanlisting->settings['max_comment'])) { $message .= "\n" . 'The comment you provided is too long.'; }
			if ($fanlisting->settings['ask_custom'] && !is_empty($fanlisting->settings['custom_field_format']) && 
				!is_empty($member->custom) && !preg_match($fanlisting->settings['custom_field_format'], $member->custom)) { // Needs a check.
					$message .= "\n" . $fanlisting->settings['custom_field_name'] . ' you entered is not in a valid format.';
			}  
	
			if ($message == '') {
				$result = $fanlisting->Join($member);
				if ($result === true) {
					$message = 'Thank you ' . $member->name . ' for joining ' . $fanlisting->settings['site_name'] . '. You will be added to the members list with the next update.';
					$join_success = true;
				} elseif ($result == 1) {
					$message .= 'Sorry, but this email address is already listed in the database. This means that someone (possibly you) is already listed as a member.';
				} else { 
					$message = 'An unknown error occured. Please try again. If the problem keeps occuring, please contact the fanlist owner ('. $fanlisting->settings['owner_name'] . ').';
				}
			}
		} else $message .= 'No spamming!' . "\n";
	} else {
		$message = 'It appears that you forgot something. All fields with * are required.' . "\n";
		$message .= $result;
	}
$message = nl2br(htmlentities(trim($message), ENT_QUOTES, 'UTF-8'));
}
?>