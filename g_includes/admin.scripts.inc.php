<?php


$message = NULL; // This should be at the top!

require_once('lib/autoupdater.inc.php');
require_once('lib/fanlisting.admin.inc.php');
$fanlisting = &new AdminFanlisting($sql, $table_name);

require_once('lib/functions.admin.inc.php');

// Setting variables
$member		= NULL;
$affiliate	= NULL;
$newsitem	= NULL;
$filter     = NULL;
$notify		= false;
$_page		= (isset($_GET['page'])) ? strtolower($_GET['page']) : '';

// Check if fanlisting is first run (after install or update) and redirect to settings.
if ($fanlisting->settings['first_run'] && ($_page != 'settings')) {
	if ($fanlisting->UpdateSetting('first_run', '0') === true) {
		header('Location: admin.php?page=settings');
		exit;
	}
}

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : NULL;

if ((isset($_REQUEST['id']) && ctype_digit((string)$_REQUEST['id'])) || (isset($_REQUEST['tid']) && ctype_digit((string)$_REQUEST['tid']))) 
{
	$member = &new Member();
	if (isset($_REQUEST['id'])) { $member->id = $_REQUEST['id']; }
	if (isset($_REQUEST['tid'])) { $member->tempid = $_REQUEST['tid']; }
	if (isset($_POST['name'])) { $member->name = $_POST['name']; }
	if (isset($_POST['mail'])) { $member->mail = $_POST['mail']; }
	if (isset($_POST['url'])) { handle_site($member->url = $_POST['url']); }
	if (isset($_POST['showmail'])) { $member->showmail = $_POST['showmail']; }
	if (isset($_POST['custom'])) { $member->custom = $_POST['custom']; }
	if (isset($_POST['country'])) { $member->country = $_POST['country']; }	
}

if (isset($_REQUEST['affiliateid']) && ctype_digit((string)$_REQUEST['affiliateid'])) 
{
	$affiliate = &new Affiliate();
	if (isset($_REQUEST['affiliateid'])) { $affiliate->id = $_REQUEST['affiliateid']; }
	if (isset($_REQUEST['affiliatename'])) { $affiliate->name = $_REQUEST['affiliatename']; }
	if (isset($_REQUEST['affiliateurl'])) { $affiliate->url = $_REQUEST['affiliateurl']; }
	if (isset($_REQUEST['affiliateimageurl'])) { $affiliate->imageurl = $_REQUEST['affiliateimageurl']; }
	if (isset($_REQUEST['affiliatecategory'])) { $affiliate->category = $_REQUEST['affiliatecategory']; }
}

if (isset($_REQUEST['newsitemid']) && ctype_digit((string)$_REQUEST['newsitemid'])) 
{
	$newsitem = &new NewsItem();
	if (isset($_REQUEST['newsitemid'])) { $newsitem->id = $_REQUEST['newsitemid']; }
	if (isset($_REQUEST['newsitemtitle'])) { $newsitem->title = $_REQUEST['newsitemtitle']; }
	if (isset($_REQUEST['newsitemcontent'])) { $newsitem->content = $_REQUEST['newsitemcontent']; }
}

if (isset($_REQUEST['action'])) {
	$action = strtolower($_REQUEST['action']);
	$do = (isset($_REQUEST['do'])) ? strtolower($_REQUEST['do']) : '';
	$notify = (isset($_POST['notify']) && (strtolower($_POST['notify']) == 'yes')); 

	$_page = $action;
	switch($action) {
		case 'modify': // Admin editing / adding a member
			$_page = 'edit';
			if ($do == 'update') 
			{
				if (is_null(addMessage($fanlisting->CheckRequired($member)))) {
					addMessage($fanlisting->Modify($member));
				}
			} else if ($do == 'remove') {
				addMessage($fanlisting->Modify($member, true));
				$_page = 'list';
			}
			break;
		case 'updateaffiliate':
			$_page = 'editaffiliate';
			if ($do == 'update') 
			{
				addMessage(($fanlisting->UpdateAffiliate($affiliate)) ? 'Affiliate successfully updated.' : 'Could not update affiliate.');
			} else if ($do == 'remove') {
				addMessage(($fanlisting->UpdateAffiliate($affiliate, true)) ? 'Affiliate succesfully removed.' : 'Could not remove affiliate.');
				$_page = 'affiliates';
			}

			break;
		case 'updatenewsitem':
			$_page = 'editnewsitem';
			if ($do == 'update') 
			{
				addMessage(($fanlisting->UpdateNewsItem($newsitem)) ? 'News item successfully updated.' : 'Could not update news item.');
			} else if ($do == 'remove') {
				addMessage(($fanlisting->UpdateNewsItem($newsitem, true)) ? 'News item succesfully removed.' : 'Could not remove news item.');
				$_page = 'news';
			}

			break;
		case 'handlequeueitem':
			$tempmember = $fanlisting->GetMember($member->tempid, true);
			$do_notify = (isset($_POST['notify']) && ($_POST['notify'] == 'yes'));
			// No break means a nice fall-through
		case 'handlequeue':
			$_page = 'queue';
			if (!isset($do_notify)) { $do_notify = NULL; } // It would be set if it was handlequeueitem, NULL so setting decides
			$member_collection = array();
			
			if (!isset($tempmember)) { 
				if (isset($_POST['approve_joins']) || isset($_POST['decline_joins']) || isset($_POST['approve_updates']) || isset($_POST['decline_updates']) || isset($_POST['approve_deletes']) || isset($_POST['decline_deletes'])) {
					$do = NULL; // Is not set by a link
					$ids = array();
					reset ($_POST);
					while (list($key, $val) = each ($_POST)) {
						$pos = strpos($key, 'qitem_');
						if (($pos !== false) && ($pos == 0)) {
							array_push($ids, (int)$_POST[$key]);
						}
					}
					if (count($ids) > 0) {
						$where_in = 'tempid IN (' . implode(',', $ids) . ')'; // SQL where clause
						$list_type= NULL;
						$do = NULL; // Not needed but just so it's clear.
						
						if (isset($_POST['approve_joins'])) {
							$do = 'approve';
							$list_type = 'join';
						} elseif (isset($_POST['decline_joins'])) {
							$do = 'decline';
							$list_type = 'join';
						} elseif (isset($_POST['approve_updates'])) {
							$do = 'approve';
							$list_type = 'update';
						} elseif (isset($_POST['decline_updates'])) {
							$do = 'decline';
							$list_type = 'update';
						} elseif (isset($_POST['approve_deletes'])) {
							$do = 'approve';
							$list_type = 'delete';
						} elseif (isset($_POST['decline_deletes'])) {
							$do = 'decline';
							$list_type = 'delete';
						}
						$members = array();
						if (!is_null($list_type)) {
							$members = $fanlisting->MemberList($list_type, $where_in);
						}
						foreach($members as $member) { // Can be $member since the one posted is useless.
							array_push($member_collection, $member);
						}
						addMessage(count($ids) . ' members selected, ' . count($members) . ' found.');
						$members = NULL; // Free list
					}
				} else {
					$member = $fanlisting->GetMember($member->tempid, true);
					if (!is_null($member)) {
						array_push($member_collection, $member);
					}
				}
			} // Load from database, POST has no importance (except for multiple decline/approve).
			else { 
				$member->extra['action'] = $tempmember->extra['action']; // Is posted back, so use action from DB.
				$member->extra['member'] = $tempmember->extra['member']; // Actual member
				array_push($member_collection, $member);
			}
			$total = count($member_collection);
			$success = 0;
			$fail = 0;
			$unknown = 0;
			foreach($member_collection as $member_item) {
				$member_action = isset($member_item->extra['action']) ? $member_item->extra['action'] : -1; // Needs to be after the isset($tempmember) check!
				switch($do) {
					case 'decline':
						$do_approve = false;
						break;
					case 'approve':
						$do_approve = true;
						break;
					default:
						$do_approve = NULL;
						break;
				}
				$result = '';
				$result_list = '';
				if (!is_null($do_approve)) {
					if (!is_null($member_item->url)) {
						$member_item->url = handle_site($member_item->url);
					}
					switch($member_action) {
						case 0:
							$result = $fanlisting->Join($member_item, $do_approve, $do_notify);
							break;
						case 1:
							$result = $fanlisting->Update($member_item, $do_approve, $do_notify);
							break;
						case 2:
							$result = $fanlisting->Delete($member_item->tempid, $do_approve, $do_notify);
							break;
						default:
							$unknown++;
							break;
					}
					
					if ($result === true) {
						$success++;
					} elseif ($result === false) {
						$fail++;
					} else {
						$result_list .= "\n" . $result;
						$fail++;
					}
				} else { 
					$unknown++;
				}
			} // End Loop
			if ($total == 1) {
				if ($unknown > 0) {
					addMessage('Don\'t know what to do with this request.');
				} elseif ($fail > 0) {
					addMessage($result_list);
				} else {
					addMessage('Member succesfully ' . $do . 'd');
				}
			} else if ($total == 0){
				addMessage('No action required.');
			} else {
				addMessage($success . '/' . $total . ' members succesfully ' . $do . 'd');
				if ($unknown > 0) {	addMessage($unknown . ' unknown requests'); }
				if ($fail > 0) { addMessage($fail . ' requests failed'); addMessage($result_list); }
			}
			break;
		case 'mail':
			$mail_message = str_replace("\\'", "'", $_POST['message']);
			$mail_message = str_replace("\\\"", "\"", $mail_message);
			$mail_subject = str_replace("\\'", "'", $_POST['subject']);
			$mail_subject = str_replace("\\'", "'", $mail_subject);
			$mail_subject = preg_replace("/(\s|\r)/", ' ', $mail_subject);
			$mails_sent = 0;
			if ($member->id == 0) {
				$members = $fanlisting->MemberList('', 'mail IS NOT NULL AND mail <> \'\'');
				foreach($members as $mailmember) {
					if (mail($mailmember->mail, $mail_subject, $mail_message . $fanlisting->settings['mail_signature'], $fanlisting->settings['mail_headers'])) { $mails_sent++; }
				}
			} else {
				$member = $fanlisting->GetMember($member->id);
				if (mail($member->mail, $mail_subject, $mail_message . $fanlisting->settings['mail_signature'], $fanlisting->settings['mail_headers'])) { $mails_sent++; }
			}
			addMessage($mails_sent . ' email message(s) sent.');
			$_page = NULL;
			break;
		case 'settings':
			$fail = 0;
			$success = 0;
			$total = 0;
			reset ($_POST);
			while (list ($key, $val) = each ($_POST)) {
				$pos = strpos($key, 's_');
				if (($pos !== false) && ($pos == 0)) {
					$setting_key = substr($key, 2);
					$result = $fanlisting->UpdateSetting($setting_key, $_POST[$key]);
					if ($result === false) {
						$fail++;
					} elseif ($result === true) {
						$success++;
					}
					$total++;
				}
			}
			addMessage(($fail == 0) ? $success . ' of ' . $total . ' setting(s) were changed.' : ($fail) . ' setting(s) could not be changed!');
			if ($fanlisting->settings['approved'] && !$fanlisting->CheckDisallowedUrls($fanlisting->settings['site_url'])) {
				$fanlisting->UpdateSetting('approved', 0);
				addMessage('An approved ' . $fanlisting->settings['list_type_name'] . ' with that url is not allowed. (Approved setting changed)');
			}
			break;
		case 'search': 
			if ($_POST['search_name'] != '') {
				$filter = "name LIKE '%" . $_POST['search_name'] . "%'";
			}
			if ($_POST['search_mail'] != '') {
				if (isset($filter)) {
					$filter .= ' OR ';
				}
				$filter = "mail LIKE '%" . $_POST['search_mail'] . "%'";
			}
			if ($_POST['search_url'] != '') {
				if (isset($filter)) {
					$filter .= ' OR ';
				}
				$filter = "url LIKE '%" . $_POST['search_url'] . "%'";
			}
			if ($_POST['search_country'] != '') {
				if (isset($filter)) {
					$filter .= ' OR ';
				}
				$filter = "country LIKE '%" . $_POST['search_country'] . "%'";
			}
			$_page = 'list';
			break;
		case 'plugins':
			$plugin = (isset($_GET['plugin'])) ? $_GET['plugin'] : '';
			if ($do == 'add') {
				if (is_file(realpath($fanlisting->settings['global_includedir'] . 'plugins/') . '/' . $plugin . '.plugin.php')) {
					$plugins = explode("\n", $fanlisting->settings['plugins']);
					if (($plugin != '') && !in_array($plugin, $plugins)) {
						array_push($plugins, $plugin);
						sort($plugins);
						$fanlisting->UpdateSetting('plugins', trim(implode("\n", $plugins)));
					}
				} else {
					addMessage('The plugin specified does\'t exist.');
				}
			} elseif ($do == 'remove') {
				$plugins = explode("\n", $fanlisting->settings['plugins']);
				if (in_array($plugin, $plugins)) {
					$index = -1;
					for($i=0; $i<count($plugins); $i++) {
						if ($plugins[$i] == $plugin) {
							$index = $i;
							break;
						}
					}
					if ($index > -1) {
						unset($plugins[$index]);
					}
					$fanlisting->UpdateSetting('plugins', trim(implode("\n", $plugins)));
				}
			} else addMessage('Don\'t know what to do with this request.');
			break;
			case 'downloadmembers':
				header('Content-Type: text/plain; charset=utf-8');
				$members = $fanlisting->MemberList();
				foreach($members as $member) {
				//<strong>ID|name|mail|url|country|customfield|showmail|date_when_user_was_added</strong>
					echo $member->id . '|' . $member->name . '|' . $member->mail . '|' . $member->url . '|' .  $member->country . '|';
					echo $member->custom . '|' . ($member->showmail ? 'y' : 'n') . '|' . date('Y-m-d', $member->dateadd) . "\n";
				}
				exit;
			break;
	}
}
switch($_page) {
	case 'edit':
			if (is_null($member)) { $member = &new Member(); }
			$member = $fanlisting->GetMember($member->id);
		break;
	case 'queue':
			$join_members   = $fanlisting->MemberList('join');
			$update_members = $fanlisting->MemberList('update');
			$delete_members = $fanlisting->MemberList('delete');
		break;
	case 'queueitem':
			$member = $fanlisting->GetMember($member->tempid, true);

			// 0: hide, -1: display readonly, 1: display editable
			$display_items = array('name'=>0, 'mail'=>0, 'showmail'=>0, 'url'=>0, 'country'=>0, 'rules'=>0, 'custom'=>0);

			if (is_null($member)) {
				addMessage("\n" . 'Unknown member from queue selected.');
			} else {
				switch ($member->extra['action']) {
					case 2: // Delete
						$display_items = array('name'=>-1, 'mail'=>-1, 'showmail'=>0, 'url'=>0, 'country'=>0, 'rules'=>0, 'custom'=>0);
						$member->name = $member->extra['member']->name;
						$member->mail = $member->extra['member']->mail;
						break;
					case 1: // Update
						$display_items = array('name'=>1, 'mail'=>1, 'showmail'=>1, 'url'=>1, 'country'=>1, 'rules'=>0, 'custom'=>1);
						if (is_null($member->name)) { $member->name = $member->extra['member']->name; $display_items['name'] = -1; }
						if (is_null($member->mail)) { $member->mail = $member->extra['member']->mail; $display_items['mail'] = -1; }
						if (is_null($member->showmail)) { $member->showmail = $member->extra['member']->showmail; $display_items['showmail'] = -1; }
						if (is_null($member->url)) { $member->url = $member->extra['member']->url; $display_items['url'] = -1; }
						if (is_null($member->country)) { $member->country = $member->extra['member']->country; $display_items['country'] = -1; }
						if (is_null($member->custom)) { $member->custom = $member->extra['member']->custom; $display_items['custom'] = -1; }
						break;
					case 0: // Join
						$display_items = array('name'=>1, 'mail'=>1, 'showmail'=>1, 'url'=>1, 'country'=>1, 'rules'=>1, 'custom'=>1);
						break;
				}
			}
		break;
	case 'list':
			$members = $fanlisting->MemberList('', $filter, $orderby);
			$doubles = $fanlisting->GetDoublesIds();
			foreach ($members as $member) {
				if (in_array($member->id , $doubles)) {
					$member->extra['isdouble'] = true;
				}
			}
		break;
	case 'editaffiliate':
			if (is_null($affiliate)) { $affiliate = &new Affiliate(); }
			$affiliate = $fanlisting->GetAffiliate($affiliate->id);
		break;
	case 'affiliates':
			$affiliates = $fanlisting->Affiliates(NULL, $orderby);
		break;
	case 'editnewsitem':
			if (is_null($newsitem)) { 
			$newsitem = &new NewsItem(); }
			$newsitem = $fanlisting->GetNewsItem($newsitem->id);
		break;
	case 'news':
			$newsitems = $fanlisting->News(NULL, $orderby);
		break;
	case 'mail':
			if (is_null($member)) { $member = &new Member(); }
			$members = $fanlisting->MemberList('', NULL, 'name');
		break;
	case 'plugins':
			$plugins = array();
			$location = realpath($fanlisting->settings['global_includedir'] . 'plugins/') . '/';
			$dp= opendir($location);
			$plugins_found = array();
			while($entry = readdir($dp)) {
				if (is_file($location . $entry) && preg_match('/\.plugin\.php$/i', $entry)) {
					array_push($plugins_found, substr($entry, 0, strpos($entry, '.plugin.php'))); 
				}
			}
			$plugins_registered = explode("\n", $fanlisting->settings['plugins']);
			if ((count($plugins_registered) == 1) && (trim($plugins_registered[0]) == '')) { $plugins_registered = array(); }
			
			$all_plugins = array_unique(array_merge($plugins_found, $plugins_registered));
			sort($all_plugins);
			foreach($all_plugins as $plugin) {
				$name = substr($plugin, 0);
				if (in_array($plugin, $plugins_registered)) {
					if (in_array($plugin, $plugins_found)) { // Valid
						array_push($plugins, array('name'=>$name, 'status'=> 2));
					} else { // Broken
						array_push($plugins, array('name'=>$name, 'status'=> 0));
					}
				} else { // Found
					array_push($plugins, array('name'=>$name, 'status'=> 1));
				}
			}
			
			// cleanup
			unset($all_plugins);
			
		break;
	default:
		$stats = $fanlisting->GetStats();
		$queue = $fanlisting->GetList(true, NULL, 'name');
		break;
}

// Making sure those settings are correct
$x = pathinfo($_SERVER['PHP_SELF']);
if (($fanlisting->settings['dir_name'] != $x['dirname']) || ($fanlisting->settings['doc_root'] != $_SERVER['DOCUMENT_ROOT'])) {
	$fanlisting->UpdateSetting('dir_name', $x['dirname']);
	$fanlisting->UpdateSetting('doc_root', $_SERVER['DOCUMENT_ROOT']);
} 
?>