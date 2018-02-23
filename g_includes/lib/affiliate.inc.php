<?php 
// Abstract Affiliate class.
if ('affiliate.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

class AffiliateHandler {
	function AffiliateHandler(&$sql, $table_name) {
		$this->sql = &$sql;
		$this->table_name = $table_name;
	}
	
	// private
	var $sql= NULL;
	var $table_name=NULL;	
	
	// Get affiliates
	// When $category is specified, limits the affiliates to a specific category.
	// Order can be selected.
	// Returns array of affiliate objects.
	function GetAffiliates($category, $order) {
		$result = array();
		$query = 'SELECT * FROM ' . $this->table_name . '_affiliates';
		if (!is_null($category)) { $query .= ' WHERE category=' . $this->sql->safe_value($category); }
		$query .= ' ORDER BY ' . ((!is_null($order)) ? $order : 'name');
		$this->sql->query($query);
		if ($this->sql->query_ok()) {
			while($row = $this->sql->fetch_array()) {
				$affiliate = &new Affiliate();
				$this->FillAffiliate($affiliate, $row);
				array_push($result, $affiliate);
			}
		}
		return $result;
	}
	
	// Gets categories ordered by name.
	// Returns array of strings.
	function GetCategories() {
		$result = array();
		$this->sql->query('SELECT DISTINCT category FROM ' . $this->table_name . ' WHERE category IS NOT NULL ORDER BY category');
		if ($this->sql->query_ok()) {
			while($row = $this->sql->fetch_array()) {
				array_push($result, $row['category']);
			}
		}
		return $result;
	}
	
	/*
    *  Private Methods
    *  *************** 
	*  Private methods are not supposed to be used by code other than the class itself! So DON'T
	*/
	
	// ** PRIVATE METHOD **
	// Fills a affiliate object from a specified record.
	// !! Doesn't return anything, just fills in the affiliate specified.
	function FillAffiliate(&$affiliate, $record) {
		$affiliate->id = $record['id'];
		$affiliate->name = $record['name'];
		$affiliate->url = $record['url'];
		$affiliate->imageurl = $record['imageurl'];
		$affiliate->category = $record['category'];
		$affiliate->dateadd = $record['dateadd'];
	}

}
	
class Affiliate {
	var $id		= NULL;
	var $name	= NULL;
	var $url    = NULL;
	var $imageurl	= NULL;
	var $category	= NULL;
	var $dateadd	= NULL;
}
?>