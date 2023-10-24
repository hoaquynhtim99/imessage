<?php

/**
 * @Project ONLINE MESSAGE 4.x
 * @Author PHAN TAN DUNG <phantandung92@gmail.com>
 * @Copyright (C) 2014 PHAN TAN DUNG. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Thu, 19 May 2016 08:50:53 GMT
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

if (!nv_function_exists('nv_block_imessage')) {
    /**
     * @param string $module
     * @param array  $data_block
     * @param array  $lang_block
     * @return string
     */
    function nv_block_config_imessage($module, $data_block, $lang_block)
    {
        global $site_mods, $db;

        $module_data = $site_mods[$module]['module_data'];
        $module_file = $site_mods[$module]['module_file'];
        include NV_ROOTDIR . '/modules/' . $module_file . '/language/' . NV_LANG_INTERFACE . '.php';

        $html = '';

        // Nhóm chat
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_module['block_group_chat'] . ':</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<select name="config_group_chat" class="form-control">';
        $html .= '<option value="0">----</option>';

        $sql = "SELECT groupid FROM " . NV_PREFIXLANG . "_" . $module_data . " WHERE is_allow=1";
        $group_allowed = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN) ?: [];
        $groups_list = nv_groups_list();

        foreach ($groups_list as $g_id => $g_title) {
            if (!in_array($g_id, $group_allowed)) {
                continue;
            }
            $html .= '<option value="' . $g_id . '"' . ($data_block['group_chat'] == $g_id ? ' selected="selected"' : '') . '>' . $g_title . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        // Kiểu hiển thị
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_module['block_type_show'] . ':</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<select name="config_type_show" class="form-control">';

        $type_shows = ['button', 'inline'];
        foreach ($type_shows as $type_show) {
            $html .= '<option value="' . $type_show . '"' . ($data_block['type_show'] == $type_show ? ' selected="selected"' : '') . '>' . $lang_module['block_type_show_' . $type_show] . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        // Mở hộp chat
        $html .= '<div class="form-group">';
        $html .= '<div class="col-sm-18 col-sm-offset-6">';
        $html .= '<div class="checkbox"><label><input type="checkbox" name="config_auto_show" value="1"' . (!empty($data_block['auto_show']) ? ' checked="checked"' : '') . '> ' . $lang_module['block_auto_show'] . '</label></div>';
        $html .= '</div>';
        $html .= '</div>';

        // Chị trí nút chat
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_module['block_align'] . ':</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<select name="config_align" class="form-control">';

        $aligns = ['right', 'left'];
        foreach ($aligns as $align) {
            $html .= '<option value="' . $align . '"' . ($data_block['align'] == $align ? ' selected="selected"' : '') . '>' . $lang_module['block_align_' . $align] . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        // Khoảng cách
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_module['block_offset_x'] . ':</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<input class="form-control" type="number" min="0" name="config_offset_x" value="' . $data_block['offset_x'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_module['block_offset_y'] . ':</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<input class="form-control" type="number" min="0" name="config_offset_y" value="' . $data_block['offset_y'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        // Chiều cao
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_module['block_box_height'] . ':</label>';
        $html .= '<div class="col-sm-9">';
        $html .= '<input class="form-control" type="number" min="0" name="config_box_height" value="' . $data_block['box_height'] . '">';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param string $module
     * @param array  $lang_block
     * @return array
     */
    function nv_block_config_imessage_submit($module, $lang_block)
    {
        global $nv_Request;

        $return = [];
        $return['error'] = [];
        $return['config'] = [];
        $return['config']['group_chat'] = $nv_Request->get_absint('config_group_chat', 'post', 0);
        $return['config']['type_show'] = $nv_Request->get_title('config_type_show', 'post', '');
        $return['config']['auto_show'] = (int) $nv_Request->get_bool('config_auto_show', 'post', false);
        $return['config']['align'] = $nv_Request->get_title('config_align', 'post', '');
        $return['config']['offset_x'] = $nv_Request->get_absint('config_offset_x', 'post', 0);
        $return['config']['offset_y'] = $nv_Request->get_absint('config_offset_y', 'post', 0);
        $return['config']['box_height'] = $nv_Request->get_absint('config_box_height', 'post', 0);

        if ($return['config']['box_height'] <= 0) {
            $return['config']['box_height'] = 455;
        }

        return $return;
    }

    /**
     * @param array $block_config
     * @return string|void
     */
    function nv_block_imessage($block_config)
    {
        global $site_mods, $global_config, $module_name, $user_info, $page_url, $client_info;

        $module = $block_config['module'];
        $module_info = $site_mods[$module];
        $module_file = $module_info['module_file'];

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_info['module_theme'] . '/block.imessage.tpl')) {
            $block_theme = $global_config['module_theme'];
        } elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $module_info['module_theme'] . '/block.imessage.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }

        include NV_ROOTDIR . '/modules/' . $module_file . '/language/' . NV_LANG_INTERFACE . '.php';

        $block_config['loginrequire'] = defined('NV_IS_USER') ? 0 : 1;
        $block_config['allowed'] = defined('NV_IS_USER') ? (nv_user_in_groups($block_config['group_chat']) ? 1 : 0) : 0;
        $block_config['loginurl'] = '';
        if (!defined('NV_IS_USER')) {
            if (!empty($page_url)) {
                $nv_redirect = $page_url;
            } elseif (strpos($client_info['selfurl'], NV_MY_DOMAIN) === 0) {
                $nv_redirect = $client_info['selfurl'];
            } else {
                $nv_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA;
            }
            $block_config['loginurl'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=login&amp;nv_redirect=' . nv_redirect_encrypt(nv_url_rewrite($nv_redirect, true));
        }
        $block_config['ajaxurl'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module . '&nocache=NOCACHEHLODER', true);
        $block_config['is_mobile'] = intval($client_info['is_mobile']);

        $xtpl = new XTemplate('block.imessage.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $module_info['module_theme']);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('CONFIG', $block_config);

        if (!defined('IMESSAGE_LOADED')) {
            // Đưa hết lang vào js 1 lần
            define('IMESSAGE_LOADED', true);
            $xtpl->assign('LANG_MODULE', json_encode($lang_module));
            $xtpl->parse('main.lang');

            // Load js và css của module 1 lần nếu block trên module khác
            if ($module_name != $module) {
                $xtpl->parse('main.js_css');
            }
        }

        if ($block_config['type_show'] == 'button') {
            $xtpl->parse('main.button');
        } else {
            $xtpl->parse('main.inline');
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_block_imessage($block_config);
}
