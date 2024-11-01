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
 * @version     svn:$Id: simple-cart-review.php 140016 2009-07-28 06:57:23Z tajima $
 */
require_once('../../../../wp-config.php');
require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_model/public/SimpleCartPublicModel.php');

switch ($_POST['action']) {
    case 'save'://“o˜^
        $params = array();
        $params['store_id']     = $_POST['store_id'];
        $params['product_id']   = $_POST['product_id'];
        $params['recommend']    = $_POST['recommend'];
        $params['comment']      = $_POST['comment'];
        $result = SimpleCartPublicModel::saveReview($params);
        break;
    case 'get'://Žæ“¾
        $params = array();
        $params['store_id']     = $_POST['store_id'];
        $params['product_id']   = $_POST['product_id'];
        $result = SimpleCartPublicModel::getReviewList($params);
        break;
    default:
        break;
}
echo json_encode($result);

