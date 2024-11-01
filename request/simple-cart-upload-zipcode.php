<?php
/*
 * This file is part of wp-simple-cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Image file upload
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     wp-simple-cart
 * @version     svn:$Id: simple-cart-upload-zipcode.php 140016 2009-07-28 06:57:23Z tajima $
 */
require_once('../../../../wp-config.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/SimpleCartFunctions.php');

//処理結果コード
define('SC_RESULT_UPLOAD_SUCCESS',                 '0');                       //成功

define('SC_RESULT_UPLOAD_ERROR',                   '-1');                      //失敗
define('SC_RESULT_UPLOAD_ERROR_UPLOAD',            '-2');                      //失敗:アップロード
define('SC_RESULT_UPLOAD_ERROR_FILE_OPEN',         '-20');                     //失敗:ファイルオープン
define('SC_RESULT_UPLOAD_ERROR_FILE_READ',         '-21');                     //失敗:ファイル読み込み
define('SC_RESULT_UPLOAD_ERROR_UNKNOWN',           '-99');                     //失敗:予期せぬエラー

global $current_user, $wpdb;

$result = array();
//ファイルの取得
if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
    //アップロード失敗
    $result['r_code']   = SC_RESULT_UPLOAD_ERROR_UPLOAD;
    $result['message']  = SCLNG_ZIP_IMPORT_UPLOAD_ERROR;
    break;
}
//ファイルの読み込み
$fp = @fopen($_FILES['userfile']['tmp_name'], "r");
if (!$fp) {
    //ファイルの読み込み失敗
    $result['r_code']   = SC_RESULT_UPLOAD_ERROR_FILE_OPEN;
    $result['message']  = SCLNG_ZIP_IMPORT_FILE_OPEN_ERROR;
    break;
}
else {
    //郵便番号テーブルから郵便番号を全件削除
    SimpleCartAdminModel::truncateZip();

    $count = 0;
    while (!feof($fp)) {
        $tmp_contents = fgets($fp);
        $contents = @mb_convert_encoding($tmp_contents, "UTF-8", "UTF-8, SJIS, SJIS-win");
        //カンマで分割
        $arrCSV = preg_split('/ *, */', $contents);
        $params = array();
        if (count($arrCSV)==15) {
            $count++;
            $params['zip'] = sc_trim_zip_column($arrCSV[2]);
            $params['address'] = null;
            $params['street'] = sc_trim_zip_column($arrCSV[8]);
            $params['city'] = sc_trim_zip_column($arrCSV[7]);
            $params['state'] = sc_trim_zip_column($arrCSV[6]);
            $params['country'] = null;
            SimpleCartAdminModel::saveZip($params);
        }
    }
    if ($count > 0) {
        $result['r_code'] = SC_RESULT_UPLOAD_SUCCESS;
        $result['message']  = sprintf(SCLNG_ZIP_IMPORT_SAVE_COMPLETE, $count);
    }
    else {
        $result['r_code'] = SC_RESULT_UPLOAD_ERROR;
        $result['message']  = SCLNG_ZIP_IMPORT_SAVE_NO_DATA;
    }
}
echo json_encode($result);

function sc_trim_zip_column($column) {
    $del_charactors = "\"'";
    return trim($column, $del_charactors);
}
