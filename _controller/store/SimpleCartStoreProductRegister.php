<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Product Register Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreProductRegister.php 147388 2009-08-20 07:23:33Z tajima $
 */

class SimpleCartStoreProductRegister extends SimpleCart {

    /**
     * 店舗会員：商品管理
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

        //前画面のパラメータを引継ぎ
        $requests = array();
        $requests['s_product_cd'] = $this->request->getParam('s_product_cd');
        $requests['s_product_name'] = $this->request->getParam('s_product_name');
        $requests['s_categorys'] = $this->request->getParam('s_categorys');
        $requests['s_publish'] = $this->request->getParam('s_publish');
        $requests['s_stock'] = $this->request->getParam('s_stock');
        $requests['pager_id'] = $this->request->getParam('pager_id');
        $this->hidden_vars = $requests;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //商品登録／変更
            case 'save_product':
            case 'save_product_variation':
                $exists_download = $this->request->getParam('no_download');
                $exists_image1 = $this->request->getParam('no_image1');
                $exists_image2 = $this->request->getParam('no_image2');
                $exists_image3 = $this->request->getParam('no_image3');

                $params = array();
                $params['id']               = $this->request->getParam('product_id');
                $params['product_cd']       = $this->request->getParam('product_cd');
                $params['name']             = $this->request->getParam('product_name');
                $params['price']            = $this->request->getParam('price');
                $params['off']              = $this->request->getParam('off');
                $params['description']      = $this->request->getParam('description');
                $params['add_description']  = $this->request->getParam('add_description');
                $params['categorys']        = $this->request->getParam('categorys');
                $params['publish']          = $this->request->getParam('publish');
                $params['notax']            = is_null($this->request->getParam('notax'))?'0':'1';
                $params['stock_manage']     = is_null($this->request->getParam('stock_manage'))?'0':'1';
                $params['stock']            = $this->request->getParam('stock');
                $params['quantity_limit']   = $this->request->getParam('quantity_limit');
                $params['download_publish'] = $this->request->getParam('download_publish');
                $params['download_price']   = $this->request->getParam('download_price');
                $params['download_off']     = $this->request->getParam('download_off');

                $params['image_prefix1'] = $this->request->getParam('image_prefix1');
                $params['image_prefix2'] = $this->request->getParam('image_prefix2');
                $params['image_prefix3'] = $this->request->getParam('image_prefix3');
                $params['download_prefix'] = $this->request->getParam('download_prefix');
                //画像１設定
                if ($exists_image1=='1') {
                    $params['image_file_url1'] = '##deleted##';
                }
                else {
                    $params['image_file_url1'] = $this->request->getParam('image_file_url1');
                }
                //画像２設定
                if ($exists_image2=='1') {
                    $params['image_file_url2'] = '##deleted##';
                }
                else {
                    $params['image_file_url2'] = $this->request->getParam('image_file_url2');
                }
                //画像３設定
                if ($exists_image3=='1') {
                    $params['image_file_url3'] = '##deleted##';
                }
                else {
                    $params['image_file_url3'] = $this->request->getParam('image_file_url3');
                }
                //ダウンロード製品設定
                if ($exists_download=='1') {
                    $params['product_url'] = '##deleted##';
                }
                else {
                    $params['product_url'] = $this->request->getParam('product_url');
                }
                $this->model['results'] = SimpleCartStoreProductModel::checkProduct($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $product_id = SimpleCartStoreProductModel::saveProduct($params);
                    //製品登録画面表示
                    if ($this->request->getParam('sc_action')=='save_product') {
                        $r = array(
                            'bp-slug-main' => $bp->simplecart_store->slug,
                            'bp-slug-sub'  => SC_BPID_PRODUCT,
                            'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                            'mu-slug-sub'  => SCLNG_PAGE_STORE_PRODUCT
                        );
                    }
                    //規格登録へ遷移
                    else {
                        $r = array(
                            'bp-slug-main' => $bp->simplecart_store->slug,
                            'bp-slug-sub'  => SC_BPID_VARIATION_RELATION.'/?from_product=1&product_id='.$product_id.'&download_publish='.$params['download_publish'],
                            'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                            'mu-slug-sub'  => SCLNG_PAGE_STORE_VARIATION_RELATION.'/?from_product=1&product_id='.$product_id.'&download_publish='.$params['download_publish']
                        );
                    }
                    $this->redirect($r);
                }
                //エラー有り
                else {
                    $this->model['download_prefix'] = $params['download_prefix'];
                    $this->model['prefix1'] = $params['image_prefix1'];
                    $this->model['prefix2'] = $params['image_prefix2'];
                    $this->model['prefix3'] = $params['image_prefix3'];
                    $this->model['categorys'] = SimpleCartStoreProductModel::getCategorys();
                    if ($exists_image1=='1') {
                        $params['image_file_url1'] = '';
                    }
                    if ($exists_image2=='1') {
                        $params['image_file_url2'] = '';
                    }
                    if ($exists_image3=='1') {
                        $params['image_file_url3'] = '';
                    }
                    if ($exists_download=='1') {
                        $params['product_url'] = '';
                    }
                    $this->model['product'] = $params;
                    $this->exec('store/product');
                }
                break;
            //
            default:
                $product_id = $this->request->getParam('product_id');
                $this->model['download_prefix'] = SimpleCartFunctions::Rand8();
                $this->model['prefix1'] = SimpleCartFunctions::Rand8();
                $this->model['prefix2'] = SimpleCartFunctions::Rand8();
                $this->model['prefix3'] = SimpleCartFunctions::Rand8();
                $this->model['categorys'] = SimpleCartStoreProductModel::getCategorys();
                $this->model['product'] = SimpleCartStoreProductModel::getProduct($product_id);
                $this->model['top'] = $this->request->getParam('top');
                $this->exec('store/product');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_PRODUCT_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_PRODUCT);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_PRODUCT_INFO_UPDATE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
