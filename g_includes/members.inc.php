<?php 
if ('members.inc.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Security Error.');

$country_count = 0;
$orderby = (isset($_GET['orderby'])) ? trim($_GET['orderby']) : NULL;	
$sort 	 = (isset($_GET['sort'])) ? trim($_GET['sort']) : $fanlisting->settings['default_list_sort'];	
$country = (isset($_GET['country'])) ? trim($_GET['country']) : NULL;	

if (($sort == 'country') && is_null($country)) { 
	$countries = $fanlisting->GetCountries(false, $orderby);
	$country_count = count($countries); // Better performance, only 1 query.
} else {
	if (is_null($orderby)) {
		$orderby = $fanlisting->settings['default_list_order'];
	}
	$members = $fanlisting->MemberList('', is_null($country) ? NULL : 'country=\''.$country.'\'', $orderby);
	$country_count = $fanlisting->GetCountries(true);
}	
?>

<div class="boxy">
	<h3>Members</h3>
	<p><a href="membership.php?sort=all">Complete list</a> - <a href="membership.php?sort=country">Sorted by country</a></p>
	<p>We have a total of <strong><?php echo PHPFANLIST_MEMBERCOUNT; ?></strong> member(s) from <strong><?php echo $country_count; ?></strong> different countries.</p>
	
	<?php if (($sort == 'country') && is_null($country)) { // Same sort as above! ?>
	<h2>Sorted by country</h2>
	<div class="membership">
	
		<div class="one_half">
			<a href="membership.php?sort=country">Country</a>
			<?php foreach ($countries as $country) { ?>
				<p><a href="membership.php?country=<?php echo htmlentities($country['name'], ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo $country['name']; ?>"><?php $fake_member = new Member(); $fake_member->country = $country['name']; if (!DoPluginCalls('show_members_country', true, $fake_member)) { echo htmlentities($country['name'], ENT_QUOTES, 'UTF-8'); } ?></a></p>
			<?php } ?>
		</div>
		
		<div class="one_half">
			<a href="membership.php?sort=country&amp;orderby=num">Members</a>
			<?php foreach ($countries as $country) { ?>
				<p><?php echo $country['members']; ?></p>
			<?php } ?>		
		</div>
	</div>
	<span style="clear: left">&nbsp;</span>
<?php } else { 
	$querystring = '';
	$showmailcol = ShowMail(true, $fanlisting->settings['show_mail']);
?><h2><?php if (!is_null($country)) { $querystring = htmlentities('&country=' . $country, ENT_QUOTES, 'UTF-8'); ?>Sorted by country: <?php echo htmlentities($country, ENT_QUOTES, 'UTF-8'); } else {?>All members<?php } ?></h2>
	<div class="membership">
	
	<div class="one_fifth">
		<?php if ($fanlisting->settings['show_member_id']) { ?>
			<a href="membership.php?sort=all&amp;orderby=id<?php echo $querystring; ?>">ID</a>
		<?php } ?>
		<?php foreach ($members as $member) { ?>
			<?php if ($fanlisting->settings['show_member_id']) { ?>
				<p><?php if (!DoPluginCalls('show_members_id', true, $member)) { echo $member->id; }?></p>
			<?php } ?>
		<?php } ?>
	</div>
	
	<div class="one_fifth">
		<a href="membership.php?sort=all&amp;orderby=name<?php echo $querystring; ?>">Name</a>
		<?php foreach ($members as $member) { ?>
			<p><?php if (!DoPluginCalls('show_members_name', true, $member)) { echo htmlentities($member->name, ENT_QUOTES, 'UTF-8'); } ?></p>
		<?php } ?>
	</div>
	
	<div class="one_fifth">
		<?php if ($showmailcol) { ?>
			<a href="membership.php?sort=all&amp;orderby=mail<?php echo $querystring; ?>">email</a>
		<?php } ?>
		<?php foreach ($members as $member) { ?>
			<?php if ($showmailcol) { ?>
				<p><?php if (!DoPluginCalls('show_members_mail', true, $member)) { if (ShowMail($member->showmail, $fanlisting->settings['show_mail'])) { ?><a href="mailto:<?php echo scramble_email(htmlentities($member->mail, ENT_QUOTES, 'UTF-8')); ?>">@</a><?php } else { echo '-'; } } // Plugin Call ?></p>
			<?php } ?>
		<?php } ?>
	</div>
	
	<div class="one_fifth">
		<?php if ($fanlisting->settings['show_url']) { ?>
			<a href="membership.php?sort=all&amp;orderby=url<?php echo $querystring; ?>">URL</a>
		<?php } ?>
		<?php foreach ($members as $member) { ?>
			<?php if ($fanlisting->settings['show_url']) { ?>
				<p><?php if (!DoPluginCalls('show_members_url', true, $member)) { if (!is_empty($member->url)) { ?><a href="<?php echo htmlentities(handle_site($member->url), ENT_QUOTES, 'UTF-8'); ?>" target="_blank"<?php if ($fanlisting->settings['url_nofollow']) { echo ' rel="nofollow"'; }?>>www</a><?php } else {echo 'www'; } } // Plugin Call ?></p>
			<?php } ?>
		<?php } ?>
	</div>
	
	<div class="one_fifth">
		<?php if ($fanlisting->settings['ask_country'] && is_null($country)) { ?>
			<a href="membership.php?sort=all&amp;orderby=country">Country</a>
		<?php } ?>
		<?php foreach ($members as $member) { ?>
			<?php if ($fanlisting->settings['ask_country'] && is_null($country)) { ?>
				<p><?php if (!DoPluginCalls('show_members_country', true, $member)) { if (!is_empty($member->country)) { echo htmlentities($member->country, ENT_QUOTES, 'UTF-8'); } else { echo '-'; } } // Plugin Call ?></p>
			<?php } ?>
		<?php } ?>
	</div>
	
	<div class="one_fifth">
		<?php if ($fanlisting->settings['show_custom'] && ($fanlisting->settings['custom_field_name'] != '')) { ?>
			<a href="membership.php?sort=all&amp;orderby=custom<?php echo $querystring; ?>"><?php echo htmlentities($fanlisting->settings['custom_field_name'], ENT_QUOTES, 'UTF-8'); ?></a>
		<?php } ?>
		<?php foreach ($members as $member) { ?>
			<?php if ($fanlisting->settings['show_custom'] && ($fanlisting->settings['custom_field_name'] != '')) { ?>
				<p><?php if (!DoPluginCalls('show_members_custom', true, $member)) { if (!is_empty($member->custom)) { echo htmlentities($member->custom, ENT_QUOTES, 'UTF-8'); } else { echo '-'; } } // Plugin Call ?></p>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>

<?php } ?>

