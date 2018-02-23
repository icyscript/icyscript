<?php 
if ('news.admin.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
require_once('news.inc.php'); // Make sure abstract class is there to extend.	
	
class AdminNewsHandler extends NewsHandler {
	function AdminNewsHandler(&$sql, $table_name) {
		parent::NewsHandler($sql, (string)$table_name); // Explicitely call parent constructor.
	}
	
	// Gets a newsitem object specified by the ID.
	// Returns NULL on error (i.e. invalid id)
	function GetNewsItem($item_id) {
		if ((!is_null($item_id)) && ctype_digit((string)$item_id) && ($item_id > 0)) {
			$newsitem = &new NewsItem();
			$this->sql->query('SELECT *, UNIX_TIMESTAMP(dateadd) AS dateadded FROM ' . $this->table_name . '_news WHERE id = ' . $item_id);
			if ($this->sql->query_ok()) {
				$row = $this->sql->fetch_array();
				$this->FillNewsItem($newsitem, $row);
			}
			return $newsitem;
		} else return NULL;
	}
	
	function UpdateNewsItem(&$newsitem, $delete=false) {
		if (!is_null($newsitem) && is_a($newsitem, 'NewsItem')) { // DEPRECIATED in PHP5
			$this->Clean4DB($newsitem);
			if ($delete && !is_null($newsitem->id) && ($newsitem->id > 0)) {
				if ($this->sql->query('DELETE FROM ' . $this->table_name . '_news WHERE id=' . $newsitem->id)) { 
					return true; 
				} else return false;
			}
			elseif (!is_null($newsitem->id) && ($newsitem->id > 0)) { // Member exists -> update
				$query = 'UPDATE ' . $this->table_name . '_news SET dateadd=dateadd, title=' . $this->sql->safe_value($newsitem->title, true) . ', content=' . $this->sql->safe_value($newsitem->content, true);
				$query .= ' WHERE id = ' . $newsitem->id;
				if ($this->sql->query($query)) {
					return true;
				} else return false;
			}
			elseif (!$delete) {
				if ($this->sql->query('INSERT INTO ' . $this->table_name . '_news (title, content, dateadd) VALUES (' . $this->sql->safe_value($newsitem->title, true) .  ', ' . $this->sql->safe_value($newsitem->content, true) .  ', NOW())')) {
					$newsitem->id = $this->sql->insert_id();
					return true;
				} else return false;
			}
			else return false;
		} else return false;
	}
	
	// ** PRIVATE METHOD **
	// Cleans the newsitem-info a bit, before it goes into the database.
	// Strips tags & trims
	function Clean4DB(&$newsitem) {	
		if (!is_null($newsitem->title)) {$newsitem->title = strip_tags(trim($newsitem->title)); }
		if (!is_null($newsitem->content)) {$newsitem->content = strip_tags(trim($newsitem->content)); }
	}
}
?>