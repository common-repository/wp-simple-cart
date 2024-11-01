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
 * @version     svn:$Id: simple-cart-invoice.php 140016 2009-07-28 06:57:23Z tajima $
 */
require_once('../../../../wp-config.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/SimpleCartFunctions.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_model/store/SimpleCartStoreOrderModel.php');

$order_id = $_GET['order_id'];
$order_no = SimpleCartFunctions::LPAD($order_id, 8);
SimpleCartFunctions::CreateOrderPDF($order_id);
