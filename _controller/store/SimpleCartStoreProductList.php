<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Product List Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreProductList.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreProductList extends SimpleCart {

    /**
     * 店舗会員：商品一覧
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

        global $bp;

        $params = array();
        $params['s_product_cd']   = $this->request->getParam('s_product_cd');
        $params['s_product_name'] = $this->request->getParam('s_product_name');
        $params['s_categorys']    = $this->request->getParam('s_categorys');
        $params['s_publish']      = $this->request->getParam('s_publish');
        $params['s_stock']        = $this->request->getParam('s_stock');

        $pager_id = $this->request->getParam('pager_id');
        $params['pager_id'] = 1;
        if (!is_null($pager_id) && $pager_id!='') {
            $params['pager_id'] = $pager_id;
        }

        $this->model['categorys'] = SimpleCartStoreProductModel::getCategorys();
        $this->model['product_search'] = $params;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //商品検索
            case 'search_product':
                break;
            //商品削除
            case 'delete_product':
                $products = $this->request->getParam('products');
                SimpleCartStoreProductModel::deleteProducts($products);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_store->slug,
                    'bp-slug-sub'  => SC_BPID_PRODUCT_LIST,
                    'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_STORE_PRODUCT_LIST
                );
                $this->redirect($r);
                break;
            //
            default:
                break;
        }

        $this->model['product_count'] = SimpleCartStoreProductModel::getProductList($params, true);
        $this->model['product_list'] = SimpleCartStoreProductModel::getProductList($params);
        $this->exec('store/product_list');
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_PRODUCT_LIST, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_PRODUCT_LIST);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_PRODUCT_LIST_SEARCH, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
