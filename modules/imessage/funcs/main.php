<?php

/**
 * @Project ONLINE MESSAGE 4.x
 * @Author PHAN TAN DUNG <phantandung92@gmail.com>
 * @Copyright (C) 2014 PHAN TAN DUNG. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Thu, 19 May 2016 08:50:53 GMT
 */

if (!defined('NV_IS_MOD_CHAT')) {
    die('Stop!!!');
}

$page_title = $module_info['site_title'];

// Dang nhap
// FIXME
if (!defined('NV_IS_USER')) {
    $xtpl = new XTemplate("login.tpl", NV_ROOTDIR . "/modules/" . $module_file . "/imessage");
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('DATAURL', NV_BASE_SITEURL . "modules/" . $module_file . "/imessage/");
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('BASEPOST', NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&login=1&token=" . md5($global_config['sitekey'] . session_id()));
    $xtpl->assign('REGISTERURL', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=register");

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include (NV_ROOTDIR . "/includes/header.php");
    echo nv_site_theme($contents, false);
    include (NV_ROOTDIR . "/includes/footer.php");
}

// Xác định nhóm được chat
$group_allow = [];
$check_group = nv_user_groups(implode(',', $user_info['in_groups']));
$group_allow = array_intersect($check_group, $config_allow);

// Kiểm tra quyền chat
if (empty($group_allow)) {
    $xtpl = new XTemplate('info.tpl', NV_ROOTDIR . '/modules/' . $module_file . '/imessage');
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('DATAURL', NV_BASE_SITEURL . 'modules/' . $module_file . '/imessage/');

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include (NV_ROOTDIR . '/includes/header.php');
    echo nv_site_theme($contents, false);
    include (NV_ROOTDIR . '/includes/footer.php');
}

// Lấy chiều cao khung chat
$height = 250;
if (isset($array_op[0])) {
    $height = (int) $array_op[0];
}
$nv_Request->set_Session('chat_height_' . $module_data, $height);

// Chuyển trang nếu chỉ có 1 nhóm chat
if (sizeof($group_allow) == 1) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=chat/' . $group_allow[0]);
}

// Tất cả các nhóm
$groups = nv_groups_list();

$xtpl = new XTemplate("select.tpl", NV_ROOTDIR . "/modules/" . $module_file . "/imessage");
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATAURL', NV_BASE_SITEURL . "modules/" . $module_file . "/imessage/");
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

foreach ($group_allow as $groupID) {
    $xtpl->assign('TITLE', $groups[$groupID]);
    $xtpl->assign('URL', nv_url_rewrite(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=chat/" . $groupID));
    $xtpl->parse('main.group');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include (NV_ROOTDIR . "/includes/header.php");
echo nv_site_theme($contents, false);
include (NV_ROOTDIR . "/includes/footer.php");
