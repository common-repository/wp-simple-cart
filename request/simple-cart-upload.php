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
 * @version     svn:$Id: simple-cart-upload.php 140359 2009-07-29 09:13:26Z tajima $
 */
require_once('../../../../wp-config.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/SimpleCartFunctions.php');

global $current_user, $wpdb;

//ユーザーディレクトリ作成
$user_dir = SimpleCartFunctions::TemporaryDir($_GET['prefix']);

if (isset($_GET['file'])) {
    $upload_file = explode('.', $_FILES['userfile']['name']);
    $file_name = $_GET['file'] . '.' . $upload_file[count($upload_file)-1];
}
else {
    $file_name = $_FILES['userfile']['name'];
}

//ファイルアップロード
$uploaddir = $user_dir . '/';
$uploadfile = $uploaddir . basename($file_name);
move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);

//ファイル名を返す
$rand = SimpleCartFunctions::Rand8();
@header("Content-Type: text/html; charset=UTF-8");
echo $file_name . '?' . $rand;
