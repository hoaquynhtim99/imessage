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

/**
 * @return
 */
function m_emotions_array()
{
    // @formatter:off
    return [
        6 => '>:D<',		18 => '#:-S',				36 => '<:-P',		 42 => ':-SS',
        48 => '<):)',		50 => '3:-O',				51 => ':(|)',		 53 => '@};-',
        55 => '**==',		56 => '(~~)',				58 => '*-:)',		 63 => '[-O<',
        67 => ':)>-',		77 => '^:)^',				106 => ':-??',		 25 => 'O:)',
        26 => ':-B',		28 => 'I-)',				29 => '8-|',		 30 => 'L-)',
        31 => ':-&',		32 => ':-$',				33 => '[-(',		 34 => ':O)',
        35 => '8-}',		7 => ':-/',					37 => '(:|',		 38 => '=P~',
        39 => ':-?',		40 => '#-O',				41 => '=D>',		 9 => ':">',
        43 => '@-)',		44 => ':^O',				45 => ':-W',		 46 => ':-<',
        47 => '>:P',		11 => [':*',':-*'],	        49 => ':@)',		 12 => '=((',
        13 => ':-O',		52 => '~:>',				16 => 'B-)',		 54 => '%%-',
        17 => ':-S',		5 => ';;)',					57 => '~O)',		 19 => '>:)',
        59 => '8-X',		60 => '=:)',				61 => '>-)',		 62 => ':-L',
        20 => ':((',		64 => '$-)',				65 => ':-"',		 66 => 'B-(',
        21 => ':))',		68 => '[-X',				69 => '\:D/',		 70 => '>:/',
        71 => ';))',		72 => 'O->',				73 => 'O=>',		 74 => 'O-+',
        75 => '(%)',		76 => ':-@',				23 => '/:)',		 78 => ':-J',
        79 => '(*)',		100 => ':)]',				101 => ':-C',		 102 => '~X(',
        103 => ':-H',		104 => ':-T',				105 => '8->',		 24 => '=))',
        107 => '%-(',		108 => ':O3',				1 => [':)',':-)'],   2 => [':(',':-('],
        3 => [';)',';-)'],	22 => [':|',':-|'],		    14 => ['X(','X-('],	 15 => [':>',':->'],
        8 => [':X',':-X'],	4 => [':D',':-D'],		    27 => '=;',		     10 => [':P',':-P'],
    ];
    // @formatter:on
}

/**
 *
 * @param mixed $data
 * @return
 */
function m_emotions_replace($data)
{
    global $module_name, $module_file, $module_info;

    $emotions = m_emotions_array();
    foreach ($emotions as $a => $b) {
        $x = [];
        if (is_array($b)) {
            for ($i = 0; $i < count($b); $i++) {
                $b[$i] = m_htmlchars($b[$i]);
                $x[] = $b[$i];
                $v = strtolower($b[$i]);
                if ($v != $b[$i]) {
                    $x[] = $v;
                }
            }
        } else {
            $b = m_htmlchars($b);
            $x[] = $b;
            $v = strtolower($b);
            if ($v != $b) {
                $x[] = $v;
            }
        }
        $p = '';
        for ($u = 0; $u < strlen($x[0]); $u++) {
            $ord = ord($x[0][$u]);
            if ($ord < 65 && $ord > 90) {
                $p .= '&#' . $ord . ';';
            } else {
                $p .= $x[0][$u];
            }
        }

        $data = str_replace($x, '<img title="' . nv_htmlspecialchars($p) . '" style="vertical-align:middle" src="' . NV_BASE_SITEURL . 'modules/' . $module_file . '/imessage/emoticons/yahoo/' . $a . '.gif" />', $data);
    }
    return $data;
}

/**
 *
 * @param mixed $str
 * @return
 */
function m_htmlchars($str)
{
    // @formatter:off
    return str_replace(
        ['&', '<', '>', '"', chr(92), chr(39)],
        ['&amp;', '&lt;', '&gt;', '&quot;', '&#92;', '&#39'],
        $str
    );
    // @formatter:on
}
