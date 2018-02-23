<?php 
if ('mail.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

require_once('template.inc.php');

class Notify {
	function Notify($settings, $root=NULL) {
		$this->keyword_array = array();
		$this->keyword_array['LIST_TYPE'] = $settings['list_type_name'];
		$this->keyword_array['APPROVED']  = ($settings['approved']) ? 'approved' : '';
		$this->keyword_array['SITE_NAME'] = $settings['site_name'];	
		$this->keyword_array['SITE_URL']  = $settings['site_url'];	
		$this->keyword_array['OWNER_NAME'] = $settings['owner_name'];	
		$this->keyword_array['OWNER_MAIL'] = $settings['owner_mail'];

		$this->signature = $settings['mail_signature'];
		$this->headers = $settings['mail_headers'];
		
		$this->template_handler = &new Template();
		
		if (!is_null($root)) {
			$this->template_path = $root . $settings['global_includedir'] . 'mail_templates/';
		} elseif (isset($settings['doc_root']) && isset($settings['dir_name'])) {
			$this->template_path = $settings['doc_root'] . $settings['dir_name'] . '/' . $settings['global_includedir'] . 'mail_templates/';
		} else {
			$this->template_path = './';
		}
	}
	
	var $keyword_array;
	var $signature;
	var $headers;
	var $template_handler;
	var $template_path;
	
	function Mail($member, $mailtype, $overwrite_address=NULL) {
		if (is_null($member) || !is_a($member, 'Member')) return false; /* DEPRECIATED in PHP5 */
	
		$this->keyword_array['MEM_ID'] = is_null($member->id) ? 'N/A' : $member->id;
		$this->keyword_array['MEM_NAME'] = is_null($member->name) ? 'N/A' : $member->name;
		$this->keyword_array['MEM_URL'] = is_null($member->url) ? 'N/A' : $member->url;
		$this->keyword_array['MEM_COUNTRY'] = is_null($member->country) ? 'N/A' : $member->country;
		$this->keyword_array['MEM_MAIL'] = is_null($member->mail) ? 'N/A' : $member->mail;
		$this->keyword_array['MEM_CUSTOM'] = is_null($member->custom) ? 'N/A' : $member->custom;
		$this->keyword_array['MEM_TID'] = is_null($member->tempid) ? '0' : $member->tempid;
		if (isset($_POST['addmess'])) {
			$this->keyword_array['EXTRA_MESSAGE'] = str_replace("\\'", "'", $_POST['addmess']);
			$this->keyword_array['EXTRA_MESSAGE'] = str_replace("\\\"", "\"", $this->keyword_array['EXTRA_MESSAGE']);
		} else $this->keyword_array['EXTRA_MESSAGE'] = 'N/A';
		
		switch($mailtype) {
			case 'admin_join':
				$mailfile = 'adminjoin.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' - JOIN REQUEST';
				$mailto = $this->keyword_array['OWNER_MAIL'];
				break;
			case 'admin_update':
				$mailfile = 'adminupdate.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' - UPDATE REQUEST';
				$mailto = $this->keyword_array['OWNER_MAIL'];
				break;
			case 'join':
				$mailfile = 'join.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Joined';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'update':
				$mailfile = 'update.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Updated';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'join_approve':
				$mailfile = 'joinapprove.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Join - Approved';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'join_decline':
				$mailfile = 'joindecline.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Join - Declined';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'update_approve':
				$mailfile = 'updateapprove.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Memberinfo update - Approved';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'update_decline':
				$mailfile = 'updatedecline.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Memberinfo update - Declined';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'delete_approve':
				$mailfile = 'deleteapprove.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Member delete - Approved';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			case 'delete_decline':
				$mailfile = 'deletedecline.txt';
				$mailtitle = $this->keyword_array['SITE_NAME'] . ' Member delete - Declined';
				$mailto = $this->keyword_array['MEM_MAIL'];
				break;
			default:
				$mailfile = NULL;
				$mailto = NULL;
				break;
		}
		if (!is_null($overwrite_address)) { $mailto = $overwrite_address; }
		if (is_empty($mailfile) || is_empty($mailto) || !(strpos($mailto, '@') > 0)) return false;

		$path = realpath($this->template_path . $mailfile);
		if ($path === FALSE) return false;
	
		$this->template_handler->do_reset();
		$this->template_handler->load_templatefile($path);
		return mail($mailto, $mailtitle, $this->template_handler->do_replace($this->keyword_array) . $this->signature, $this->headers);
	}	
}
?>