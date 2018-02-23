<?php


// Password protect it \\
session_start();
header("Cache-control: private"); // IE fix!!!
if (isset($_GET['action']) && ($_GET['action'] == 'logout')) {
	$_SESSION = array();
	header('Location: login_.php');
	}
if ((!isset($_SESSION['loggedin'])) || ($_SESSION['loggedin'] != 1)) {

	//header('Location:admin.php');
	//exit;
	}
/***********************/

require_once('includes/inc.php');
require_once(realpath(PHPFANLIST_INCLUDES . 'admin.inc.php'));
		

?>