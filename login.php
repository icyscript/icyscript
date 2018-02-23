<?php 
require_once('includes/front.inc.php');
$passok = true;
if (isset($_POST['user']) && isset($_POST['pass']) && (strcasecmp($_POST['user'], $fanlisting->settings['admin_name']) == 0) && (strcmp($_POST['pass'], $fanlisting->settings['admin_pass']) == 0)) {
	session_start();
	header("Cache-control: private"); // IE fix!!!
	$_SESSION['loggedin'] = 1;
	if (!isset($fanlisting->settings['cookie_lifetime'])) {
		$fanlisting->settings['cookie_lifetime'] = 60;
	}
	if (isset($_POST['rememberme']) && ($_POST['rememberme'] == 'yes')) {
		setcookie('phpfanlist_rememberme', 'yesplease', time()+60*60*24*$fanlisting->settings['cookie_lifetime'], '/');
		setcookie('phpfanlist_username', $_POST['user'], time()+60*60*24*$fanlisting->settings['cookie_lifetime'], '/');
	} else {
		setcookie('phpfanlist_rememberme', FALSE, time()+60*60*24*$fanlisting->settings['cookie_lifetime'], '/');
		setcookie('phpfanlist_username', FALSE, time()+60*60*24*$fanlisting->settings['cookie_lifetime'], '/');
	}
	if (isset($_SESSION['previous_url'])) {
		$url = $_SESSION['previous_url'];
		unset($_SESSION['previous_url']);
	} else { 
		$url = 'admin.php';

	}
	header('Location: ' . $url);
	exit;
} else { (isset($_POST['pass'])) ? $passok = false : $passok = true; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>The <?php if ($fanlisting->settings['approved']) { echo 'approved '; } echo htmlentities($fanlisting->settings['site_name'], ENT_QUOTES, 'UTF-8'); ?> [Login]</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo $fanlisting->settings['site_css']; ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<?php !$passok ? print("<p><strong>The password you provided is NOT valid!</strong></p>\n") : NULL; 
?><form action="login.php" method="post" accept-charset="utf-8">
<table>
	<tr>
		<td>login :&nbsp;</td>
		<td><input name="user" type="text" id="user"<?php  if (isset($_COOKIE['phpfanlist_username'])) { ?>value="<?php echo htmlentities($_COOKIE['phpfanlist_username'], ENT_QUOTES, 'UTF-8'); ?>"<?php } ?>></td>
	</tr>
	<tr>
		<td>password :&nbsp;</td>
		<td><input name="pass" type="password" id="pass"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="Submit" value="Login"></td>
	</tr>	
	<tr>
		<td colspan="2"><input name="rememberme" type="checkbox" id="rememberme" value="yes" class="noborder" <?php if (isset($_COOKIE['phpfanlist_rememberme']) && ($_COOKIE['phpfanlist_rememberme'] == 'yesplease')) { echo 'checked="checked" '; } ?>/> 
			remember me</td>
	</tr>
</table>
</form>
</body>
</html>
<?php error_reporting($err_setting); ?>