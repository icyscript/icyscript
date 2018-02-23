<?php 
// Makes variables safe to display
if ('display.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');
	
// Version 1.0
class Display {
	var $charset;
	
	function Display($charset='UTF-8') {
		$this->charset = $charset;
	}
	
	function output($value) {
		return htmlentities($value, ENT_QUOTES, $this->charset);
	}
}
?>