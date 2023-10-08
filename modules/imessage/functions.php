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

/**
 * Tính toán thời gian đẹp
 *
 * @param mixed $time
 * @return
 */
function nv_time_type($time)
{
    global $lang_module;

    $timeout = NV_CURRENTTIME - $time;
    if ($timeout > 86400) {
        $time = nv_date('H:i d/m/Y', $time);
    } elseif ($timeout > 3600) {
        $timeout = (int)($timeout / 3600);
        $time = sprintf($lang_module['hago'], $timeout);
    } elseif ($timeout > 60) {
        $timeout = (int)($timeout / 60);
        $time = sprintf($lang_module['mago'], $timeout);
    } elseif ($timeout > 10) {
        $time = sprintf($lang_module['sago'], $timeout);
    } else {
        $time = $lang_module['atamoment'];
    }
    return $time;
}

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

if (!function_exists('nv_groups_list')) {
    /**
     * @param string $mod_data
     * @return array
     */
    function nv_groups_list($mod_data = 'users')
    {
        global $nv_Cache;
        $cache_file = NV_LANG_DATA . '_groups_list_' . NV_CACHE_PREFIX . '.cache';
        if (($cache = $nv_Cache->getItem($mod_data, $cache_file)) != false) {
            return unserialize($cache);
        }
        global $db, $db_config, $global_config, $lang_global;

        $groups = [];
        $_mod_table = ($mod_data == 'users') ? NV_USERS_GLOBALTABLE : $db_config['prefix'] . '_' . $mod_data;
        $result = $db->query('SELECT g.group_id, d.title, g.idsite FROM ' . $_mod_table . '_groups AS g LEFT JOIN ' . $_mod_table . "_groups_detail d ON ( g.group_id = d.group_id AND d.lang='" . NV_LANG_DATA . "' ) WHERE (g.idsite = " . $global_config['idsite'] . ' OR (g.idsite =0 AND g.siteus = 1)) ORDER BY g.idsite, g.weight');
        while ($row = $result->fetch()) {
            if ($row['group_id'] < 9) {
                $row['title'] = $lang_global['level' . $row['group_id']];
            }
            $groups[$row['group_id']] = ($global_config['idsite'] > 0 and empty($row['idsite'])) ? '<strong>' . $row['title'] . '</strong>' : $row['title'];
        }
        $nv_Cache->setItem($mod_data, $cache_file, serialize($groups));

        return $groups;
    }
}
