<?php 
// Fanlisting Class implementation for the Front-End
if ('fanlisting.front.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
// Functions \\
if (!function_exists('getIP')) {
	function getIP() {
		$IP = getenv('HTTP_X_FORWARDED_FOR');
		if ($IP == '') { return getenv('REMOTE_ADDR'); }
		else { return $IP; }
	}
}
	
require_once('fanlisting.inc.php'); // Make sure abstract class is there to extend.	
	
class FrontFanlisting extends Fanlisting {
	function FrontFanlisting(&$sql, $table_name) {
		parent::FanListing($sql, (string)$table_name); // Explicitely call parent constructor.

		switch($this->settings['list_type']) {
			case 0:
				$this->settings['approved_link'] = 'http://www.thefanlistings.org';
				break;
			case 2:
				$this->settings['approved_link'] = 'http://www.thenamelistings.org';
				break;
			case 3:
				$this->settings['approved_link'] = 'http://www.animefanlistings.org';
				break;
			case 5:
				$this->settings['approved_link'] = 'http://www.thenumberlistings.org';
				break;
			default:
				$this->settings['approved_link'] = '';
				break;
		}
	}
	
	// Adds a member for joining.
	// Returns TRUE.
	// Returns 1 on mail exists.
	// Returns FALSE on failure.
	function Join($member) {
		if (is_a($member, 'Member')) { /* DEPRECIATED in PHP 5 */
			if (!is_empty($member->mail) && !$this->settings['allow_doublemail'] && $this->MailExists($member->mail)) { return 1; }
			if (isset($member->extra['mid'])) { unset($member->extra['mid']); }
			if ($this->InsertMember($member)) {
				if (!is_null($this->notify_handler)) {
					if($this->settings['mail_on_join']) {
						$this->notify_handler->Mail($member, 'join');
					}
					if($this->settings['mail_admin']) {
						$this->notify_handler->Mail($member, 'admin_join');
					}
				}
				return true;
			} 
		}
		return false;
	}

	// Adds a member for update.
	// Returns TRUE.
	// Returns 1 on user mail exists.
	// Returns 2 on user doesn't exist.
	// Returns FALSE on failure.
	function Update($member) {
		if (is_a($member, 'Member')) { /* DEPRECIATED in PHP 5 */
			if ($this->GetMember($member->id) == NULL) { return 2; }
			if (!is_empty($member->mail) && !$this->settings['allow_doublemail'] && $this->MailExists($member->mail, $member->id)) { return 1; }
			
			$member->extra['action'] = 1;
			if ($this->InsertMember($member)) {
				if (!is_null($this->notify_handler)) {
					if($this->settings['mail_on_join']) {
						$member_original = $this->GetMember($member->id);
						if (!is_null($member->mail) && (strcasecmp(trim($member->mail), trim($member_original->mail)) != 0)) {
							$this->notify_handler->Mail($member_original, 'update');
						} elseif (is_null($member->mail)) {
							$member->mail = $member_original->mail;
						}
						$this->notify_handler->Mail($member, 'update');
					}
					
					if($this->settings['mail_admin']) {
						$this->notify_handler->Mail($member, 'admin_update');
					}
				}
				return true;
			} 
		} else return false;
	}
	
	// Adds a member for update.
	// Returns TRUE.
	// Returns 2 on user doesn't exist.
	// Returns FALSE on failure.
	function Delete($member) {
		if (is_a($member, 'Member')) { /* DEPRECIATED in PHP 5 */
			if ($this->GetMember($member->id) == NULL) { return 2; }
			$member->extra['action'] = 2;
			$member->name = NULL;
			$member->mail = NULL;
			$member->url = NULL;
			$member->showmail = NULL;
			$member->country = NULL;
			$member->custom = NULL;
			if (isset($member->extra['rules'])) { unset($member->extra['rules']); }
			if ($this->InsertMember($member)) {
				if (!is_null($this->notify_handler)) {
					if($this->settings['mail_admin']) {
						$this->notify_handler->Mail($member, 'admin_update');
					}
				}
				return true;
			} 
		} else return false;
	}

	function GetCountries($only_number=false, $order=NULL) {
		$countries = array();
		if ($only_number) {
			$this->sql->query('SELECT COUNT(DISTINCT country) AS num FROM ' . $this->table_name . ' WHERE country IS NOT NULL');
			if ($this->sql->query_ok()) {
				$row = $this->sql->fetch_array();
				$countries = $row['num'];
			} else $countries = 0;
		} else {
			$order_clause = '';
			if (!is_null($order)) {
				$order_clause = ' ORDER BY `' . $order . '`';
			}
			$this->sql->query('SELECT country, count(1) AS num FROM ' . $this->table_name . ' GROUP BY country' . $order_clause);
			if ($this->sql->query_ok()) {
				while($row = $this->sql->fetch_array()) {
					$country = array('name'=>$row['country'], 'members'=>$row['num']);
					array_push($countries, $country);
				}
			} else echo 'ERR';
		}
			
		return $countries;
	}

	// Checks if there is already an update scheduled for this Id
	// Returns True for yes and False for no or invalid id.
	function CheckUpdateScheduled($id) {
		if ((!is_null($id)) && ctype_digit((string)$id) && ($id > 0)) {
			$members = $this->GetList(true, '(action = 1 OR action = 2) AND mid = ' .$id);
			return (count($members) > 0);
		} else return false;
	}
	
	/*
    *  Private Methods
    *  *************** 
	*  Private methods are not supposed to be used by code other than the class itself! So DON'T
	*/
	function InsertMember(&$member) {
		if (isset($member) && !is_null($member) && is_a($member, 'Member')) { /* DEPRECIATED IN PHP 5 */
			$this->Clean4DB($member);
			if (is_null($member->showmail)) { $member->showmail = 0; }
			if (!isset($member->extra['action'])) { $member->extra['action'] = 0; }
			if (!isset($member->extra['rules'])) { $member->extra['rules'] = NULL; }
			if (!isset($member->extra['comment'])) { $member->extra['comment'] = NULL; }
			$member->extra['IP'] = getIP();
			
			$columns = 'IP, action, showmail, dateadd';
			$values  = $this->sql->safe_value($member->extra['IP'], true) . ', ' . $member->extra['action'] . ', ' . $this->sql->safe_value($member->showmail) . ', NOW()';
			
			if (!is_null($member->id)) { $columns .= ', mid'; $values .= ', ' . $this->sql->safe_value($member->id, true); }
			if (!is_null($member->name)) { $columns .= ', name'; $values .= ', ' . $this->sql->safe_value($member->name, true); }
			if (!is_null($member->country)) { $columns .= ', country'; $values .= ', ' . $this->sql->safe_value($member->country, true); }
			if (!is_null($member->mail)) { $columns .= ', mail'; $values .= ', ' . $this->sql->safe_value($member->mail, true); }
			if (!is_null($member->url)) { $columns .= ', url'; $values .= ', ' . (($member->url === false) ? '0' : $this->sql->safe_value($member->url, true)); }
			if (!is_null($member->custom)) { $columns .= ', custom'; $values .= ', ' . $this->sql->safe_value($member->custom, true); }
			if (!is_null($member->extra['comment'])) { 
				// SUBSTR can give problems with UTF-8 and weird characters!!
				$value = (strlen($member->extra['comment']) > $this->settings['max_comment']) ? substr($member->extra['comment'], 0, $this->settings['max_comment']) : $member->extra['comment'];
				$columns .= ', comment';
				$values .= ', ' . $this->sql->safe_value($value, true);
			}
			if (!is_null($member->extra['rules'])) { $columns .= ', rules'; $values .= ', ' . $this->sql->safe_value($member->extra['rules'], true); }
			
			$query  = 'INSERT INTO ' . $this->table_name . '_temp (' . $columns . ') VALUES (' . $values . ')';
			$result = $this->sql->query($query);
			if ($result) {
				$member->tempid = $this->sql->insert_id();
				return true;
			} else return false;
		}
		return false;
	}
}
?>