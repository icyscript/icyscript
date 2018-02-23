<?php 
if ('fanlisting.admin.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
require_once('fanlisting.inc.php'); // Make sure abstract class is there to extend.	
require_once('affiliate.admin.inc.php'); // Make sure abstract class is there to extend.	
require_once('news.admin.inc.php'); // Make sure abstract class is there to extend.	
	
class AdminFanlisting extends Fanlisting {
	function AdminFanlisting(&$sql, $table_name) {
		parent::FanListing($sql, (string)$table_name); // Explicitely call parent constructor.
		
		// Overwriting handlers with admin versions.
		if (class_exists('AdminAffiliateHandler')) {
			$this->affiliate_handler = &new AdminAffiliateHandler($this->sql, $this->table_name);
		}
		if (class_exists('AdminNewsHandler')) {
			$this->news_handler = &new AdminNewsHandler($this->sql, $this->table_name);
		}

		switch ($this->settings['list_type']) {
			case '0': // Fanlisting
			case '2': // Namelisting
			case '5': // Numberlisting
			case '3': // Anime Fanlisting 
				if ($this->settings['approved'] && !$this->CheckDisallowedUrls($this->settings['site_url'])) {
					$this->UpdateSetting('approved', 0);
				}
				if ($this->settings['approved']) {
					if ($this->settings['ask_country'] != '2') { $this->UpdateSetting('ask_country', 2); } // Country is required for approved fanlistings.
					if ($this->settings['ask_url'] == 2) { $this->UpdateSetting('ask_url', 1); } // Website can't be a required field.
					if ($this->settings['ask_custom'] == 2) { $this->UpdateSetting('ask_custom', 1); } // Custom field can't be a required field.
					if ($this->settings['ask_rules'] == 2) { $this->UpdateSetting('ask_rules', 0); } // Rules can't be a required field.
				}
				break;
			case '4': // General listing
			case '1': // Clique
				if ($this->settings['approved']) { $this->UpdateSetting('approved', 0); } // can't be approved?
				break;
			default:
				break;
		}
		if (($this->settings['ask_url'] == 0) && ($this->settings['show_url'])) {
			$this->UpdateSetting('show_url', 0);  // No use in displaying when you don't ask it.
		}
		if (($this->settings['ask_custom'] == 0) && ($this->settings['show_custom'])) {
			$this->UpdateSetting('show_custom', 0);  // No use in displaying when you don't ask it.
		}
	}
	
	// $member->Id is used to determine if it's an edit or an add (add=> id=0)
	// Returns string with message.
	function Modify($member, $remove=false) {
		if ($remove) {
			if ($this->RemoveMember($member->id)) {
				$this->LastUpdate();
				return 'Member successfully removed from the list.';
			} else return 'Removing member from the list failed!';
		} else {
			if ($member->id > 0) { // Existing member => Edit
				if (!$this->settings['allow_doublemail']) {
					if ($this->mailExists($member->mail, $member->id)) {
						return 'A member with this email address already exists. You can\'t use this email address.';
					}
				}
				if ($this->UpdateMember($member)) {
					$this->LastUpdate();
					return 'Member modified successfully.';
				} else return 'Member modify failed.';
			} else { // New member, admin add
				if ($this->UpdateMember($member)) {
					$this->LastUpdate();
					return 'Member added successfully.';
				} else return'Member adding failed.';
			}
		}
	}
	
	// $member can be either an ID or an actual member object
	// In case of an object that object is used.
	// In case of an ID, the temporary user is fetched and used as the object
	// Returns true if successfull, false if failed and message if error.
	function Join($member, $approve=true, $notify=NULL) {
		if (!is_object($member)) {
			$member = $this->GetMember($member, true);
		}
		if (is_null($member)) return 'Not a valid member.';
		if ($approve) {
			if (!$this->settings['allow_doublemail']) {
				if ($this->mailExists($member->mail)) {
					return 'A member with this email address already exists. Can\'t let this member to join.';
				}
			}
			if ($this->settings['show_mail'] == 1) {
				$member->showmail = '1';
			} elseif ($this->settings['show_mail'] == 2) {
				$member->showmail = '0';
			}
			if ($this->UpdateMember($member)) {
				if (!is_null($this->notify_handler) && (($notify===true) || (($notify == NULL) && $this->settings['mail_approve']))) {
					$this->notify_handler->Mail($member, 'join_approve');
				}
				if ($this->RemoveMember($member->tempid, true)) {
					$this->LastUpdate();
					return true;
				} else { return $member->name . ' approved, but an error occured while deleting him/her from the temporary database.'; }
			} else { return false; }
		} else {
			if ($this->RemoveMember($member->tempid, true)) {
				if (!is_null($this->notify_handler) && (($notify===true) || (($notify == NULL) && $this->settings['mail_approve']))) {
					$this->notify_handler->Mail($member, 'join_decline');
				}
				return true;
			} else return false;
		}
	}

	// $member can be either an ID or an actual member object
	// In case of an object that object is used.
	// In case of an ID, the temporary user is fetched and used as the object
	// notify=true: forced mail, NULL: mail if setting says mail, false: no mail
	// Returns true for succes, false for failure and string with message for error.	
	function Update($member, $approve=true, $notify=NULL) {
		if (!is_object($member)) {
			$member = $this->GetMember($member, true);
		}
		if (is_null($member)) return 'Not a valid member.';
		if ($approve) {
			if (!isset($member->extra['member'])) { //If Id is given in function call, the member is loaded as well.
				$member->extra['member'] = $this->GetMember($member->id);
			}
			if (is_null($member->extra['member'])) return 'Can\'t find original member to update. Member not updated';
			if (!$this->settings['allow_doublemail'] && !is_null($member->mail)) {
				if ($this->MailExists($member->mail, $member->extra['member']->id)) { 
					return 'A member with this email address already exists. Can\'t let this member update his/her info.';
				}
			}

			if (is_null($member->name)) { $member->name = $member->extra['member']->name; }
			if (is_null($member->country)) { $member->country = $member->extra['member']->country; }
			if (is_null($member->mail)) { $member->mail = $member->extra['member']->mail; }
			if (is_null($member->url)) { $member->url = $member->extra['member']->url; }
			if (is_null($member->custom)) { $member->custom = $member->extra['member']->custom; }
			if (is_null($member->showmail)) { $member->showmail = $member->extra['member']->showmail; }
			if (is_null($member->dateadd)) { $member->dateadd = $member->extra['member']->dateadd; }

			$response = $this->CheckRequired($member);
			if (!is_null($response)) { return $response; }
			if ($this->UpdateMember($member)) {
				if (!is_null($this->notify_handler) && (($notify===true) || (($notify == NULL) && $this->settings['mail_approve']))) {
					$this->notify_handler->Mail($member, 'update_approve');
					if ($member->extra['member']->mail != $member->mail) { // If mail addresses are not the same, send one to the old mail too.
						$this->notify_handler->Mail($member, 'update_approve', $member->extra['member']->mail);
					}
				}
				if ($this->RemoveMember($member->tempid, true)) {
					$this->LastUpdate();
					return true;
				} else return 'Update for ' . $member->name . ' approved, but an error occured while deleting him/her from the temporary database.';
			} else return false;
		} else {
			$member = $this->GetMember($member->tempid, true);
			if ($this->RemoveMember($member->tempid, true)) {
				if (!is_null($this->notify_handler) && (($notify===true) || (($notify == NULL) && $this->settings['mail_approve']))) {
					$this->notify_handler->Mail($member->extra['member'], 'update_decline');
				}
				$this->LastUpdate();
				return true;
			} else return false;
		}
	}
	
	// Id of the member to delete
	// Returns true for success, false for failure and string with message for error.
	function Delete($member_id, $approve=true, $notify=NULL) {
		$member = $this->GetMember($member_id, true);
		if (is_null($member) || (!isset($member->extra['member']))) return 'Not a valid member';
		if ($approve) {
			if ($this->RemoveMember($member->extra['member']->id)) {
				if (!is_null($this->notify_handler) && (($notify===true) || (($notify == NULL) && $this->settings['mail_approve']))) {
					$this->notify_handler->Mail($member, 'delete_approve');
				}
				if ($this->RemoveMember($member->tempid, true)) {
					$this->LastUpdate();
					return true;
				} else return 'Member ' . $member->extra['member']->name . ' deleted, but an error occured while deleting him/her from the temporary database.';
			} else return false;
		} else {
			if ($this->RemoveMember($member->tempid, true)) {
				if (!is_null($this->notify_handler) && (($notify===true) || (($notify == NULL) && $this->settings['mail_approve']))) {
					$this->notify_handler->Mail($member->extra['member'], 'delete_decline');
				}
				$this->LastUpdate();
				return true;
			} else return false;
		}
	}
	
	function GetDoublesIds() {
		$doubles = array();
		$this->sql->query('SELECT mail FROM ' . $this->table_name . ' GROUP BY mail HAVING count(1) > 1');
		if ($this->sql->num_rows() > 0) {
			$double_mails = array();
			while($row = $this->sql->fetch_array()) {
				array_push($double_mails, $row['mail']);
			}
			$this->sql->query('SELECT id FROM ' . $this->table_name . ' WHERE mail IN (\'' . implode("', '", $double_mails) . '\')');
			if ($this->sql->num_rows() > 0) {
				while($row = $this->sql->fetch_array()) {
					array_push($doubles, $row['id']);
				}
			}
		}
		return $doubles;
	}
	
	// Updates the given setting with the given value.
	// If the setting doesn't exist returns FALSE.
	// If the new value is the same as the old, returns TRUE.
	// Returns TRUE on success, FALSE on failure, 0 on no edit needed.
	function UpdateSetting($setting, $value) {
		if (array_key_exists($setting, $this->settings)) { 
			if ($this->settings[$setting] != $value) { 
				$this->sql->query('UPDATE ' . $this->table_name . '_settings SET value = ' . $this->sql->safe_value($value, true, true) . ' WHERE setting = ' . $this->sql->safe_value($setting));
				if ($this->sql->query_ok()) {
					$this->settings[$setting] = $value;
					return true;
				} else { return false; }
			} else return 0;
		} else return false;
	}
		
	// Gets the specified affiliate object.
	function GetAffiliate($affiliate_id) {
		if (!is_null($this->affiliate_handler)) {
			return $this->affiliate_handler->GetAffiliate($affiliate_id);
		} else return NULL;
	}
	
	// Update the affiliate
	function UpdateAffiliate(&$affiliate, $delete=false) {
		if (!is_null($this->affiliate_handler)) {
			return $this->affiliate_handler->UpdateAffiliate($affiliate, $delete);
		} else return false;
	}

	// Gets the specified newsitem object.
	function GetNewsItem($item_id) {
		if (!is_null($this->news_handler)) {
			return $this->news_handler->GetNewsItem($item_id);
		} else return NULL;
	}

	// Update the affiliate
	function UpdateNewsItem(&$newsitem, $delete=false) {
		if (!is_null($this->news_handler)) {
			return $this->news_handler->UpdateNewsItem($newsitem, $delete);
		} else return false;
	}
		
	/*
    *  Private Methods
    *  *************** 
	*  Private methods are not supposed to be used by code other than the class itself! So DON'T
	*/
	
	// ** PRIVATE METHOD **
	// Updates the member specified.
	// If memberId = 0 (new member), member is added.
	// Returns TRUE on success, FALSE on failure.
	function UpdateMember(&$member) 
	{
		if (!is_null($member) && is_a($member, 'Member')) { // DEPRECIATED in PHP5
			$this->Clean4DB($member);
			if (isset($member->extra['member']) && !is_null($member->extra['member']->id) && $member->extra['member']->id > 0) {
				$member->id = $member->extra['member']->id;
			}
			if ((!is_null($member->id)) && ($member->id > 0)) { // Member exists -> update
				$query = 'UPDATE ' . $this->table_name . ' SET dateofadd = dateofadd, lastupdate = ';
				$query .= (isset($member->extra['action'])) ? 'NOW()' : 'lastupdate'; // If extra['action'] is set, the update was requested by the member, otherwise done by admin
				$query .= ', name = ' . $this->sql->safe_value($member->name, true) . ', country = ' . $this->sql->safe_value($member->country, true) . ', mail = ' . $this->sql->safe_value($member->mail, true) . ', url = ' . $this->sql->safe_value($member->url, true) . ', custom = ' . $this->sql->safe_value($member->custom, true) . ', showmail = ';
				$query .= (is_null($member->showmail)) ? 'showmail' : $this->sql->safe_value($member->showmail);
				$query .= ' WHERE id = ' . $member->id;
				if ($this->sql->query($query)) {
					return true;
				} else return false;
			}
			else {
				if ($this->sql->query('INSERT INTO ' . $this->table_name . ' (name, country, mail, url, custom, dateofadd, lastupdate, showmail) VALUES (' . $this->sql->safe_value($member->name, true) .  ', ' . $this->sql->safe_value($member->country, true) .  ', ' . $this->sql->safe_value($member->mail, true) .  ', ' . $this->sql->safe_value($member->url, true) .  ', ' . $this->sql->safe_value($member->custom, true) .  ', NOW(), NOW(), ' . $this->sql->safe_value($member->showmail) . ')')) {
					$member->id = $this->sql->insert_id();
					return true;
				} else return false;
			}
		} else return false;
		
	}	
	
	// ** PRIVATE METHOD **
	// Removes the member from the database, being temp or actual member.
	// Returns TRUE when successful, FALSE on failure.
	function RemoveMember($memberid, $is_temp=false) {
		if (!is_null($memberid) && ctype_digit((string)$memberid) && ($memberid > 0)) {
			if ($is_temp) { $query = 'DELETE FROM ' . $this->table_name . '_temp WHERE tempid = ' . $memberid . ' LIMIT 1'; }
			else { $query = 'DELETE FROM ' . $this->table_name . ' WHERE id = ' . $memberid . ' LIMIT 1'; }
			if ($this->sql->query($query)) {
				return true;
			} else return false;
		} else return false;
	}
	
	// Check if the url is valid according to approved rules
	// Returns true if ok, false if not.
	function CheckDisallowedUrls($url) {
		if (!is_null($url) && !is_empty($url) && $this->settings['approved']) {
			$parsed_url = parse_url($url);
			if (isset($parsed_url['host'])) {
				return !(preg_match('/\.web1000\.com$/i', $parsed_url['host']) || preg_match('/\.kit\.net$/i', $parsed_url['host']) || preg_match('/\.cjb\.net$/i', $parsed_url['host']) || preg_match('/\.tk$/i', $parsed_url['host']));
			} else return false;
		} else return true;
	}

	// ** PRIVATE METHOD **
	// Updates the last update date of the fanlisting.
	function LastUpdate() {
		$this->sql->query('UPDATE ' . $this->table_name . '_settings SET value = UNIX_TIMESTAMP(NOW()) WHERE setting = \'last_update\'');
	}
	
	// ** PRIVATE METHOD **
	// Gets version of current object. This is not the phpFanList version.
	// Can be used to check capabilities.
	function GetVersion() {
		return parent::GetVersion();
	}
}
?>