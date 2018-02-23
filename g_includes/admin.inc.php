<?php
require_once('./includes/inc.php');

// Get the actions
require_once('admin.scripts.inc.php');
$fanlisting->LastChecked();

// For debugging so it's valid XHTML
if (error_reporting() == E_ALL) {
    header("Content-Type: application/xhtml+xml; charset=utf-8");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>The <?php if ($fanlisting->settings['approved']) { echo 'approved '; } echo $display->output($fanlisting->settings['site_name']); ?> [Administration]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="<?php echo $fanlisting->settings['web_includedir']; ?>adminstyle.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo $fanlisting->settings['web_includedir']; ?>phpfanlist.js"></script>
</head>
<body id="phpfanlist-admin">
<div id="wrapper">
    <h1><?php echo $display->output($fanlisting->settings['site_name']); ?> - Administration <?php if ($fanlisting->settings['site_url'] != '') {?><a href="<?php echo $display->output($fanlisting->settings['site_url']); ?>" class="viewsite external">&laquo; View Site &raquo;</a><?php } ?></h1>
    <ul id="menu">
        <li><a href="admin.php" title="Home">Home</a></li>
        <li><a href="admin.php?page=queue" title="Approve Updates &amp; Deletes">Queue</a></li>
        <li><a href="admin.php?page=list" title="Member List">Members</a></li>
        <li><a href="admin.php?page=edit" title="Add Member">Add</a></li>
        <li><a href="admin.php?page=mail" title="Send Mail">Send Mail</a></li>
        <li><a href="admin.php?page=affiliates" title="Affiliates">Affiliates</a></li>
        <li><a href="admin.php?page=news" title="News">News</a></li>
        <li><a href="admin.php?page=settings" title="Settings">Settings</a></li>
        <li><a href="admin.php?page=plugins" title="Plugins">Plugins</a></li>
        <li><a href="admin.php?page=tools" title="Tools">Tools</a></li>
        <li><a href="admin.php?action=logout" title="Logout">Logout</a></li>
    </ul>
    <?php if ((isset($message)) && ($message != '')) { ?><div class="message"><h2>Information</h2><p><?php echo nl2br($display->output(trim($message))); ?></p></div><?php }
    if ($_page == 'list') { ?>
        <form action="admin.php?action=search" method="post" name="search" id="search" accept-charset="utf-8">
            <fieldset>
                <legend>Filter</legend>
                <p><label for="search_name">Name</label> <input name="search_name" id="search_name" size="40"<?php defaultValue('search_name'); ?> /></p>
                <p><label for="search_mail">email</label> <input name="search_mail" id="search_mail" size="40"<?php defaultValue('search_mail'); ?> /></p>
                <p><label for="search_url">Url</label> <input name="search_url" id="search_url" size="40"<?php defaultValue('search_url'); ?> /></p>
                <p><label for="search_country">Country</label> <input name="search_country" id="search_country" size="40"<?php defaultValue('search_country'); ?> /></p>
                <p><input name="submit" type="submit" id="submit" value="Filter" /></p>
            </fieldset>
        </form>
        <table class="tableitemcollection">
            <thead>
            <tr>
                <th scope="col" class="col_id"><a href="admin.php?page=list&amp;orderby=id" title="Sort By ID">ID</a></th>
                <th scope="col" class="col_name"><a href="admin.php?page=list&amp;orderby=name" title="Sort By Name">Name</a></th>
                <th scope="col" class="col_date"><a href="admin.php?page=list&amp;orderby=date" title="Sort By Date">Date</a></th>
                <th scope="col" class="col_country"><a href="admin.php?page=list&amp;orderby=country" title="Sort By Country">Country</a></th>
                <th scope="col" class="col_actions">&nbsp;</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="4"><strong><?php echo count($members); ?></strong> member(s) displayed.</td>
                <td class="table_actions"><a href="admin.php?page=edit" title="Add a member"><img alt="Add" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/add-16.png" /> Add</a></td>
            </tr>
            <?php if ($fanlisting->settings['show_legend'] && (count($members) > 0)) { ?>
                <tr class="legend">
                <td colspan="5">
                    <img alt="View Profile" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /> Update Member
                    <img alt="Mail Member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/mail-message-new-16.png" /> Send Mail
                </td>
                </tr><?php } ?>
            </tfoot>
            <?php if (count($members) > 0) { ?>
                <tbody>
                <?php foreach($members as $member) { ?>
                    <tr<?php if (isset($member->extra['isdouble']) && ($member->extra['isdouble'] === true)) { ?> class="double"<?php } ?>>
                        <td><?php echo $member->id; ?></td>
                        <td><a href="admin.php?page=edit&amp;id=<?php echo $member->id; ?>"><?php echo $display->output($member->name); ?></a></td>
                        <td><?php echo date($fanlisting->settings['date_format'] , $member->dateadd ); ?></td>
                        <td><?php echo $display->output($member->country); ?></td>
                        <td>
                            <a href="admin.php?page=edit&amp;id=<?php echo $member->id; ?>"><img alt="Edit member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /></a>
                            <a href="admin.php?page=mail&amp;id=<?php echo $member->id; ?>"><img alt="Mail member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/mail-message-new-16.png" /></a>
                        </td>
                    </tr>
                <?php }?>
                </tbody><?php } ?>
        </table>
    <?php }	elseif ($_page == 'mail') { ?>

            <form action="admin.php?action=mail" method="post" name="mail" id="mail" accept-charset="utf-8">
            <table class="tableitem sendmail">
                <colgroup>
                    <col class="col_setting"></col>
                    <col class="col_settingvalue"></col>
                </colgroup>
                <thead>
                <tr>
                    <th colspan="2">Send Mail</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Member:</td>
                    <td>
                        <select name="id">
                            <option value="0"<?php is_active(is_null($member->id) || ($member->id == 0)); ?>>(everyone)</option>
                            <?php foreach($members as $some_member) { ?>
                                <option value="<?php echo $some_member->id; ?>"<?php is_active(!is_null($member->id) && ($member->id == $some_member->id)); ?>><?php echo $display->output($some_member->name . ' (' . $some_member->mail . ')'); ?></option><?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Subject:&nbsp;</td>
                    <td><input name="subject" type="text" id="subject" size="60" maxlength="250" /></td>
                </tr>
                <tr>
                    <td>Message:&nbsp;</td>
                    <td><textarea name="message" cols="60" rows="10" id="message"  ></textarea></td>
                </tr>
                <?php if (!$fanlisting->settings['is_expert']) {?>
                    <tr>
                        <td>&nbsp;</td>
                        <td><p class="warning">Sending mails to a large number of members, can take a lot time. So be patient!<br />
                                On some webhosts it can also be considered spam!</p></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>&nbsp;</td>
                    <td><input name="submit" type="submit" id="submit" value="Send Mail" /></td>
                </tr>
                </tbody>
            </table>
        </form>
    <?php } elseif ($_page == 'queue') { ?>
        <h2>Join Requests</h2>
        <form action="admin.php?action=handlequeue" method="post" accept-charset="utf-8">
            <table class="tablequeue">
                <thead>
                <tr>
                    <th scope="col" class="col_checkbox">&nbsp;</th>
                    <th scope="col" class="col_name">Name</th>
                    <th scope="col" class="col_mail">email</th>
                    <th scope="col" class="col_show_mail">Show Mail</th>
                    <th scope="col" class="col_url">Url</th>
                    <?php if ($fanlisting->settings['ask_custom']) { ?><th scope="col" class="col_custom">Custom</th><?php } ?>
                    <?php if ($fanlisting->settings['ask_rules']) { ?><th scope="col" class="col_rules">Rules</th><?php } ?>
                    <th scope="col" class="col_ip">IP</th>
                    <th scope="col" class="col_comment">Comment</th>
                    <th scope="col" class="col_actions">&nbsp;</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td class="table_checkall">
                        <input name="checkall_joins" id="checkall_joins" class="checkall" type="checkbox" value="all" />
                    </td>
                    <td class="table_actions">
                        <input name="approve_joins" id="approve_joins" value="1" type="image" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" title="Approve all selected joins" alt="Approve all" />
                        <input name="decline_joins" id="decline_joins" value="1" type="image" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" title="Decline all selected joins" alt="Decline all" />
                    </td>
                    <td colspan="<?php $cols = 6; if ($fanlisting->settings['ask_custom']) { $cols++; } if ($fanlisting->settings['ask_rules']) { $cols++; } echo $cols; ?>"><?php $count = count($join_members); echo ($count == 0) ? 'No join requests.' : $count . ' member(s) requested to join.'; ?></td>
                </tr>
                <?php if ($fanlisting->settings['show_legend'] && (count($join_members) > 0)) {?>
                    <tr class="legend">
                    <td colspan="<?php $cols = 8; if ($fanlisting->settings['ask_custom']) { $cols++; } if ($fanlisting->settings['ask_rules']) { $cols++; } echo $cols; ?>">
                        <img alt="Check Site" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/check-site-16.png" /> Check Site
                        <img alt="View Profile" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /> View Profile
                        <img alt="Approve" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" /> Approve
                        <img alt="Delete" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /> Delete
                    </td>
                    </tr><?php } ?>
                </tfoot>
                <tbody>
                <?php if (count($join_members) > 0) {
                    foreach($join_members as $member) { ?><tr>
                        <td><input name="qitem_<?php echo $member->tempid; ?>" id="qitem_<?php echo $member->tempid; ?>" type="checkbox" value="<?php echo $member->tempid; ?>" /></td>
                        <td>
                            <a href="admin.php?page=queueitem&amp;tid=<?php echo $member->tempid; ?>"><?php echo $display->output((is_null($member->name) ? '<no name>' : $member->name)); ?></a>
                        </td>
                        <td>
                            <?php echo (is_null($member->mail)) ? '&nbsp;' : $display->output($member->mail); ?>
                        </td>
                        <td>
                            <?php if (ShowMail($member->showmail, $fanlisting->settings['show_mail'], true)) {?><img alt="Yes" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/ok-16.png" /><?php } else { ?><img alt="No" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/cancel-16.png" /><?php } ?>
                        </td>
                        <td>
                            <?php if (is_null($member->url)) { echo '&nbsp;';} else { ?><a class="external" href="<?php echo $display->output(handle_site($member->url)); ?>"><img alt="www" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/check-site-16.png" /></a><?php } ?>
                        </td>
                        <?php if ($fanlisting->settings['ask_custom']) { ?><td>
                            <?php echo (is_null($member->custom)) ? '&nbsp;' : $display->output($member->custom); ?>
                            </td><?php } ?>
                        <?php if ($fanlisting->settings['ask_rules']) { ?><td>
                            <?php if (strtolower(trim($fanlisting->settings['rules_answer'])) == strtolower(trim($member['extra']->rules))) {?><img alt="Yes" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/ok-16.png" /><?php } else { ?><img alt="No" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/cancel-16.png" /><?php } ?>
                            </td><?php } ?>
                        <td><?php echo (isset($member->extra['ip'])) ? $member->extra['ip'] : 'N/A'; ?></td>
                        <td><?php echo (isset($member->extra['comment'])) ? nl2br($display->output($member->extra['comment'])) : '&nbsp;'; ?></td>
                        <td>
                            <a href="admin.php?page=queueitem&amp;tid=<?php echo $member->tempid?>"><img alt="View" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /></a>
                            <a href="admin.php?action=handlequeue&amp;do=approve&amp;tid=<?php echo $member->tempid; ?>"><img alt="Approve" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" /></a>
                            <a href="admin.php?action=handlequeue&amp;do=decline&amp;tid=<?php echo $member->tempid; ?>"><img alt="Delete" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /></a>
                        </td>
                        </tr>
                    <?php } } else { ?><tr><td></td></tr><?php } ?>
                </tbody>
            </table>
        </form>
        <h2>Update Requests</h2>
        <form action="admin.php?action=handlequeue" method="post" accept-charset="utf-8">
            <table class="tablequeue">
                <thead>
                <tr>
                    <th scope="col" class="col_checkbox">&nbsp;</th>
                    <th scope="col" class="col_id">ID</th>
                    <th scope="col" class="col_name">Name</th>
                    <th scope="col" class="col_mail">email</th>
                    <th scope="col" class="col_show_mail">Show Mail</th>
                    <th scope="col" class="col_url">Url</th>
                    <?php if ($fanlisting->settings['ask_custom']) { ?>
                        <th scope="col" class="col_custom">Custom</th>
                    <?php } ?>
                    <th scope="col" class="col_ip">IP</th>
                    <th scope="col" class="col_comment">Comment</th>
                    <th scope="col" class="col_actions">&nbsp;</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td class="table_checkall">
                        <input name="checkall_updates" id="checkall_updates" type="checkbox" value="all" class="checkall" />
                    </td>
                    <td colspan="2" class="table_actions">
                        <input name="approve_updates" id="approve_updates" value="1" type="image" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" title="Approve all selected updates" alt="Approve all" />
                        <input name="decline_updates" id="decline_updates" value="1" type="image" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" title="Decline all selected updates" alt="Decline all" />
                    </td>
                    <td colspan="<?php echo ($fanlisting->settings['ask_custom'])  ? '7' : '6'; ?>"><?php $count = count($update_members); echo ($count == 0) ? 'No members requested to be updated.' : $count . ' member(s) requested to be updated.'; ?></td>
                </tr>
                <?php if ($fanlisting->settings['show_legend'] && (count($update_members) > 0)) {?>
                    <tr class="legend">
                        <td colspan="<?php echo ($fanlisting->settings['ask_custom'])  ? '10' : '9'; ?>"> <img alt="Updated" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" /> Updated value <img alt="Check Site" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/check-site-16.png" /> Check Site <img alt="View Profile" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /> View Profile <img alt="Mail Member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/mail-message-new-16.png" /> Check Site <img alt="Approve" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" /> Approve <img alt="Delete" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /> Delete </td>
                    </tr>
                <?php } ?>
                </tfoot>
                <tbody>
                <?php if (count($update_members) > 0) {
                    foreach($update_members as $member) { ?>
                        <tr>
                            <td><input name="qitem_<?php echo $member->tempid; ?>" id="qitem_<?php echo $member->tempid; ?>" type="checkbox" value="<?php echo $member->tempid; ?>" /></td>
                            <td><?php echo $member->extra['member']->id; ?></td>
                            <td> <a href="admin.php?page=queueitem&amp;tid=<?php echo $member->tempid; ?>"><?php echo $display->output(((is_null($member->name)) ? $member->extra['member']->name : $member->name)); ?></a>
                                <?php if (!is_null($member->name)) { ?>
                                    <img alt="*new*" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" />
                                <?php } ?>
                            </td>
                            <td> <?php echo $display->output(((is_null($member->mail)) ? $member->extra['member']->mail : $member->mail)); ?>
                                <?php if (!is_null($member->mail)) { ?>
                                    <img alt="*new*" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" />
                                <?php } ?>
                            </td>
                            <td>
                                <?php if (ShowMail($member->showmail, $fanlisting->settings['show_mail'], true)) {?>
                                    <img alt="Yes" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/ok-16.png" />
                                <?php } else { ?>
                                    <img alt="No" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/cancel-16.png" />
                                <?php } ?>
                                <?php if (!is_null($member->showmail) && ($member->showmail != $member->extra['member']->showmail)) { ?>
                                    <img alt="*new*" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" />
                                <?php } ?>
                            </td>
                            <td>
                                <?php
                                if (!is_null($member->url) && ($member->url !== false)) { $url = $member->url; }
                                elseif (is_null($member->url) && !is_empty($member->extra['member']->url)) { $url = $member->extra['member']->url; }
                                else { $url = NULL; }
                                if (!is_null($url)) {?>
                                    <a class="external" href="<?php echo $display->output(((is_null($member->url)) ? $member->extra['member']->url : handle_site($member->url))); ?>"><img alt="www" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/check-site-16.png" /></a>
                                <?php } else { echo '&nbsp;'; }
                                if (!is_null($member->url)) { ?>
                                    <img alt="*new*" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" />
                                <?php } ?>
                            </td>
                            <?php if ($fanlisting->settings['ask_custom']) { ?>
                                <td> <?php echo $display->output(((is_null($member->custom)) ? $member->extra['member']->custom : $member->custom)); ?>
                                    <?php if (!is_null($member->custom)) { ?>
                                        <img alt="*new*" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" />
                                    <?php } ?>
                                </td>
                            <?php } ?>
                            <td><?php echo (isset($member->extra['ip'])) ? $member->extra['ip'] : 'N/A'; ?></td>
                            <td><?php echo (isset($member->extra['comment'])) ? nl2br($display->output($member->extra['comment'])) : '&nbsp;'; ?></td>
                            <td> <a href="admin.php?page=queueitem&amp;tid=<?php echo $member->tempid?>"><img alt="View" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /></a> <a href="admin.php?page=mail&amp;id=<?php echo $member->extra['member']->id?>"><img alt="Mail member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/mail-message-new-16.png" /></a> <a href="admin.php?action=handlequeue&amp;do=approve&amp;tid=<?php echo $member->tempid?>&amp;id=<?php echo $member->extra['member']->id?>"><img alt="Approve" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" /></a> <a href="admin.php?action=handlequeue&amp;do=decline&amp;tid=<?php echo $member->tempid?>&amp;id=<?php echo $member->extra['member']->id?>"><img alt="Delete" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /></a> </td>
                        </tr>
                    <?php } } else { ?>
                    <tr>
                        <td></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </form>
        <h2>Delete Requests</h2>
        <form action="admin.php?action=handlequeue" method="post" accept-charset="utf-8">
            <table class="tablequeue">
                <thead>
                <tr>
                    <th scope="col" class="col_checkbox">&nbsp;</th>
                    <th scope="col" class="col_id">ID</th>
                    <th scope="col" class="col_name">Name</th>
                    <th scope="col" class="col_ip">IP</th>
                    <th scope="col" class="col_comment">Comment</th>
                    <th scope="col" class="col_actions">&nbsp;</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td class="table_checkall">
                        <input name="checkall_deletes" id="checkall_deletes" type="checkbox" class="checkall" value="all" />
                    </td>
                    <td colspan="2" class="table_actions">
                        <input name="approve_deletes" id="approve_deletes" value="1" type="image" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" title="Approve all selected deletes" alt="Approve all" />
                        <input name="decline_deletes" id="decline_deletes" value="1" type="image" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" title="Decline all selected deletes" alt="Decline all" />
                    </td>
                    <td colspan="3"><?php $count = count($delete_members); echo ($count == 0) ? 'No members requested to be deleted.' : $count . ' member(s) requested to be deleted.'; ?></td>
                </tr>
                <?php if ($fanlisting->settings['show_legend'] && (count($delete_members) > 0)) {?>
                    <tr class="legend">
                    <td colspan="6">
                        <img alt="View Profile" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /> View Profile
                        <img alt="Mail Member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/mail-message-new-16.png" /> Check Site
                        <img alt="Approve" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" /> Approve
                        <img alt="Delete" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /> Delete
                    </td>
                    </tr><?php } ?>
                </tfoot>
                <tbody>
                <?php if (count($delete_members) > 0) {
                    foreach($delete_members as $member) { ?>
                        <tr>
                            <td><input name="qitem_<?php echo $member->tempid; ?>" id="qitem_<?php echo $member->tempid; ?>" type="checkbox" value="<?php echo $member->tempid; ?>" /></td>
                            <td><?php echo $member->extra['member']->id; ?></td>
                            <td><a href="admin.php?page=queueitem&amp;tid=<?php echo $member->tempid; ?>"><?php echo $display->output($member->extra['member']->name); ?></a></td>
                            <td><?php echo (isset($member->extra['ip'])) ? $member->extra['ip'] : 'N/A'; ?></td>
                            <td><?php echo (isset($member->extra['comment'])) ? nl2br($display->output($member->extra['comment'])) : '&nbsp;'; ?></td>
                            <td>
                                <a href="admin.php?page=queueitem&amp;tid=<?php echo $member->tempid?>"><img alt="View" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/member-edit-16.png" /></a>
                                <a href="admin.php?page=mail&amp;id=<?php echo $member->extra['member']->id?>"><img alt="Mail member" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/mail-message-new-16.png" /></a>
                                <a href="admin.php?action=handlequeue&amp;do=approve&amp;tid=<?php echo $member->tempid?>&amp;id=<?php echo $member->id?>"><img alt="Approve" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/approve-16.png" /></a>
                                <a href="admin.php?action=handlequeue&amp;do=decline&amp;tid=<?php echo $member->tempid?>&amp;id=<?php echo $member->id?>"><img alt="Delete" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /></a>
                            </td>
                        </tr>
                    <?php } } else { ?><tr><td></td></tr><?php } ?></tbody>
            </table>
        </form>
    <?php } elseif ($_page == 'queueitem') { if (!(isset($message) && ($message != '')) && !is_null($member)) { ?>
        <form action="admin.php?action=handlequeueitem" method="post" accept-charset="utf-8">
            <table class="tableitem">
                <colgroup span="2">
                    <col class="col_setting"></col>
                    <col class="col_settingvalue"></col>
                </colgroup>
                <thead>
                <tr>
                    <th colspan="2">Queue <?php
                        switch ($member->extra['action']) {
                            case 0:
                                echo 'Join';
                                break;
                            case 1:
                                echo 'Update';
                                break;
                            case 2:
                                echo 'Delete';
                                break;
                        }
                        ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td><input name="submit" type="submit" <?php
                        switch ($member->extra['action']) {
                            case 0:
                                echo 'value="Join"';
                                break;
                            case 1:
                                echo 'value="Update"';
                                break;
                            case 2:
                                echo 'value="Delete"';
                                break;
                            default:
                                echo 'value="Submit"';
                                break;
                        } ?> /><input name="tid" type="hidden" value="<?php echo $member->tempid; ?>" /></td>
                    <td> <input name="do" type="radio" value="approve" />Approve&nbsp;<input name="do" type="radio" value="decline" />Decline</td>
                </tr>
                </tfoot>
                <tbody>
                <?php if ($display_items['name']) { ?><tr>
                    <td><label for="name">Name</label></td>
                    <td><input name="name" type="text" id="name" value="<?php if (!is_null($member->name)) { echo $display->output($member->name); } ?>" size="40" maxlength="100"<?php if ($display_items['name'] < 0) {?> readonly="readonly"<?php } ?> /></td>
                    </tr>
                <?php } if ($display_items['mail']) { ?>
                    <tr>
                        <td><label for="mail">email</label></td>
                        <td><input name="mail" type="text" id="mail" value="<?php if (!is_null($member->mail)) { echo $display->output($member->mail); } ?>" size="40" maxlength="255"<?php if ($display_items['mail'] < 0) {?> readonly="readonly"<?php } ?> /></td>
                    </tr>
                <?php } if ($display_items['showmail']) { ?>
                    <tr>
                        <td><label for="showmail">Show email</label></td>
                        <td><?php $showmail = (is_null($member->id)) ? ShowMail((is_null($member->showmail) ? false : true), $fanlisting->settings['show_mail'], true) : ShowMail($member->showmail, $fanlisting->settings['show_mail'], true); if ($fanlisting->settings['show_mail'] > 2) {
                                ?>Yes<input name="showmail" type="radio" value="1"<?php is_checked($showmail); ?> class="noborder" />
                                &nbsp;No<input name="showmail" type="radio" value="0"<?php is_checked(!$showmail); ?> class="noborder" /><?php
                            } else { echo ($showmail) ? 'Yes' : 'No'; ?><input name="showmail" type="hidden" id="showmail" value="<?php echo 'NULL'; ?>" /><?php } ?>
                        </td>
                    </tr>
                <?php } if ($display_items['url']) { ?>
                    <tr>
                        <td><label for="url">Url</label></td>
                        <td>
                            <input name="url" type="text" id="url" value="<?php if (!is_null($member->url)) { echo handle_site($display->output($member->url)); } ?>" size="40" maxlength="255"<?php if ($display_items['url'] < 0) {?> readonly="readonly"<?php } ?> />
                            <?php if (!is_empty($member->url)) { ?>&nbsp;<a href="<?php echo handle_site($member->url); ?>" onclick="return openUrl('url', this, event);" title="Check Site"><img alt="Check Site" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/check-site-16.png" /></a><?php } ?>
                        </td>
                    </tr>
                <?php } if ($display_items['country']) { ?>
                    <tr>
                        <td><label for="country">Country</label></td>
                        <td><input name="country" type="text" id="country" value="<?php if (!is_null($member->country)) { echo $display->output($member->country); } ?>" size="40" maxlength="150"<?php if ($display_items['country'] < 0) {?> readonly="readonly"<?php } ?> /></td>
                    </tr>
                <?php } if ($display_items['custom']) { ?>
                    <?php if ($fanlisting->settings['custom_field_name'] != '') { ?>
                        <tr>
                            <td><label for="custom"><?php echo $display->output($fanlisting->settings['custom_field_name']); ?></label></td>
                            <td><input name="custom" type="text" id="custom" value="<?php if (!is_null($member->custom)) { echo $display->output($member->custom); } ?>" size="40" maxlength="255"<?php if ($display_items['custom'] < 0) {?> readonly="readonly"<?php } ?> /></td>
                        </tr>
                    <?php } ?>
                <?php } if ($display_items['rules']) { ?>
                    <?php if (isset($member->extra['rules']) && $fanlisting->settings['ask_rules']) { ?>
                        <tr>
                            <td>
                                Rules: &nbsp;<?php if (($fanlisting->settings['rules_answer'] != '') && (!stristr($member->extra['rules'], $fanlisting->settings['rules_answer']))){ ?><img alt="Not OK" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/cancel-16.png" /><?php } else { ?><img alt="OK" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/ok-16.png" /><?php } ?><br />
                                <?php echo ($fanlisting->settings['ask_rules'] == 2) ? '<strong>Required</strong>' : '<em>Not required.</em>'; ?>
                            </td>
                            <td>
                                <?php echo $display->output($fanlisting->settings['rules_question']); ?> [<?php echo $display->output($fanlisting->settings['rules_answer']); ?>]<br />
                                <?php echo (isset($member->extra['rules'])) ? nl2br($display->output($member->extra['rules'])) : '&nbsp;'; ?>
                            </td>
                        </tr>
                    <?php } } // display_items ?>
                <tr>
                    <td>IP</td>
                    <td><?php echo (isset($member->extra['ip'])) ? $member->extra['ip'] : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Comment</td>
                    <td><?php echo (isset($member->extra['comment'])) ? nl2br($display->output($member->extra['comment'])) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td class="multiline"><label for="addmess">Additional Message</label></td>
                    <td>
                        <p><textarea name="addmess" cols="35" rows="3" id="addmess"></textarea></p>
                        <?php if (!$fanlisting->settings['is_expert']) {?><p class="smaller">This additional message will be included in the notify-mail send to the applicant.</p><?php } ?>
                        <input name="notify" id="notify" type="checkbox" value="yes" <?php is_checked($fanlisting->settings['mail_approve']); ?> />
                        Notify (send mail)</td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php if ($member->extra['action'] == 1) {?><p class="information">Fields that were not changed are read-only.</p><?php }
    } // No item to display.
    } elseif ($_page == 'edit') { ?>
        <form action="admin.php?action=modify" method="post" accept-charset="utf-8">
            <table class="tableitem">
                <colgroup span="2">
                    <col class="col_setting"></col>
                    <col class="col_settingvalue"></col>
                </colgroup>
                <thead>
                <tr>
                    <th colspan="2"><?php echo (is_null($member->id)) ? 'Add' : 'Update'; ?> Member</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td><input name="submit" type="submit" id="submit" value="<?php echo (is_null($member->id)) ? 'Add' : 'Update'; ?>" /></td>
                    <td><?php if (is_null($member->id)) { ?><input name="do" type="hidden" value="update" checked="checked" class="noborder" /><?php } else { ?><input name="do" type="radio" value="update" checked="checked" class="noborder" />
                            Update<input type="radio" name="do" value="remove" class="noborder" />
                            Remove<?php } ?></td>
                </tr>
                </tfoot>
                <tbody>
                <tr>
                    <td>ID:</td>
                    <td><?php echo (is_null($member->id)) ? 'New member' : $member->id; ?><input name="id" type="hidden" id="id" value="<?php echo (is_null($member->id)) ? '0' : $member->id; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="name">Name</label>:</td>
                    <td><input name="name" type="text" id="name" value="<?php if (!is_null($member->name)) { echo $display->output($member->name); } ?>" size="40" maxlength="100" /></td>
                </tr>
                <tr>
                    <td><label for="mail">email</label>:</td>
                    <td><input name="mail" type="text" id="mail" value="<?php if (!is_null($member->mail)) { echo $display->output($member->mail); } ?>" size="40" maxlength="255" /></td>
                </tr>
                <tr>
                    <td><label for="showmail">Show email </label>:</td>
                    <td><?php $showmail = (is_null($member->id)) ? ShowMail(false, $fanlisting->settings['show_mail'], true) : ShowMail($member->showmail, $fanlisting->settings['show_mail']); if ($fanlisting->settings['show_mail'] > 2) {
                            ?>Yes<input name="showmail" type="radio" value="1"<?php if (!is_null($member->id)) { is_checked($showmail); } ?> class="noborder" />&nbsp;
                                                                                                                                                                No<input name="showmail" type="radio" value="0"<?php if (!is_null($member->id)) { is_checked(!$showmail); } ?> class="noborder" /><?php
                        } else { echo ($showmail) ? 'Yes' : 'No'; ?><input name="showmail" type="hidden" id="showmail" value="<?php echo 'NULL'; ?>" /><?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="url">Url</label>
                        :</td>
                    <td><input name="url" type="text" id="url" value="<?php if (!is_null($member->url)) { echo handle_site($display->output($member->url)); } ?>" size="40" maxlength="255" />
                        <?php if (!is_empty($member->url)) { ?>&nbsp;<a href="<?php echo handle_site($member->url); ?>" onclick="return openUrl('url', this, event);" title="Check Site"><img alt="Check Site" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/check-site-16.png" /></a><?php } ?></td>
                </tr>
                <?php if ($fanlisting->settings['ask_country']) { ?><tr>
                    <td><label for="country">Country</label>:</td>
                    <td><?php if (is_null($member->id)) { ?><select name="country" id="country">
                            <?php include($fanlisting->settings['global_includedir'] . 'countrylist.inc.php'); ?>
                            </select><?php } else { ?><input name="country" type="text" id="country" value="<?php echo $display->output($member->country); ?>" size="40" maxlength="150" /><?php } ?>
                    </td>
                    </tr><?php } ?><tr>
                    <td>Date Approved:&nbsp;</td>
                    <td><?php echo (is_null($member->id)) ? date($fanlisting->settings['date_format'], mktime() + ($fanlisting->settings['timediff'] *3600)) : date($fanlisting->settings['date_format'], $member->dateadd); ?></td>
                </tr>
                <?php if ($fanlisting->settings['custom_field_name'] != '') { ?>
                    <tr>
                        <td><?php echo $fanlisting->settings['custom_field_name']; ?>:&nbsp;</td>
                        <td><input name="custom" type="text" id="custom" value="<?php if (!is_null($member->custom)) { echo $display->output($member->custom); } ?>" size="40" maxlength="255" />
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </form>
    <?php } elseif ($_page == 'affiliates') {?>
        <table class="tableitemcollection affiliates">
            <thead>
            <tr>
                <th scope="col" class="col_name"><a href="admin.php?page=affiliates&amp;orderby=name" title="Sort By Name">Name</a></th>
                <th scope="col" class="col_category"><a href="admin.php?page=affiliates&amp;orderby=category" title="Sort By Category">Category</a></th>
                <th scope="col" class="col_image">Image</th>
                <th scope="col" class="col_actions">&nbsp;</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"><strong><?php echo count($affiliates); ?></strong> affiliate(s).</td>
                <td class="table_actions"><a href="admin.php?page=editaffiliate" title="Add an affiliate"><img alt="Add" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/add-16.png" /> Add</a></td>
            </tr>
            </tfoot>
            <?php if (count($affiliates) > 0) { ?>
                <tbody>
                <?php foreach($affiliates as $affiliate) { ?><tr>
                    <td><a href="admin.php?page=editaffiliate&amp;affiliateid=<?php echo $affiliate->id; ?>"><?php echo $display->output($affiliate->name); ?></a></td>
                    <td><?php if (!is_empty($affiliate->category)) { echo $display->output($affiliate->category); } else echo '-'; ?></td>
                    <td><?php if (!is_empty($affiliate->imageurl)) {?><img src="<?php echo $display->output($affiliate->imageurl); ?>" alt="<?php echo $display->output($affiliate->name); ?>" /><?php } else echo '&nbsp;'; ?></td>
                    <td></td>
                    </tr><?php } ?>
                </tbody><?php } ?>
        </table>
    <?php } elseif ($_page == 'editaffiliate') {?>
        <form method="post" action="admin.php?action=updateaffiliate" accept-charset="utf-8">
            <table class="tableitem">
                <colgroup span="2">
                    <col class="col_setting"></col>
                    <col class="col_settingvalue"></col>
                </colgroup>
                <thead>
                <tr>
                    <th colspan="2"><?php echo (is_null($affiliate->id)) ? 'Add' : 'Update'; ?> Affiliate</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td><input name="affiliateid" type="hidden" value="<?php echo (is_null($affiliate->id) ? '0' : $affiliate->id); ?>" /><input name="submit" type="submit" id="submit" value="<?php echo (is_null($affiliate->id)) ? 'Add' : 'Update'; ?>" /></td>
                    <td><?php if (is_null($affiliate->id)) { ?><input name="do" type="hidden" value="update" checked="checked" class="noborder" /><?php } else { ?><input name="do" type="radio" value="update" checked="checked" class="noborder" />
                            Update<input type="radio" name="do" value="remove" class="noborder" />
                            Remove<?php } ?></td>
                </tr>
                </tfoot>
                <tbody>
                <tr>
                    <td><label for="affiliatename">Name</label></td>
                    <td><input name="affiliatename" type="text" id="affiliatename" size="40" maxlength="50" value="<?php if (!is_null($affiliate->name)) { echo $display->output($affiliate->name); } ?>" /></td>
                </tr>
                <tr>
                    <td><label for="affiliatecategory">Category</label></td>
                    <td><input name="affiliatecategory" type="text" id="affiliatecategory" size="40" maxlength="100" value="<?php if (!is_null($affiliate->category)) { echo $display->output($affiliate->category); } ?>" /></td>
                </tr>
                <tr>
                    <td><label for="affiliateurl">Url</label></td>
                    <td><input name="affiliateurl" type="text" id="affiliateurl" size="40" maxlength="255" value="<?php if (!is_null($affiliate->url)) { echo $display->output($affiliate->url); } ?>" /></td>
                </tr>
                <tr>
                    <td><label for="affiliateimageurl">Image Url</label></td>
                    <td><input name="affiliateimageurl" type="text" id="affiliateimageurl" size="40" maxlength="255" value="<?php if (!is_null($affiliate->imageurl)) { echo $display->output($affiliate->imageurl); } ?>" /></td>
                </tr>
                </tbody>
            </table>
        </form>
    <?php } elseif ($_page == 'news') {?>
        <table class="tableitemcollection news">
            <thead>
            <tr>
                <th scope="col" class="col_title"><a href="admin.php?page=news&amp;orderby=title" title="Sort By Title">Title</a></th>
                <th scope="col" class="col_date"><a href="admin.php?page=news&amp;orderby=dateadd" title="Sort By Date">Date</a></th>
                <th scope="col" class="col_actions">&nbsp;</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="2"><strong><?php echo count($newsitems); ?></strong> news item(s).</td>
                <td class="table_actions"><a href="admin.php?page=editnewsitem" title="Add a news item"><img alt="Add" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/add-16.png" /> Add</a></td>
            </tr>
            </tfoot>
            <?php if (count($newsitems) > 0) { ?>
                <tbody>
                <?php foreach($newsitems as $newsitem) { ?><tr>
                    <td><a href="admin.php?page=editnewsitem&amp;newsitemid=<?php echo $newsitem->id; ?>"><?php echo ((trim($newsitem->title) == '') ? '[no title]' : $display->output($newsitem->title)); ?></a></td>
                    <td><?php echo date('dS F Y H:s', $newsitem->dateadd + ($fanlisting->settings['timediff'] *3600)); ?></td>
                    <td></td>
                    </tr><?php } ?>
                </tbody><?php } ?>
        </table>
    <?php } elseif ($_page == 'editnewsitem') {?>
        <form method="post" action="admin.php?action=updatenewsitem" accept-charset="utf-8">
            <table class="tableitem">
                <colgroup span="2">
                    <col class="col_setting"></col>
                    <col class="col_settingvalue"></col>
                </colgroup>
                <thead>
                <tr>
                    <th colspan="2"><?php echo (is_null($affiliate->id)) ? 'Add' : 'Update'; ?> News item</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td><input name="newsitemid" type="hidden" value="<?php echo (is_null($newsitem->id) ? '0' : $newsitem->id); ?>" /><input name="submit" type="submit" id="submit" value="<?php echo (is_null($newsitem->id)) ? 'Add' : 'Update'; ?>" /></td>
                    <td><?php if (is_null($newsitem->id)) { ?><input name="do" type="hidden" value="update" checked="checked" class="noborder" /><?php } else { ?><input name="do" type="radio" value="update" checked="checked" class="noborder" />
                            Update<input type="radio" name="do" value="remove" class="noborder" />
                            Remove<?php } ?></td>
                </tr>
                </tfoot>
                <tbody>
                <tr>
                    <td>Date &amp; Time</td>
                    <td><?php if (!is_null($newsitem->dateadd)) { echo date('dS F Y H:s', $newsitem->dateadd + ($fanlisting->settings['timediff'] * 3600)); } else { echo date('dS F Y H:s', mktime() + ($fanlisting->settings['timediff'] * 3600)); } ?></td>
                </tr>
                <tr>
                    <td><label for="newsitemtitle">Title</label></td>
                    <td><input name="newsitemtitle" type="text" id="newsitemtitle" size="40" maxlength="255" value="<?php if (!is_null($newsitem->title)) { echo $display->output($newsitem->title); } ?>" /></td>
                </tr>
                <tr>
                    <td><label for="newsitemcontent">Content</label></td>
                    <td><textarea name="newsitemcontent" cols="55" rows="10" id="newsitemcontent"><?php if (!is_null($newsitem->content)) { echo $display->output($newsitem->content); } ?></textarea></td>
                </tr>
                </tbody>
            </table>
        </form>
    <?php } elseif ($_page == 'settings') {?>
        <form action="admin.php?action=settings" method="post" accept-charset="utf-8">
            <h2>List Settings</h2>
            <table class="tablesettings">
                <thead>
                <tr>
                    <th scope="col" class="col_setting">Setting</th>
                    <th scope="col" class="col_settingvalue">Value</th>
                    <th scope="col" class="col_description">Description</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><label for="s_list_type">List type</label></td>
                    <td>
                        <select name="s_list_type" id="s_list_type">
                            <option value="4"<?php is_active($fanlisting->settings['list_type'], 4); ?>>General Listing</option>
                            <option value="0"<?php is_active($fanlisting->settings['list_type'], 0); ?>>Fanlisting</option>
                            <option value="3"<?php is_active($fanlisting->settings['list_type'], 3); ?>>Anime Fanlisting</option>
                            <option value="1"<?php is_active($fanlisting->settings['list_type'], 1); ?>>Clique</option>
                            <option value="2"<?php is_active($fanlisting->settings['list_type'], 2); ?>>Namelisting</option>
                            <option value="5"<?php is_active($fanlisting->settings['list_type'], 5); ?>>Numberlisting</option>
                        </select>
                    </td>
                    <td>Is phpFanList used for a fanlisting, clique or namelisting.</td>
                </tr>
                <tr>
                    <td><label for="s_approved">Approved</label></td>
                    <td>
                        <select name="s_approved" id="s_approved">
                            <option value="1"<?php is_active($fanlisting->settings['approved']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['approved']); ?>>No</option>
                        </select>
                    </td>
                    <td>This will add 'approved' to your fanlisting title.</td>
                </tr>
                <tr>
                    <td><label for="s_site_name">Site Name</label></td>
                    <td><input name="s_site_name" type="text" id="s_site_name" value="<?php echo $display->output($fanlisting->settings['site_name']); ?>" size="30" maxlength="255" /></td>
                    <td>Name of the site.</td>
                </tr>
                <tr>
                    <td><label for="s_site_url">Site URL</label></td>
                    <td><input name="s_site_url" type="text" id="s_site_url" value="<?php echo $display->output($fanlisting->settings['site_url']); ?>" size="30" maxlength="255" /></td>
                    <td>Url where the site can be found.  (don't forget the ending /)</td>
                </tr>
                <tr>
                    <td><label for="s_site_css">Site CSS</label></td>
                    <td><input name="s_site_css" type="text" id="s_site_css" value="<?php echo $display->output($fanlisting->settings['site_css']); ?>" size="30" maxlength="255" /></td>
                    <td>Style for the site. </td>
                </tr>
                <tr>
                    <td><label for="s_owner_name">Owner Name</label></td>
                    <td><input name="s_owner_name" type="text" id="s_owner_name" value="<?php echo $display->output($fanlisting->settings['owner_name']); ?>" size="30" maxlength="255" /></td>
                    <td>That would be your name </td>
                </tr>
                <tr>
                    <td><label for="s_owner_mail">Owner email</label></td>
                    <td><input name="s_owner_mail" type="text" id="s_owner_mail" value="<?php echo $display->output($fanlisting->settings['owner_mail']); ?>" size="30" maxlength="255" /></td>
                    <td>Your email address (for sending you a message when someone joins/updates/...) </td>
                </tr>
                <tr>
                    <td><label for="s_mail_on_join">Mail on member action </label></td>
                    <td>
                        <select name="s_mail_on_join" id="s_mail_on_join">
                            <option value="1"<?php is_active($fanlisting->settings['mail_on_join']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['mail_on_join']); ?>>No</option>
                        </select>
                    </td>
                    <td>When enabled, member gets mail when he/she joins/updates.</td>
                </tr>
                <tr>
                    <td><label for="s_mail_approve">Mail on admin action</label></td>
                    <td>
                        <select name="s_mail_approve" id="s_mail_approve">
                            <option value="1"<?php is_active($fanlisting->settings['mail_approve']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['mail_approve']); ?>>No</option>
                        </select>
                    </td>
                    <td>When enabled, member gets mail when admin approves/declines.</td>
                </tr>
                <tr>
                    <td><label for="s_mail_admin">Mail to notify admin </label></td>
                    <td>
                        <select name="s_mail_admin" id="s_mail_admin">
                            <option value="1"<?php is_active($fanlisting->settings['mail_admin']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['mail_admin']); ?>>No</option>
                        </select>
                    </td>
                    <td>When enabled, the admin gets a mail sent to the email specified above.</td>
                </tr>
                <?php if ($fanlisting->settings['is_expert'] || ($fanlisting->settings['approved'] && ($fanlisting->settings['list_type'] == 0))) { ?>
                    <tr>
                    <td><label for="s_date_format">Date Format</label></td>
                    <td><input name="s_date_format" type="text" id="s_date_format" value="<?php echo $display->output($fanlisting->settings['date_format']); ?>" size="30" maxlength="255" /></td>
                    <td>Format used to display dates. </td>
                    </tr><?php } ?>
                <?php if ($fanlisting->settings['is_expert']) { ?>
                    <tr>
                        <td><label for="s_autoinicap">Auto Inicap</label></td>
                        <td>
                            <select name="s_autoinicap" id="s_autoinicap">
                                <option value="1"<?php is_active($fanlisting->settings['autoinicap']); ?>>Yes</option>
                                <option value="0"<?php is_active(!$fanlisting->settings['autoinicap']); ?>>No</option>
                            </select>
                        </td>
                        <td>Member's names automatically get a capital 1st letter.</td>
                    </tr>
                    <tr>
                        <td><label for="s_lastx">Last X</label></td>
                        <td><input name="s_lastx" type="text" id="s_lastx" value="<?php echo $display->output($fanlisting->settings['lastx']); ?>" size="30" maxlength="3" /></td>
                        <td>Determines the amount of last updated members when using PHPFANLIST_LASTX. <span class="smaller">(Must be a number)</span></td>
                    </tr>
                    <tr>
                        <td><label for="s_show_num_newsitems">Number newsitems</label></td>
                        <td><input name="s_show_num_newsitems" type="text" id="s_show_num_newsitems" value="<?php echo $display->output($fanlisting->settings['show_num_newsitems']); ?>" size="30" maxlength="3" /></td>
                        <td>Number of last news items to display. <span class="smaller">(Must be a number)</span></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <h2>Admin Settings</h2>
            <table class="tablesettings">
                <thead>
                <tr>
                    <th scope="col" class="col_setting">Setting</th>
                    <th scope="col" class="col_settingvalue">Value</th>
                    <th scope="col" class="col_description">Description</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($fanlisting->settings['is_expert']) { ?>
                    <tr>
                        <td><label for="s_admin_name">Admin Username</label></td>
                        <td><input name="s_admin_name" type="text" id="s_admin_name" value="<?php echo $display->output($fanlisting->settings['admin_name']); ?>" size="30" maxlength="255" /></td>
                        <td>The username you use to log on to this administration section. </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label for="s_admin_pass">Admin Password</label></td>
                    <td><input name="s_admin_pass" type="text" id="s_admin_pass" value="<?php echo $display->output($fanlisting->settings['admin_pass']); ?>" size="30" maxlength="255" /></td>
                    <td>The passward you use to log on to this administration section. </td>
                </tr>
                <?php if ($fanlisting->settings['is_expert']) { ?>
                    <tr>
                        <td><label for="s_admin_pass">Cookie lifetime </label></td>
                        <td><input name="s_cookie_lifetime" type="text" id="s_cookie_lifetime" value="<?php echo $display->output($fanlisting->settings['cookie_lifetime']); ?>" size="30" maxlength="3" /></td>
                        <td>Number of days the username is remembered.</td>
                    </tr>
                    <tr>
                        <td><label for="s_show_legend">Show Legends</label></td>
                        <td>
                            <select name="s_show_legend" id="s_show_legend">
                                <option value="1"<?php is_active($fanlisting->settings['show_legend']); ?>>Yes</option>
                                <option value="0"<?php is_active(!$fanlisting->settings['show_legend']); ?>>No</option>
                            </select>
                        </td>
                        <td>Show the icon legends in the admin.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <h2>Join / Update Settings</h2>
            <table class="tablesettings">
                <thead>
                <tr>
                    <th scope="col" class="col_setting">Setting</th>
                    <th scope="col" class="col_settingvalue">Value</th>
                    <th scope="col" class="col_description">Description</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><label for="s_allow_memberdelete">Allow members to request deletes</label></td>
                    <td>
                        <select name="s_allow_memberdelete" id="s_allow_memberdelete">
                            <option value="1"<?php is_active($fanlisting->settings['allow_memberdelete']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['allow_memberdelete']); ?>>No</option>
                        </select>
                    </td>
                    <td>Allow a member to request being removed from the list.</td>
                </tr>
                <tr>
                    <td><label for="s_allow_doublemail">Allow double email addresses</label></td>
                    <td>
                        <select name="s_allow_doublemail" id="s_allow_doublemail">
                            <option value="1"<?php is_active($fanlisting->settings['allow_doublemail']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['allow_doublemail']); ?>>No</option>
                        </select>
                    </td>
                    <td>Check if an email address exists before joining/updating (helps prevent double memberships).</td>
                </tr>
                <tr>
                    <td><label for="s_spam_words">Spam words</label></td>
                    <td><input name="s_spam_words" type="text" id="s_spam_words" value="<?php echo $display->output($fanlisting->settings['spam_words']); ?>" size="30" maxlength="255" />
                    </td>
                    <td>Join and update fields are checked if the don't contain any of these words  (separate them with commas).</td>
                </tr>
                <?php if ($fanlisting->settings['is_expert']) { ?>
                    <tr>
                    <td><label for="s_allow_pluralupdate">Allow plural update </label></td>
                    <td>
                        <select name="s_allow_pluralupdate" id="s_allow_pluralupdate">
                            <option value="1"<?php is_active($fanlisting->settings['allow_pluralupdate']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['allow_pluralupdate']); ?>>No</option>
                        </select>
                    </td>
                    <td>Allow more than 1 update/delete per member.</td>
                    </tr><?php } ?>
                <tr>
                    <td><label for="s_ask_country">Ask for Country</label></td>
                    <td>
                        <select name="s_ask_country" id="s_ask_country">
                            <option value="2"<?php is_active($fanlisting->settings['ask_country'], '2'); ?>>Required</option>
                            <option value="1"<?php is_active($fanlisting->settings['ask_country'], '1'); ?>>Yes</option>
                            <option value="0"<?php is_active($fanlisting->settings['ask_country'], '0'); ?>>No</option>
                        </select>
                    </td>
                    <td>Ask for a country. <span class="smaller">(Country is automatically required for approved fanlistings/namelistings)</span></td>
                </tr>
                <tr>
                    <td><label for="s_ask_url">Ask for Website</label></td>
                    <td>
                        <select name="s_ask_url" id="s_ask_url">
                            <option value="2"<?php is_active($fanlisting->settings['ask_url'], '2'); ?>>Required</option>
                            <option value="1"<?php is_active($fanlisting->settings['ask_url'], '1'); ?>>Yes</option>
                            <option value="0"<?php is_active($fanlisting->settings['ask_url'], '0'); ?>>No</option>
                        </select>
                    </td>
                    <td>Ask for a website. <span class="smaller">(Website can't be required for approved fanlistings/namelistings)</span></td>
                </tr>
                <tr>
                    <td><label for="s_ask_custom">Ask Custom</label></td>
                    <td>
                        <select name="s_ask_custom" id="s_ask_custom">
                            <option value="2"<?php is_active($fanlisting->settings['ask_custom'], '2'); ?>>Required</option>
                            <option value="1"<?php is_active($fanlisting->settings['ask_custom'], '1'); ?>>Yes</option>
                            <option value="0"<?php is_active($fanlisting->settings['ask_custom'], '0'); ?>>No</option>
                        </select>
                    </td>
                    <td>Ask for a custom field. <span class="smaller">(The Custom field can't be required for approved fanlistings/namelistings) </span></td>
                </tr>
                <?php if ($fanlisting->settings['ask_custom'] ) { ?>
                    <tr>
                        <td><label for="s_custom_field_name">Custom field name </label></td>
                        <td><input name="s_custom_field_name" type="text" id="s_custom_field_name" value="<?php echo $display->output($fanlisting->settings['custom_field_name']); ?>" size="30" maxlength="255" /></td>
                        <td>The custom field can be used as favorite field.</td>
                    </tr>
                    <?php if ($fanlisting->settings['is_expert']) { ?>
                        <tr>
                        <td><label for="s_custom_field_format">Custom field format.</label></td>
                        <td><input name="s_custom_field_format" type="text" id="s_custom_field_format" value="<?php echo $display->output($fanlisting->settings['custom_field_format']); ?>" size="30" maxlength="255" /></td>
                        <td>Regular expression (PERL) to which the custom field must match before it's valid. <span class="smaller">(Leave empty to disable. It's also good to tell your visitors, how you want the custom field) </span></td>
                        </tr><?php } ?>
                <?php } ?>
                <tr>
                    <td><label for="s_ask_rules">Ask Rules</label></td>
                    <td>
                        <select name="s_ask_rules" id="s_ask_rules">
                            <option value="2"<?php is_active($fanlisting->settings['ask_rules'], '2'); ?>>Required</option>
                            <option value="1"<?php is_active($fanlisting->settings['ask_rules'], '1'); ?>>Yes</option>
                            <option value="0"<?php is_active($fanlisting->settings['ask_rules'], '0'); ?>>No</option>
                        </select>
                    </td>
                    <td>Ask for the correct rules answer. <span class="smaller">(Rules can't be required for approved fanlistings/namelistings)</span></td>
                </tr>
                <?php if ($fanlisting->settings['ask_rules']) { ?>
                    <tr>
                        <td><label for="s_rules_question">Rules question</label></td>
                        <td><input name="s_rules_question" type="text" id="s_rules_question" value="<?php echo $display->output($fanlisting->settings['rules_question']); ?>" size="30" maxlength="255" /></td>
                        <td>Question to pose if you want join rules (Mostly used for cliques).</td>
                    </tr>
                    <tr>
                    <td><label for="s_rules_answer">Rules Answer</label></td>
                    <td><input name="s_rules_answer" type="text" id="s_rules_answer" value="<?php echo $display->output($fanlisting->settings['rules_answer']); ?>" size="30" maxlength="255" /></td>
                    <td>The correct answer to that question. </td>
                    </tr><?php } ?>
                </tbody>
            </table>
            <h2>Memberlist Settings</h2>
            <table class="tablesettings">
                <thead>
                <tr>
                    <th scope="col" class="col_setting">Setting</th>
                    <th scope="col" class="col_settingvalue">Value</th>
                    <th scope="col" class="col_description">Description</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($fanlisting->settings['is_expert']) { ?>
                    <tr>
                    <td><label for="s_default_list_sort">Default List View </label></td>
                    <td>
                        <select name="s_default_list_sort" id="s_default_list_sort">
                            <option value="all"<?php is_active($fanlisting->settings['default_list_sort'], 'all'); ?>>Complete list</option>
                            <option value="country"<?php is_active($fanlisting->settings['default_list_sort'], 'country'); ?>>Sorted by country</option>
                        </select>
                    </td>
                    <td>Default view of the member list.</td>
                    </tr><?php } ?>
                <tr>
                    <td><label for="s_default_list_order">Default List Order </label></td>
                    <td>
                        <select name="s_default_list_order" id="s_default_list_order">
                            <option value="id"<?php is_active($fanlisting->settings['default_list_order'], 'id'); ?>>Id</option>
                            <option value="name"<?php is_active($fanlisting->settings['default_list_order'], 'name'); ?>>Name</option>
                            <option value="mail"<?php is_active($fanlisting->settings['default_list_order'], 'mail'); ?>>email</option>
                            <option value="url"<?php is_active($fanlisting->settings['default_list_order'], 'url'); ?>>Url</option>
                            <option value="country"<?php is_active($fanlisting->settings['default_list_order'], 'country'); ?>>Country</option>
                            <option value="custom"<?php is_active($fanlisting->settings['default_list_order'], 'custom'); ?>>Custom</option>
                            <option value="dateofadd DESC"<?php is_active($fanlisting->settings['default_list_order'], 'dateofadd DESC'); ?>>Join date (latest first)</option>
                        </select>
                    </td>
                    <td>Default column to order the member list by.</td>
                </tr>
                <tr>
                    <td><label for="s_show_member_id">Show Member ID</label></td>
                    <td>
                        <select name="s_show_member_id" id="s_show_member_id">
                            <option value="1"<?php is_active($fanlisting->settings['show_member_id']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['show_member_id']); ?>>No</option>
                        </select>
                    </td>
                    <td>Show member ID in the list. </td>
                </tr>
                <tr>
                    <td><label for="s_show_mail">Show Member email</label></td>
                    <td>
                        <select name="s_show_mail" id="s_show_mail">
                            <option value="1"<?php is_active($fanlisting->settings['show_mail'] == 1); ?>>Yes</option>
                            <option value="4"<?php is_active($fanlisting->settings['show_mail'] == 4); ?>>Yes (admin can override)</option>
                            <option value="2"<?php is_active($fanlisting->settings['show_mail'] == 2); ?>>No</option>
                            <option value="3"<?php is_active($fanlisting->settings['show_mail'] == 3); ?>>Member Decides</option>
                        </select>
                    </td>
                    <td>Show member email in the list.</td>
                </tr>
                <?php if ($fanlisting->settings['ask_url']) { ?>
                    <tr>
                    <td><label for="s_show_url">Show Member URL</label></td>
                    <td>
                        <select name="s_show_url" id="s_show_url">
                            <option value="1"<?php is_active($fanlisting->settings['show_url']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['show_url']); ?>>No</option>
                        </select>
                    </td>
                    <td>Show member url in the list.</td>
                    </tr><?php } ?>
                <?php if ($fanlisting->settings['ask_custom']) { ?>
                    <tr>
                    <td><label for="s_show_custom">Show Custom</label></td>
                    <td>
                        <select name="s_show_custom" id="s_show_custom">
                            <option value="1"<?php is_active($fanlisting->settings['show_custom']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['show_custom']); ?>>No</option>
                        </select>
                    </td>
                    <td>Show member custom in the list. <span class="smaller">(If there is one)</span></td>
                    </tr><?php } ?>
                <?php if ($fanlisting->settings['is_expert']) { ?><tr>
                    <td><label for="s_url_nofollow">Nofollow on URLs</label></td>
                    <td>
                        <select name="s_url_nofollow" id="s_url_nofollow">
                            <option value="1"<?php is_active($fanlisting->settings['url_nofollow']); ?>>Yes</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['url_nofollow']); ?>>No</option>
                        </select>
                    </td>
                    <td>Tells the searchengines not to follow the urls in the memberlist. <span class="smaller">(This discourages spammers)</span></td>
                    </tr><?php } ?>
                </tbody>
            </table>
            <h2>System Settings</h2>
            <table class="tablesettings">
                <thead>
                <tr>
                    <th scope="col" class="col_setting">Setting</th>
                    <th scope="col" class="col_settingvalue">Value</th>
                    <th scope="col" class="col_description">Description</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($fanlisting->settings['is_expert']) {?>
                    <tr>
                        <td><label for="s_is_xhtml">Is XHTML</label></td>
                        <td><select name="s_is_xhtml" id="s_is_xhtml">
                                <option value="1"<?php is_active($fanlisting->settings['is_xhtml']); ?>>Yes</option>
                                <option value="0"<?php is_active(!$fanlisting->settings['is_xhtml']); ?>>No</option>
                            </select></td>
                        <td>Is your listing front side (members, join, news,... pages) XHTML compliant?<br />
                            <span class="smaller">Some output is a little different when you have XHTML compliant pages. </span></td>
                    </tr>
                    <tr>
                        <td><label for="s_global_includedir">Global Includes Directory</label></td>
                        <td><input name="s_global_includedir" type="text" id="s_global_includedir" value="<?php echo $display->output($fanlisting->settings['global_includedir']); ?>" size="30" maxlength="255" />
                            <a href="http://www.phpfanlist.com/g_includes.html" class="btn external"><img alt="help" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/help-16.png" /></a></td>
                        <td>If you don't know what this is for, leave it as it is.<br />Don't forget the / at the end!!</td>
                    </tr>
                    <tr>
                        <td><label for="s_web_includedir">Web Include Directory</label></td>
                        <td><input name="s_web_includedir" type="text" id="s_web_includedir" value="<?php echo $display->output($fanlisting->settings['web_includedir']); ?>" size="30" maxlength="255" /></td>
                        <td>Path of the web_includes, absolute URL or relative path to admin.php<br />Don't forget the / at the end!!</td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label for="s_is_expert">Mode</label></td>
                    <td>
                        <select name="s_is_expert" id="s_is_expert">
                            <option value="1"<?php is_active($fanlisting->settings['is_expert']); ?>>Expert</option>
                            <option value="0"<?php is_active(!$fanlisting->settings['is_expert']); ?>>Beginner</option>
                        </select>
                    </td>
                    <td>If you're new to phpFanList it's best to keep the setting at beginner, since phpFanList will give you tips on certain more complex items.<br />
                        In expert mode some extra options will be available and beginner help items will be hidden.</td>
                </tr>
                <?php if ($fanlisting->settings['is_expert']) { ?>
                    <tr>
                        <td><label for="s_timediff">Server time difference</label></td>
                        <td><input name="s_timediff" type="text" id="s_timediff" value="<?php echo $display->output($fanlisting->settings['timediff']); ?>" size="30" maxlength="6" /></td>
                        <td>Time difference (in hours) between your time zone and your server's timezone. <span class="smaller">It's now [24h]: <?php echo date('dS F Y H:s', mktime() + ($fanlisting->settings['timediff'] *3600));?></span></td>
                    </tr>
                    <tr>
                        <td><label for="s_check_latest">Check latest version</label></td>
                        <td>
                            <select name="s_check_latest" id="s_check_latest">
                                <option value="1"<?php is_active($fanlisting->settings['check_latest']); ?>>Yes</option>
                                <option value="0"<?php is_active(!$fanlisting->settings['check_latest']); ?>>No</option>
                            </select>
                        </td>
                        <td>Check if there is a new version of phpFanList admin <span class="smaller">(checked once per session in the admin)</span>. </td>
                    </tr>
                    <tr>
                        <td><label for="s_advanced_mailcheck">Advanced email check</label></td>
                        <td>
                            <select name="s_advanced_mailcheck" id="s_advanced_mailcheck">
                                <option value="1"<?php is_active($fanlisting->settings['advanced_mailcheck']); ?>>Advanced</option>
                                <option value="0"<?php is_active(!$fanlisting->settings['advanced_mailcheck']); ?>>Normal</option>
                            </select>
                        </td>
                        <td>Advanced checks by using undisposable.org (if better, but can be slower).</td>
                    </tr>
                    <tr>
                    <td><label for="s_max_comment">Max Comment </label></td>
                    <td><input name="s_max_comment" type="text" id="s_max_comment" value="<?php echo $display->output($fanlisting->settings['max_comment']); ?>" size="30" maxlength="6" /></td>
                    <td>Maximum length of the comment field, displayed on join and update forms. <span class="smaller">(for security reasons it's best to not allow too much text)</span></td>
                    </tr><?php } ?>
                </tbody>
            </table>
            <p class="submit"><input name="submit" type="submit" value="Update" /></p>
            <p class="note">Changing these settings will take effect immediately after you click update.</p>
        </form>
    <?php } elseif ($_page == 'plugins') {?>
        <table class="tableitemcollection plugins">
            <thead>
            <tr>
                <th colspan="col" class="col_status">&nbsp;</th>
                <th colspan="col" class="col_plugin">Plugin</th>
                <th colspan="col" class="col_actions">&nbsp;</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"><?php echo count($plugins); ?> plugins listed (<?php echo count($plugins_found); ?> found / <?php echo count($plugins_registered); ?> registered).</td>
            </tr>
            <?php if ($fanlisting->settings['show_legend'] && (count($plugins) > 0)) {?>
                <tr class="legend">
                <td colspan="3">
                    <img alt="OK" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/ok-16.png" /> OK
                    <img alt="Not registered" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" /> New found
                    <img alt="Broken" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/cancel-16.png" /> Broken (not found)
                    <img alt="Add" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/add-16.png" /> Add plugin
                    <img alt="Remove" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /> Remove plugin
                </td>
                </tr><?php } ?>
            </tfoot>
            <?php if (count($plugins) > 0) { ?>
                <tbody>
                <?php foreach($plugins as $plugin) {?>
                    <tr>
                    <td><?php switch ($plugin['status']) {
                            case 2: ?><img alt="OK" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/ok-16.png" /><?php
                                break;
                            case 1: ?><img alt="Not registered" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/new-16.png" /><?php
                                break;
                            case 0: ?><img alt="Broken" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/cancel-16.png" /><?php
                                break;
                        } ?></td>
                    <td><?php echo $display->output(str_replace('_', ' ', $plugin['name'])); ?></td>
                    <td><?php switch ($plugin['status']) {
                            case 1: ?><a href="admin.php?action=plugins&amp;do=add&amp;plugin=<?php echo $display->output($plugin['name']); ?>"><img alt="Add" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/add-16.png" /></a><?php
                                break;
                            case 2:
                            case 0: ?><a href="admin.php?action=plugins&amp;do=remove&amp;plugin=<?php echo $display->output($plugin['name']); ?>"><img alt="Remove" src="<?php echo $fanlisting->settings['web_includedir']; ?>images/delete-16.png" /></a><?php
                                break;
                        } ?></td>
                    </tr><?php } ?>
                </tbody><?php } ?>
        </table>
        <p class="note">The plugins must be located in the plugin directory to be found.</p>
        <?php if (!$fanlisting->settings['is_expert']) { ?><p class="information">Download <a href="http://www.phpfanlist.com/plugins.html" class="external">more plugins</a> from the phpFanList website or learn <a href="http://www.phpfanlist.com/makeplugins.html" class="external">how to make your own</a>.</p><?php } ?>
    <?php } elseif ($_page == 'tools') {?>
        <h2>Tools</h2>
        <p><a href="admin.php?action=downloadmembers">Download members backup</a>.</p>
    <?php } else { ?>
        <h2>Welcome <?php echo $display->output($fanlisting->settings['owner_name']); ?></h2>
        <?php
        if (!$fanlisting->settings['is_expert']) {
            if (!$fanlisting->settings['show_member_id']) { ?><p class="information">Notice: <em>You have chosen to hide your member IDs. This will make it more difficult for your members to update their info.</em></p><?php }
            if (($fanlisting->settings['date_format'] != 'dS F Y') && $fanlisting->settings['approved']) { ?><p class="information">Notice: <em>You have an approved listing, but your date format is not the suggested "dS F Y".</em></p><?php }
            if (($fanlisting->settings['admin_name'] == 'admin') && ($fanlisting->settings['admin_pass'] == '')) {?>	<p class="warning"><strong>Attention</strong>: <em>The administrator username and password are still set to the default values. It is advisable that you change it.</em></p><?php }
            $setting_host = parse_url($fanlisting->settings['site_url']);
            if (($setting_host === false) || (!isset($setting_host['host'])) || (strtolower($setting_host['host']) != strtolower($_SERVER['HTTP_HOST']))) {
                if ($fanlisting->settings['ask_custom'] && ($fanlisting->settings['custom_field_name'] == '')) {?>	<p class="warning"><strong>Attention</strong>: <em>You have asking for a custom field enabled, but you don't have a value for this custom field set.</em></p><?php }
                if ($fanlisting->settings['ask_rules'] && ($fanlisting->settings['rules_question'] == '')) {?>	<p class="warning"><strong>Attention</strong>: <em>You have asking for rules enabled, but you don't have a rules question set.</em></p><?php }
                if ($fanlisting->settings['ask_rules'] && ($fanlisting->settings['rules_answer'] == '')) {?>	<p class="warning"><strong>Attention</strong>: <em>You have asking for rules enabled, but you don't have a rules answer set.</em></p><?php }
                ?>	<p class="warning"><strong>Attention</strong>: <em>The url you set for the site, doesn't match the url of the site.</em></p>
            <?php }
            if ($fanlisting->settings['ask_rules'] && ($fanlisting->settings['rules_answer'] == '')) {?>	<p class="warning"><strong>Attention</strong>: <em>You have asking for rules enabled, but you don't have a rules answer set.</em></p><?php }
            if (($fanlisting->settings['owner_mail'] == '') && ($fanlisting->settings['mail_admin'] || $fanlisting->settings['mail_on_join'] || $fanlisting->settings['mail_approve'])) { ?><p class="error"><strong>Error</strong>: <em>You haven't given an email address but you have mailing enabled!</em></p><?php }
        }
        ?>
        <h3>Your <?php echo $fanlisting->settings['list_type_name']; ?></h3>
        <p>Number of members: <?php echo $stats['member_count']; ?></p>
        <p>Queue size: <?php echo $stats['member_pending_count'] + $stats['member_update_count'] + $stats['member_delete_count']; ?> (<?php echo $stats['member_pending_count'];?> joins / <?php echo $stats['member_update_count'];?> updates / <?php echo $stats['member_delete_count'];?> deletes)</p>
        <p>Requests in queue: <?php if (isset($queue)) {
                $links = array();
                foreach ($queue as $queueitem) {
                    array_push($links, '<a href="admin.php?page=queueitem&amp;tid=' . $queueitem->tempid . '">' . $display->output(((is_null($queueitem->name) && isset($queueitem->extra['member'])) ? $queueitem->extra['member']->name : $queueitem->name) ) .'</a>');
                }
                echo implode(', ', $links);
            } ?></p>
        <h3>phpFanList</h3>
        <p>Latest phpFanList version: <?php $latest = ($fanlisting->settings['check_latest']) ? checkVersion() : false; echo ($latest === false) ? 'N/A' : (($latest > $fanlisting->settings['version']) ? '<strong>' . $latest . '</strong>' : $latest); ?></p>
        <p>Found a problem? <a class="external" href="http://www.phpfanlist.com/contact.php">Let us know</a>.</p>
    <?php } ?>
    <p id="copyright">Powered by <a class="external" href="http://www.phpfanlist.com/">phpFanList</a> v. <?php echo $fanlisting->settings['version']; if (error_reporting() == E_ALL) { echo '<span style="color: red; font-weight: bold;"> In DEBUG!</span>'; }?></p>
</div>
</body>
</html>