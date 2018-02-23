<?php 
// Abstract News class.
if ('news.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

class NewsHandler {
	function NewsHandler(&$sql, $table_name) {
		$this->sql = &$sql;
		$this->table_name = $table_name;
	}
	
	// private
	var $sql= NULL;
	var $table_name=NULL;	
	
	// Get News
	// When $limit is specified only that number of last news items is returned
	// Returns array of newsitem objects.
	function GetNews($limit=NULL, $order=NULL) {
		$result = array();
		$query = 'SELECT *, UNIX_TIMESTAMP(dateadd) AS dateadded FROM ' . $this->table_name . '_news';
		$query .= ' ORDER BY ' . ((is_null($order)) ? 'dateadd DESC' : $order);
		if (!is_null($limit)) { $query .= ' LIMIT ' . $limit; }
		$this->sql->query($query);
		if ($this->sql->query_ok()) {
			while($row = $this->sql->fetch_array()) {
				$newsitem = &new NewsItem();
				$this->FillNewsItem($newsitem, $row);
				array_push($result, $newsitem);
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
	// Fills a newsitem object from a specified record.
	// !! Doesn't return anything, just fills in the newsitem object specified.
	function FillNewsItem(&$newsitem, $record) {
		$newsitem->id = $record['id'];
		$newsitem->title	= $record['title'];
		$newsitem->content	= $record['content'];
		$newsitem->dateadd	= $record['dateadded'];
	}
}
	
class NewsItem {
	var $id		= NULL;
	var $title	= NULL;
	var $content    = NULL;
	var $dateadd	= NULL;
}

?>