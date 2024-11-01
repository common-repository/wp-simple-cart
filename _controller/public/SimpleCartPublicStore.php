<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Store Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartPublicStore.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartPublicStore extends SimpleCart {

    /**
     * 全体公開：店舗情報
     *
     */
    function execute() {
        $params = array();
        $params['store_id'] = $this->request->getParam('store_id');
        $this->model['store'] = SimpleCartPublicModel::getStore($params);
        $this->exec('public/store_info');
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_PUBLIC_STORE_INFO, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
