<?php

/**
 * @Project ONLINE MESSAGE 4.x
 * @Author PHAN TAN DUNG <phantandung92@gmail.com>
 * @Copyright (C) 2014 PHAN TAN DUNG. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Thu, 19 May 2016 08:50:53 GMT
 */

if (!defined('NV_SYSTEM')) {
    die('Stop!!!');
}

define('NV_IS_MOD_CHAT', true);

require NV_ROOTDIR . '/modules/' . $module_file . '/emotions.php';

// Cấu hình được phép chat
$config_allow = [];
$sql = "SELECT groupid FROM " . NV_PREFIXLANG . "_" . $module_data . " WHERE is_allow=1";
$list = $nv_Cache->db($sql, 'groupid', $module_name);
if (!empty($list)) {
    foreach ($list as $row) {
        $config_allow[$row['groupid']] = $row['groupid'];
    }
}
unset($list);
