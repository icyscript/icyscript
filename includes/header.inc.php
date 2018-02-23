<?php require_once('front.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>The <?php if ($fanlisting->settings['approved']) { echo 'approved '; } echo htmlentities($fanlisting->settings['site_name'], ENT_QUOTES, 'UTF-8'); isset($site_sub_title) ? print(' [' . htmlentities($site_sub_title, ENT_QUOTES, 'UTF-8') . ']') : NULL; ?></title>
	<meta name="generator" content="phpFanList <?php echo PHPFANLIST_VERSION; ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="<?php echo htmlentities($fanlisting->settings['site_css'], ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet" type="text/css" />
</head>

<body>
