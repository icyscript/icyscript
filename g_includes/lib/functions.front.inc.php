<?php 
// Functions only required by the Front-End UI
if ('functions.front.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
require_once('functions.inc.php');

function fillMember() {
	$member = &new Member();
	
	if (isset($_POST['name'])) { $member->name = $_POST['name']; }
	if (isset($_POST['mail'])) { $member->mail = $_POST['mail']; }
	if (isset($_POST['url'])) { $member->url = handle_site($_POST['url']); }
	if (isset($_POST['deleteurl']) && ($_POST['deleteurl'] == '1')) { $member->url = FALSE; }
	if (isset($_POST['showmail'])) { $member->showmail = $_POST['showmail']; }
	if (isset($_POST['custom'])) { $member->custom = $_POST['custom']; }
	if (isset($_POST['country'])) { $member->country = $_POST['country']; }	
	if (isset($_POST['rules'])) { $member->extra['rules'] = $_POST['rules']; }	
	if (isset($_POST['comment'])) { $member->extra['comment'] = $_POST['comment']; }
	if (isset($_POST['mid'])) { $member->id = $_POST['mid']; }

	return $member;
}

function isClean($value) {
	global $fanlisting;
	if (!is_null($value) && ($value != '') && (strlen($fanlisting->settings['spam_words']) > 1)) {
		foreach (explode(',',$fanlisting->settings['spam_words']) as $phrase) {
			if (stristr($value, trim($phrase)) !== false) {
				return false;
			}
		} 
	}
	return true;
}

// Outputs (X)HTML links of affiliates.
// Can be overwritten by a plugin.
// Perrow is number if affiliates per row (0 to disable)
// Order is the sorting order, by default name.
function ShowAffiliates($perrow=0, $order='name') {
	if (!DoPluginCalls('show_affiliates')) {
		global $fanlisting;
		$affiliates = $fanlisting->Affiliates(NULL, $order);
		if ($perrow == 0) { $perrow = count($affiliates); }
		elseif($perrow > count($affiliates)) { $perrow = count($affiliates); }
		$i = 0;
		foreach($affiliates as $affiliate) {
			$link = '';
			if (!is_null($affiliate->imageurl)) {
				$link .= '<img alt="' . htmlentities($affiliate->name, ENT_QUOTES, 'UTF-8') . '" src="' . htmlentities($affiliate->imageurl, ENT_QUOTES, 'UTF-8') . '" />';
			} else {
				$link = htmlentities($affiliate->name, ENT_QUOTES, 'UTF-8');
			}
			if (!is_null($affiliate->url)) {
				$link = '<li><a title="' . htmlentities($affiliate->name, ENT_QUOTES, 'UTF-8') . '" href="' . htmlentities($affiliate->url, ENT_QUOTES, 'UTF-8') . '">' . $link . '</a></li>';
			}
			echo $link;
			$i++;
			if ($i == $perrow) { echo '<br />'; }
		}
	}
}

function ShowNews($limit=NULL) {
	if (!DoPluginCalls('show_news')) {
		global $fanlisting;
		$newsitems = $fanlisting->News($limit);
		foreach ($newsitems as $newsitem) { 
			if (!DoPluginCalls('show_news_item', true, $newsitem)) { // Call for all newsitems
					ShowNewsTitle($newsitem);
					ShowNewsContent($newsitem);
			}
		}
	}
}

function ShowNewsTitle($newsitem) {
	if (!DoPluginCalls('show_news_title', true, $newsitem)) {
		global $fanlisting;
		echo '<strong>' . htmlentities($newsitem->title, ENT_QUOTES, 'UTF-8') . '</strong><br /><strong>' . date($fanlisting->settings['date_format'], $newsitem->dateadd) . '</strong>';
	}
}

function ShowNewsContent($newsitem) {
	if (!DoPluginCalls('show_news_content', true, $newsitem)) {
		global $fanlisting;
		echo '<p>' . nl2br(htmlentities($newsitem->content, ENT_QUOTES, 'UTF-8')) . '</p>';
	}
}

// Set post-init settings & statistics
if (isset($fanlisting) && is_object($fanlisting) && is_a($fanlisting, 'FrontFanlisting')) { // is_a() depreciated in PHP 5
	define('PHPFANLIST_STARTDATE', date($fanlisting->settings['date_format'], $fanlisting->settings['start_date']));
	define('PHPFANLIST_LASTCHECKED', date($fanlisting->settings['date_format'], $fanlisting->settings['last_checked'] + ($fanlisting->settings['timediff'] *3600)));
	define('PHPFANLIST_LASTUPDATE', date($fanlisting->settings['date_format'], $fanlisting->settings['last_update'] + ($fanlisting->settings['timediff'] *3600)));
	$stats = $fanlisting->GetStats();
	define('PHPFANLIST_MEMBERCOUNT', $stats['member_count']);
	define('PHPFANLIST_LASTMEMUPDATE', $stats['last_memberupdate']);
	define('PHPFANLIST_LASTMEMNEW', $stats['last_membernew']);
	define('PHPFANLIST_NUMJOIN', $stats['member_pending_count']);
	define('PHPFANLIST_NUMUPDATE', $stats['member_update_count']);
	define('PHPFANLIST_NUMDELETE', $stats['member_delete_count']);
	define('PHPFANLIST_LASTX', $stats['last_X']);
	define('PHPFANLIST_LASTNEWX', $stats['lastnew_X']);
}

require_once('plugins.inc.php'); // Calls the functions, make sure this it at the bottom, so they can use the constants.
DoPluginCalls('load_complete', false);
// NOTHING BEYOND THIS POINT
?>