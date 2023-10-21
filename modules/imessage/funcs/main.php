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

if ($nv_Request->get_title('tokend', 'post', '') === NV_CHECK_SESSION) {
    $array_data = [
        'allowed' => 1,
        'loginrequire' => 0,
    ];
    $gid = $nv_Request->get_absint('gid', 'post', 0);

    // Phải đăng nhập
    if (!defined('NV_IS_USER')) {
        $array_data['loginrequire'] = 1;
        nv_jsonOutput($array_data);
    }
    // Không có quyền chat
    if (!isset($config_allow[$gid]) or !nv_user_in_groups($gid)) {
        $array_data['allowed'] = 0;
        nv_jsonOutput($array_data);
    }
    $message = nv_nl2br(nv_htmlspecialchars(trim(strip_tags($nv_Request->get_string('message', 'post', '', true, false)))));
    $message = m_emotions_replace($message);

    $array_data['message'] = $message;

    nv_jsonOutput($array_data);
}

$url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA;
nv_redirect_location($url);
