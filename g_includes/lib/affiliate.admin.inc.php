<?php 
if ('affiliate.admin.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
require_once('affiliate.inc.php'); // Make sure abstract class is there to extend.	

class AdminAffiliateHandler extends AffiliateHandler {
	function AdminAffiliateHandler(&$sql, $table_name) {
		parent::AffiliateHandler($sql, $table_name); // Explicitely call parent constructor.
	}
	
	function GetAffiliate($affiliate_id) {
		if ((!is_null($affiliate_id)) && ctype_digit((string)$affiliate_id) && ($affiliate_id > 0)) {
			$affiliate = &new Affiliate();
			$this->sql->query('SELECT * FROM ' . $this->table_name . '_affiliates WHERE id = ' . $affiliate_id);
			if ($this->sql->query_ok()) {
				$row = $this->sql->fetch_array();
				$this->FillAffiliate($affiliate, $row);
			}
			return $affiliate;
		} else return NULL;
	}
	
	function UpdateAffiliate(&$affiliate, $delete) {
		if (!is_null($affiliate) && is_a($affiliate, 'Affiliate')) { // DEPRECIATED in PHP5
			$this->Clean4DB($affiliate);
			if ($delete && !is_null($affiliate->id) && ($affiliate->id > 0)) {
				if ($this->sql->query('DELETE FROM ' . $this->table_name . '_affiliates WHERE id=' . $affiliate->id)) { 
					return true; 
				} else return false;
			}
			elseif (!is_null($affiliate->id) && ($affiliate->id > 0)) { // Member exists -> update
				$query = 'UPDATE ' . $this->table_name . '_affiliates SET name=' . $this->sql->safe_value($affiliate->name, true) . ', url=' . $this->sql->safe_value($affiliate->url, true) . ', imageurl=' . $this->sql->safe_value($affiliate->imageurl, true) . ', category=' . $this->sql->safe_value($affiliate->category, true);
				$query .= ' WHERE id = ' . $affiliate->id;
				if ($this->sql->query($query)) {
					return true;
				} else return false;
			}
			elseif (!$delete) {
				if ($this->sql->query('INSERT INTO ' . $this->table_name . '_affiliates (name, url, imageurl, category, dateadd) VALUES (' . $this->sql->safe_value($affiliate->name, true) .  ', ' . $this->sql->safe_value($affiliate->url, true) .  ', ' . $this->sql->safe_value($affiliate->imageurl, true) .  ', ' . $this->sql->safe_value($affiliate->category, true) .  ', NOW())')) {
					$affiliate->id = $this->sql->insert_id();
					return true;
				} else return false;
			}
			else return false;
		} else return false;
	}
	
	// ** PRIVATE METHOD **
	// Cleans the affiliate-info a bit, before it goes into the database.
	// Strips tags & trims
	function Clean4DB(&$affiliate) {	
		if (!is_null($affiliate->name)) {$affiliate->name = strip_tags(trim($affiliate->name)); }
		if (!is_null($affiliate->url)) {$affiliate->url = strip_tags(trim($affiliate->url)); }
		if (!is_null($affiliate->imageurl)) {$affiliate->imageurl = strip_tags(trim($affiliate->imageurl)); }
		if (!is_null($affiliate->category)) {$affiliate->category = strip_tags(trim($affiliate->category)); }
	}

}
?>