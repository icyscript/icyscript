<?php
$err = error_reporting(0);
require('inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>phpFanList Install</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
	<h1>phpFanList Install</h1>
	<h2>This is the automated install/update for phpFanList version <strong></strong></h2>
<?php if (isset($_GET['action']) && ($_GET['action'] == 'proceed')) { ?>
	<p>Creating tables: <?php if (do_install()) { ?><strong>OK</strong><br />
	Inserting default settings: <?php if (do_insert()) { ?><strong>OK</strong></p>
	<p>The installation of phpFanList version <?php echo PHPFANLIST_VERSION_INSTALL; ?> is complete.<br />
	Don't forget to remove the <em>install</em> directory (for security reasons). Also the <em>license.txt</em>, <em>readme.txt</em> and <em>version.txt</em> files are not needed (this is documentation only) and can be removed.</p>
	<p>Before you start using phpFanList, you need to open the <a href="../admin.php">administration page</a> to define some settings (default username: <strong>admin</strong>, admin).</p>
	<?php register(); } /* Insert False */ else {?><strong class="attention">Failed!</strong></p><?php } } /* Tables False */ else { ?><span class="attention">Failed!</span></p><?php } 
	 } 
elseif (isset($_GET['action']) && ($_GET['action'] == 'update')) { if (do_update()) {?><p>phpFanList successfully updated to version <?php echo PHPFANLIST_VERSION_INSTALL; ?>.</p><?php } else { ?><p>Updating of your installation of phpFanList <strong>FAILED</strong>!</p>
<?php }
	} else { $ok4install = true; ?>
<?php if (!$connect) { ?><p><span class="attention">Could not connect to your database server!</span>.<br />Make sure all settings are filled in correctly.<br /><span class="note">(This is done in the <em>config.inc.php</em> file in the <em>includes</em> directory)</span></p><?php } ?>
<?php if ($db_name == '') { ?><p><span class="attention">You need to specify a database name. </span><br /><span class="note">(This is done in the <em>config.inc.php</em> file in the <em>includes</em> directory)</span></p><?php } ?>
<?php if (!$db_select) { ?><p><span class="attention">Could not select the database you specified.</span></p><?php } ?>
<?php if ($table_name == '') { ?><p><span class="attention">You need to specify a table name. </span><br /><span class="note">(This is done in the <em>config.inc.php</em> file in the <em>includes</em> directory)</span></p><?php } 
	if ($db_select && ($table_name != '')) {
		$prev_version = 'N/A';
		$sql = 'SHOW TABLE STATUS FROM ' . $db_name . ' LIKE \'' . $table_name . '\'';
		$result = mysql_query($sql, $connect);
		if (($result) && (mysql_num_rows($result) == 1)) {
			$ok4install = false;
			$sql = 'SELECT value FROM ' . $table_name . '_settings WHERE setting=\'version\'';
			$result = mysql_query($sql, $connect);
			if ($result && (mysql_num_rows($result) == 1)) {
				$row  = mysql_fetch_array($result);
				$prev_version = $row['value'];
			}/* Previous version nr */ 
			$sql = 'SELECT COUNT(1) AS num FROM ' . $table_name . '_temp';
			$result = mysql_query($sql, $connect);
			if ($result && (mysql_num_rows($result) == 1)) {
				$row  = mysql_fetch_array($result);
				$temps = $row['num'];
			}/* Number of items to approve */
		} /* Table exists */
		if (!$ok4install && ($prev_version != 'N/A') && ($prev_version < PHPFANLIST_VERSION_INSTALL) && ($prev_version >= PHPFANLIST_VERSION_UPDATE)) { 
			if (isset($temps) && ($temps > 0)) {
				?><p>A previous version of phpFanList was found (version: <?php echo $prev_version; ?>). Updating is possible, but you need to approve/decline all the join/update/delete requests first.</p><?php } else { ?><p>A previous version of phpFanList was found (version: <?php echo $prev_version; ?>). If you want to update it to version <?php echo PHPFANLIST_VERSION_INSTALL; ?>, click <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=update">update</a>.</p><p class="note">Note: Always take a backup of your database before updating.</p><?php 
			}
		}
		elseif (!$ok4install && ($prev_version != 'N/A') && ($prev_version >= PHPFANLIST_VERSION_INSTALL)) { ?><p>A previous version of phpFanList was found (version: <?php echo $prev_version; ?>) that is the same or newer than you're trying to install. Updating is not possible.</p><?php }
		elseif (!$ok4install && ($prev_version == 'N/A')) { ?><p>An unknown previous version of phpFanList was found. Updating is not possible.</p>
		<?php } elseif($ok4install) { ?><p class="large">To continue with the install, click <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=proceed">install</a></p>
		<p>Requirements:</p>
		<dl>
			<dt class="<?php echo (phpversion() > PHPFANLIST_REQ_PHP) ? 'good' : 'bad'; ?>"><strong>PHP</strong></dt>
			<dd class="<?php echo (phpversion() > PHPFANLIST_REQ_PHP) ? 'good' : 'bad'; ?>"><?php echo PHPFANLIST_REQ_PHP; ?> (you have <?php echo phpversion(); ?>)</dd>
			<dt class="<?php echo (PHPFANLIST_MYSQL > PHPFANLIST_REQ_MYSQL) ? 'good' : 'bad'; ?>"><strong>MySQL</strong></dt>
			<dd class="<?php echo (PHPFANLIST_MYSQL > PHPFANLIST_REQ_MYSQL) ? 'good' : 'bad'; ?>"><?php echo PHPFANLIST_REQ_MYSQL; ?> (you have <?php echo PHPFANLIST_MYSQL; ?>)</dd>
		</dl>
		<?php 
		} else { ?><p>An unknown error occured and phpFanList can't be installed.</p><?php }
	} else { /* Database connection not ok */ ?><p>Please make sure the database-connection is working correctly, and no table with the name <?php echo htmlentities($table_name); ?> exists.</p><?php 
	} 
} error_reporting($err); ?>
	<p id="copyright">Copyright &copy; 2003 - 2007 <a href="http://www.phpfanlist.com" title="phpFanList homepage">phpFanList.com</a>. Some Rights Reserved.</p>
</div>
</body>
</html>