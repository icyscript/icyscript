<?php 
if ('plugins.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

//function AddPlugin(
//call_user_func_array(

if (!function_exists('AddPlugin')) {
	$plugins = array();
	$plugins['show_members_id'] = array();
	$plugins['show_members_name'] = array();
	$plugins['show_members_country'] = array();
	$plugins['show_members_mail'] = array();
	$plugins['show_members_url'] = array();
	$plugins['show_members_custom'] = array();
	$plugins['show_affiliates'] = array();
	$plugins['show_news'] = array();
	$plugins['show_news_item'] = array();
	$plugins['show_news_title'] = array();
	$plugins['show_news_content'] = array();
	
	// Function to add plugin to the call list.
	function AddPlugin($call, $functionname) {
		global $plugins;
		$call = strtolower($call);
		if (isset($plugins) && is_array($plugins) && array_key_exists($call, $plugins) && is_array($plugins[$call])) {
			array_push($plugins[$call], $functionname);
			return true;
		} else return false;
	}

	// Returns True when the calls were made, False when something went wrong (and default should be shown).
	// If output is true, it will automatically output the result of the plugins.
	function DoPluginCalls($call, $output=true, $parameter=NULL) {
		global $plugins;
		$result = '';
		$call = strtolower($call);
		if (isset($plugins) && is_array($plugins) && array_key_exists($call, $plugins) && is_array($plugins[$call])) {
			if (count($plugins[$call]) > 0) {
				foreach($plugins[$call] as $function) {
					if (function_exists($function)) {
						$function_result = NULL;
						switch ($call) {
							case 'show_members_id':
							case 'show_members_name':
							case 'show_members_country':
							case 'show_members_mail':
							case 'show_members_url':
							case 'show_members_custom':
							case 'show_news_title':
							case 'show_news_content':
								$function_result = call_user_func($function, $parameter);
								break;
							default:
								// show_affiliates
								// show_news
								$function_result = call_user_func($function);
								break;
						}
	
						if (!is_null($function_result) && ($function_result !== false)) { // If plugin returns NULL or false, don't add it to result.
							$result .= $function_result;
						} 
					}
				}
			
				if (($result != '') && $output) { 
					echo $result; 
					return true;
				} 
				elseif ($output) return true;
				else return false;
			} else return false; // No plugins to call;
		} else return false; // Not valid plugin array.
	}
	
	if (isset($fanlisting) && is_a($fanlisting, 'FrontFanlisting')) { // Depreciated in PHP5
		$installed_plugins = explode("\n", $fanlisting->settings['plugins']);
		
		foreach($installed_plugins as $plugin) {
			if (!is_null($plugin) && ($plugin != '')) {
				$path = realpath($fanlisting->settings['global_includedir'] . 'plugins/' . $plugin . '.plugin.php');
				if ($path !== false) {
					include_once($path);
				}
			}
		}
		unset($installed_plugins);
	}
	
	define('PHPFANLIST_PLUGINS', PHPFANLIST_INCLUDES . 'plugins/');
}
?>