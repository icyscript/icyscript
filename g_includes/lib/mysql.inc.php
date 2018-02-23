<?php 
if ('mysql.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

// Version 1.6
class DB {
	var $db_host = NULL;
	var $db_pass = NULL;
	var $db_user = NULL;
	var $db_name = NULL;
	var $handle  = NULL;
	
	function DB($db_host, $db_user, $db_pass, $db_name) {
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_name = $db_name;
		$this->connect();
	} 
	
	function connect() {
		if (!$this->handle = mysql_connect($this->db_host, $this->db_user, $this->db_pass)) {
			return false;
		} else {
			if (!mysql_select_db($this->db_name, $this->handle)) { 
				return false; 
			} else return true;
		}
	}
}

class SQL {
	var $db_sql      = NULL; 
	var $db_result   = NULL;
	var $db_connect  = NULL;
	
	function SQL(&$db_connect) {
		$this->db_connect = &$db_connect;
	}
	
	function query($sql) {
		$this->reset();
		$this->db_sql = $sql;
		if ($this->db_connect) {
			if ($this->db_result = mysql_query($this->db_sql, $this->db_connect)) {
				$this->db_numrows = $this->num_rows();
				return ($this->db_result == true);
			} else return false;
		} else return false;
	}
	
	function fetch_array($res_type = MYSQL_ASSOC) {
		if ($this->db_result) {
			if ($row = mysql_fetch_array($this->db_result, $res_type)) {
				return $row;
			} else return false;
		} else return false;
	}
	
	function reset() {
		$this->db_sql = NULL;
		$this->db_result = NULL;
	}
	
	function num_rows() {
		if (!$this->db_result) { return 0; }
		if (preg_match('/^select/i', $this->db_sql)) {
			return mysql_num_rows($this->db_result);
		} else { return mysql_affected_rows($this->db_connect); }
	}
	
	function insert_id() {
		if (preg_match('/^insert/i', $this->db_sql)) {
			return mysql_insert_id($this->db_connect);
		} else return false;
	}
	
	function query_ok() {
		if ($this->db_result AND ($this->num_rows() > 0)) {
			return true;
		} else return false;
	}
	
	// Can make NULL too
	// Suspecting slashstripping has already happened.
	function safe_value ($value, $nullable=false, $allow_empty=false) {
		// Stripslashes
		//if (get_magic_quotes_gpc()) {
		//	$value = stripslashes($value);
		//}
		
		if ($nullable && (is_null($value) || strtolower($value) == 'null' || (!$allow_empty && ($value == '')))) {
			$value = 'NULL';
		} elseif (!is_numeric($value)) { // Quote if not a number or a numeric string
			$value = "'" . mysql_real_escape_string($value, $this->db_connect) . "'";
		}
		return $value;
	}
	
	function error() 
	{
		return mysql_error($this->db_connect);
	}
}
?>