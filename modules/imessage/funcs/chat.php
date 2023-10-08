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

// Lay nhom chat
$gID = 0;
if (isset($array_op[1])) {
    $gID = (int)$array_op[1];
}
if (empty($gID)) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiem tra thanh vien
if (!defined('NV_IS_USER')) {
    Header("Location: " . nv_url_rewrite(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true));
    exit();
}

// Nhom duoc chat
$group_allow = array();
$check_group = nv_user_groups(implode(',', $user_info['in_groups']));
$group_allow = array_intersect($check_group, $config_allow);

// Quyen chat
if (!in_array($gID, $group_allow)) {
    Header("Location: " . nv_url_rewrite(NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true));
    exit();
}
unset($group_allow, $check_group, $user_group);

require_once NV_ROOTDIR . "/modules/" . $module_file . '/emotions.php';

// Json Out
/* array(
*	0 => <ding>
*  1 => Info
*	2 => array(
*			array(
*				0 => Avatar
*				1 => Name
*				2 => Time
*				3 => Content
*				4 => NV_CURRENTTIME
*			), ...
*		)
*	)
*/

// Cap nhat thong tin chat
if ($nv_Request->isset_request('updatechat', 'post,get')) {
    $maxtime = $nv_Request->get_int('maxtime', 'post,get', 0);

    $data = file_get_contents(NV_ROOTDIR . "/modules/" . $module_file . "/data/data_" . NV_LANG_DATA . "_" . $gID . ".dat");
    $data = explode("\n", $data);
    $data = array_filter($data);

    $num = sizeof($data);
    $array = array();

    $_maxtime = $maxtime;
    $is_ding = 0;
    for ($i = $num - 1; $i >= 0; --$i) {
        $tmp = explode("<*|*>", $data[$i]);
        if (intval($tmp[3]) <= $maxtime or $tmp[0] == $user_info['userid'])
            break;

        if (intval($tmp[3]) > $_maxtime and $tmp[0] != $user_info['userid'])
            $_maxtime = intval($tmp[3]);

        if ($tmp[4] == '[ding]')
            $is_ding = 1;

        $array[] = array(
            0 => empty($tmp[1]) ? NV_BASE_SITEURL . "modules/" . $module_file . "/imessage/images/d-avatar.gif" : $tmp[1],
            1 => $tmp[2],
            2 => nv_time_type($tmp[3]),
            3 => ($tmp[4] == '[ding]') ? "<span class=\"ding\">" . $tmp[4] . "</span>" : $tmp[4],
            4 => $tmp[3]
        );
    }

    $return_array = array(
        0 => $_maxtime,
        1 => array(
            0 => !empty($array) ? (($is_ding == 1) ? 1 : 2) : 0,
            1 => "",
            2 => $array
        )
    );

    echo json_encode($return_array);
    die();
}

// Post chat
if ($nv_Request->isset_request('getjson', 'post,get')) {
    $content = m_emotions_replace($nv_Request->get_string('data', 'post,get', ':('));

    if (!defined('NV_IS_USER')) {
        $arr = array(
            0 => 0,
            1 => $lang_module['error_login'],
            2 => array()
        );
    } else {
        $arr = array(
            0 => ($content == "[ding]") ? 1 : 0,
            1 => "",
            2 => array(
                array(
                    0 => empty($user_info['photo']) ? NV_BASE_SITEURL . "modules/" . $module_file . "/imessage/images/d-avatar.gif" : NV_BASE_SITEURL . $user_info['photo'],
                    1 => empty($user_info['full_name']) ? $user_info['username'] : $user_info['full_name'],
                    2 => nv_time_type(NV_CURRENTTIME),
                    3 => ($content == '[ding]') ? "<span class=\"ding\">" . $content . "</span>" : $content,
                    4 => NV_CURRENTTIME
                )
            )
        );

        $data = file_get_contents(NV_ROOTDIR . "/modules/" . $module_file . "/data/data_" . NV_LANG_DATA . "_" . $gID . ".dat");
        $data = explode("\n", $data);
        $data = array_filter($data);

        $data[] = $user_info['userid'] . "<*|*>" . $arr[2][0][0] . "<*|*>" . $arr[2][0][1] . "<*|*>" . NV_CURRENTTIME . "<*|*>" . $content;

        if (sizeof($data) >= 101)
            unset($data[0]);

        $data = implode("\n", $data);

        file_put_contents(NV_ROOTDIR . "/modules/" . $module_file . "/data/data_" . NV_LANG_DATA . "_" . $gID . ".dat", $data, LOCK_EX);
    }

    echo json_encode($arr);
    die();
}

// Cap nhat thong tin chat
if ($nv_Request->isset_request('undateonline', 'get')) {
    list($online) = $db->query("SELECT COUNT(*) FROM " . NV_SESSIONS_GLOBALTABLE . " WHERE onl_time >= " . (NV_CURRENTTIME - NV_ONLINE_UPD_TIME) . " AND userid!=0")->fetchColumn();
    die((string )$online);
}

$height = $nv_Request->get_int('chat_height_' . $module_data, 'session', 250);
if (!$height)
    die("Error Access!!!");

$xtpl = new XTemplate("main.tpl", NV_ROOTDIR . "/modules/" . $module_file . "/imessage");
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATAURL', NV_BASE_SITEURL . "modules/" . $module_file . "/imessage/");
$xtpl->assign('SERVERURL', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op . "/" . $gID . "&");
$xtpl->assign('TOKEN', md5($global_config['sitekey'] . session_id()));
$xtpl->assign('HEIGHT', $height);
$xtpl->assign('GROUP_ID', $gID);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('USERNAME', empty($user_info['full_name']) ? $user_info['username'] : $user_info['full_name']);
$xtpl->assign('EDITINFO', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=editinfo");
$online = $db->query("SELECT COUNT(*) FROM " . NV_SESSIONS_GLOBALTABLE . " WHERE onl_time >= " . (NV_CURRENTTIME - NV_ONLINE_UPD_TIME) . " AND userid!=0")->fetchColumn();
$xtpl->assign('ONLINE', $online);

$data = file_get_contents(NV_ROOTDIR . "/modules/" . $module_file . "/data/data_" . NV_LANG_DATA . "_" . $gID . ".dat");
$data = explode("\n", $data);
$data = array_filter($data);

$num = sizeof($data);
$xtpl->assign('NUMITEM', $num);
$xtpl->assign('ITEMBG', $num % 2 ? 0 : 1);

for ($i = $num - 1; $i >= 0; --$i) {
    $tmp = explode("<*|*>", $data[$i]);
    $tmp[5] = $tmp[3];
    $tmp[3] = nv_time_type($tmp[3]);
    $tmp[4] = ($tmp[4] == '[ding]') ? "<span class=\"ding\">" . $tmp[4] . "</span>" : $tmp[4];
    $xtpl->assign('DATA', $tmp);
    $xtpl->assign('BG', ($i % 2 == 0) ? " bg" : "");
    $xtpl->assign('ID', $i + 1);
    $xtpl->parse('main.loop');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include (NV_ROOTDIR . "/includes/header.php");
echo nv_site_theme($contents, false);
include (NV_ROOTDIR . "/includes/footer.php");
