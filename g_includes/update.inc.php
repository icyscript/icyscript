<?php 
if ('update.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

// Setting values
$update_success	= false;
$message2		= '';

if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['doupdate'])) {
	$member = fillMember();
	
	if (isClean($member->name) && isClean($member->url) && isClean($member->mail) && (!isset($member->extra['comment']) || isClean($member->extra['comment']))) {
		if (is_empty($member->id)) { $message2 .= 'You need to specify the number of the member you want to update.'; }
		if (!is_empty($member->url) && !validate_site($member->url)) { $message2 .= "\n" . 'The website you supplied (' . $member->url . ') is not valid.'; }
		// we changed this line to avoid an error // if (!is_empty($member->mail) && !validate_mail($member->mail)) { $message2 .= "\n" . 'The email address you supplied (' . $member->mail . ') is not valid.'; } //
		if (isset($member->extra['comment']) && (strlen($member->extra['comment']) > $fanlisting->settings['max_comment'])) { $message2 .= "\n" . 'The comment you provided is too long.'; }
		if ($fanlisting->CheckRequired('url') && is_empty(url)) { $message2 .= "\n" . 'The URL is a required field and can\'t be removed.'; }
		if (!$fanlisting->settings['allow_pluralupdate'] && $fanlisting->CheckUpdateScheduled($member->id)) { $message2 .= "\n" . 'There is already an update scheduled for this member.'; }
	
		if ($message2 == '') {
			if (isset($_POST['delme']) && ($_POST['delme'] == '1')) {
				$result = $fanlisting->Delete($member);
			} else {
				$result = $fanlisting->Update($member);
			}
			if ($result === true) {
				$message2 = 'Your request has been received and your data will be updated, after it has been verified.';
				$update_success = true;
			} elseif($result == 1) {
				$message2 = 'Sorry, but this email address is already listed in the database. This means that someone (possibly you) is already listed as a member.';
			} elseif($result == 2) {
				$message2 = 'There is no member with the member number you specified.';
			} else {
				$message2 = 'An unknown error occured. Please try again. If the problem keeps occuring, please contact the fanlist owner ('. $fanlisting->settings['owner_name'] . ').';
			}
		}
	} else $message2 .= 'No spamming!' . "\n";
	$message2 = nl2br(htmlentities(trim($message2), ENT_QUOTES, 'UTF-8'));
}
?>