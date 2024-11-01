<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Info Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartPublicInfo.php 143340 2009-08-06 11:47:12Z tajima $
 */

class SimpleCartPublicInfo extends SimpleCart {

    /**
     * 全体公開：トップ
     *
     */
    function execute() {

        //カテゴリー情報取得
        $this->model['s_categorys'] = SimpleCartPublicModel::getCategorys();
        //店舗情報取得
        $this->model['s_stores'] = SimpleCartPublicModel::getStores();

        //店舗毎の登録製品数算出（カテゴリーはSimpleCartFunctions内にて）
        if (is_array($this->model['s_stores'])) {
            foreach ($this->model['s_stores'] as $store) {
                $this->model['count'][$store['ID']] = SimpleCartPublicModel::getProductList(array('s_store'=>$store['ID']), true);
            }
        }
        $this->exec('public/top');
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
        <h2><?php _e(SCLNG_PUBLIC_PRODUCT_INFO, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
