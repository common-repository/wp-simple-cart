<?php
/*
 * This file is part of Teniteo.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * デバッグ用クラス
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     Teniteo
 * @version     svn:$Id: SimpleCartLogger.php 140016 2009-07-28 06:57:23Z tajima $
 */

define('WP_LOGGER_DIR', WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files');
define('WP_LOGGER_MAX_SIZE', 5242880); //1024*1024*5
define('WP_LOGGER_MAX_ROTATION', 10);

/**
 * trace logging
 */
function sc_traceLog($message) {
    sc_pringLog('trace.log', sc_constructLogLine($message));
}

/**
 * logging formatter
 */
function sc_constructLogLine($message) {
    $ip = getEnv('REMOTE_ADDR');
    $ip = ($ip=='')?'none':"$ip";
    $date = date('Y/m/d H:i:s', time());
    if (is_object($message)||is_array($message)) {
        $message = var_export($message, true);
    }
    return "[$date][$ip]$message";
}

function sc_pringLog($file, $str) {
    $path = WP_LOGGER_DIR . '/' . $file;
    $fp = fopen($path, "a+");
    fwrite($fp, $str . "\n");
    fclose($fp);
    sc_rotationLog($path);
}

function sc_rotationLog($path) {
    $dirname = dirname($path);
    $basename = basename($path);

    // ファイルが最大サイズを超えていないかチェック
    if (filesize($path) > WP_LOGGER_MAX_SIZE) {
        if ($dh = opendir($dirname)) {
            while (($file = readdir($dh)) !== false) {
                if (ereg("^". $basename . "\." , $file)) {
                    $arrLog[] = $file;
                }
            }
            // ファイルログが最大個数なら以上なら古いファイルから削除する
            $count = count($arrLog);
            if ($count >= WP_LOGGER_MAX_ROTATION) {
                $diff = $count - WP_LOGGER_MAX_ROTATION;
                for ($i=0; $diff >= $i; $i++) {
                    unlink($dirname. "/" .array_pop($arrLog));
                }
            }
            // ログファイルの添え字をずらす
            $count = count($arrLog);
            for ($i=$count; 1 <= $i; $i--) {
                $move_number = $i + 1;
                if (file_exists("$path.$move_number")) {
                    unlink("$path.$move_number");
                }
                copy("$dirname/" . $arrLog[$i - 1], "$path.$move_number");
            }
            $ret = copy($path, "$path.1");
            if($ret) {
                unlink($path);
                touch($path);
            }
        }
    }
}
