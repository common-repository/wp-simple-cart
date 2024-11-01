<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Info Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreInfo.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreInfo extends SimpleCart {

    /**
     * 店舗会員：トップページ
     *
     */
    function execute() {
        //roleの確認
        $u = new WP_User($this->user_ID);
        if (!$u->has_cap(SC_CAP)) {
            if ($this->mode!=SC_MODE_BP) {
                $this->roleError();
            }
            return;
        }

        global $sc_status;

        //3ヶ月前を取得
        $tm = time();
        $yy = date('Y', $tm);
        $mm = date('m', $tm);

        $params = array();
        $params['ymd'] = date('Y-m-d', mktime(0,0,0,($mm-3),1,$yy));
        $params['yymm00'] = date('Y-m-d', mktime(0,0,0,($mm-0),1,$yy));
        $params['yymm01'] = date('Y-m-d', mktime(0,0,0,($mm-1),1,$yy));
        $params['yymm02'] = date('Y-m-d', mktime(0,0,0,($mm-2),1,$yy));
        $params['yymm03'] = date('Y-m-d', mktime(0,0,0,($mm-3),1,$yy));

        $this->model['status'] = $sc_status;
        $this->model['ymd_info'] = $params;
        $this->model['order_list'] = SimpleCartStoreInfoModel::getOrderList($params);
        $this->model['sales_list'] = SimpleCartStoreInfoModel::getSalesList($params);
        $this->model['stock_list'] = SimpleCartStoreInfoModel::getStockList($params);
        $this->exec('store/top');
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_INFO);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_INFO_SUMARRY, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
