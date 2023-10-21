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
        'data' => []
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

    // &#91;ding&#93; => [ding]
    $message = nv_nl2br(nv_htmlspecialchars(trim(strip_tags($nv_Request->get_string('message', 'post', '', true, false)))));
    $message = m_emotions_replace($message);

    /*
     * Đọc dữ liệu chat cũ. Lưu tối đa 200 bản thi, quá đó thì cứ xóa đi cái cũ nhất
     */
    $chat_file = NV_ROOTDIR . '/modules/' . $module_file . '/data/data_' . NV_LANG_DATA . '_' . $gid . '.dat';
    if (file_exists($chat_file)) {
        $array_data['data'] = json_decode(file_get_contents($chat_file), true);
    }

    // Ghi thêm vào file
    if (!empty($message)) {
        $rows = [
            'photo' => empty($user_info['avata']) ? (NV_BASE_SITEURL . 'themes/default/images/' . $module_file . '/d-avatar.gif') : $user_info['avata'],
            'name' => $user_info['full_name'],
            'time' => NV_CURRENTTIME,
            'chat' => $message == '&#91;ding&#93;' ? '<span class="ding">[ding]</span>' : $message,
            'uniqid' => uniqid('', true),
            'userid' => $user_info['userid'],
        ];
        $array_data['data'][] = $rows;
    }

    if (sizeof($array_data['data']) > 200) {
        unset($array_data['data'][0]);
        $array_data['data'] = array_values($array_data['data']);
    }
    file_put_contents($chat_file, json_encode($array_data['data']), LOCK_EX);

    // Tính lại để show thời gian
    foreach ($array_data['data'] as $key => $chat) {
        $array_data['data'][$key]['time_show'] = nv_time_type($chat['time']);
        $array_data['data'][$key]['self'] = $user_info['userid'] == $chat['userid'];
    }

    nv_jsonOutput($array_data);
}

$url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA;
nv_redirect_location($url);
