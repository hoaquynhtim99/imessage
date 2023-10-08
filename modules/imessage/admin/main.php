<?php

/**
 * @Project ONLINE MESSAGE 4.x
 * @Author PHAN TAN DUNG <phantandung92@gmail.com>
 * @Copyright (C) 2014 PHAN TAN DUNG. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Thu, 19 May 2016 08:50:53 GMT
 */

if (!defined('NV_IS_IMESSAGE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['config'];
$groups_list = nv_groups_list();
$array = [];

if ($nv_Request->isset_request('submit', 'post')) {
    $array['group'] = $nv_Request->get_typed_array('group', 'post', 'int');

    $db->query("TRUNCATE " . NV_PREFIXLANG . "_" . $module_data);

    foreach ($groups_list as $groupID => $groupName) {
        $db->query("INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . " (groupid, is_allow) VALUES (" . $groupID . ", " . (in_array($groupID, $array['group']) ? 1 : 0) . ")");
    }

    $nv_Cache->delMod($module_name);

    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
}

$sql = "SELECT groupid FROM " . NV_PREFIXLANG . "_" . $module_data . " WHERE is_allow=1";
$result = $db->query($sql);

while (list ($groupid) = $result->fetch(PDO::FETCH_NUM)) {
    $array[] = $groupid;
}

$xtpl = new XTemplate('config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name);
$xtpl->assign('LANG', $lang_module);

if (empty($groups_list)) {
    $xtpl->parse('main.empty');
} else {
    foreach ($groups_list as $groupID => $groupName) {
        $xtpl->assign('ID', $groupID);
        $xtpl->assign('CHECKED', in_array($groupID, $array) ? ' checked="checked"' : '');
        $xtpl->assign('NAME', $groupName);

        $xtpl->parse('main.data.group');
    }

    $xtpl->parse('main.data');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include (NV_ROOTDIR . '/includes/header.php');
echo nv_admin_theme($contents);
include (NV_ROOTDIR . '/includes/footer.php');
