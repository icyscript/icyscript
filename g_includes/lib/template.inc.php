<?php 
if ('template.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

// v 1.2
class Template {
	var $template_text = '';
	var $replace_text  = '';
	
	function Template() {
	}
	
	function load_templatefile($filename) {
		$content = '';
		if (file_exists($filename)) {
			$fp = fopen ($filename, 'r');
			while (!feof ($fp)) {
	   			$buf = fgets($fp, 4096);
				if ($buf != '') {
					$content .= $buf;
				}
			}
			fclose ($fp);
		} 
		$this->set_text($content);
	}
		
	function set_text($intext) {
		$this->template_text = $intext;
	}
	
	function get_text() {
		return $this->replace_text;
	}
	
	function do_replace($arr) {
		$this->replace_text = $this->template_text;
		foreach ($arr as $key => $value) {
			$this->replace_text = str_replace('%'.$key.'%', $value, $this->replace_text);
		}
		return $this->replace_text;
	}
	
	function do_reset() {
		$this->replace_array = array();
		$this->template_text = '';
		$this->replace_text = '';
	}
}
?>