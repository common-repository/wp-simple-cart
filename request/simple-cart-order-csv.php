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
 * @version     svn:$Id: simple-cart-order-csv.php 140016 2009-07-28 06:57:23Z tajima $
 */
require_once('../../../../wp-config.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/SimpleCartFunctions.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_model/store/SimpleCartStoreOrderModel.php');

global $sc_csv_order;

$params = array();
$params['s_order_status'] = $_REQUEST['s_order_status'];
$params['s_order_no']  = $_REQUEST['s_order_no'];
$params['s_order_fyy'] = $_REQUEST['s_order_fyy'];
$params['s_order_fmm'] = $_REQUEST['s_order_fmm'];
$params['s_order_fdd'] = $_REQUEST['s_order_fdd'];
$params['s_order_tyy'] = $_REQUEST['s_order_tyy'];
$params['s_order_tmm'] = $_REQUEST['s_order_tmm'];
$params['s_order_tdd'] = $_REQUEST['s_order_tdd'];
$model = new SimpleCartStoreOrderModel();
$order_list = $model->getOrderList($params);

$contents = array();

//ヘッダー行出力
$cols = array();
foreach ($sc_csv_order as $key=>$val) {
    $cols[] = '"'.$val.'"';
}
$contents[] = @implode(",", $cols);

//データ行出力
foreach ($order_list as $order) {
    $cols = array();
    foreach ($sc_csv_order as $key=>$val) {
        $cols[] = '"'.$order[$key].'"';
    }
    $contents[] = @implode(",", $cols);
}

$file = 'order_'.date('YmdHis', time()).'.csv';
$contents = @implode(chr(10), $contents);
header("Content-Disposition: attachment; filename=\"" . mb_convert_encoding(basename($file), $enc) . "\"");
header('Content-Length: ' . strlen($contents));
header('Content-Type: application/octet-stream');
print(mb_convert_encoding($contents, "SHIFT-JIS","UTF-8"));
