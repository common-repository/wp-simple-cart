<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Product Detail Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartPublicProductDetail.php 147731 2009-08-21 05:28:05Z tajima $
 */

class SimpleCartPublicProductDetail extends SimpleCart {

    /**
     * 全体公開：商品詳細
     *
     */
    function execute() {
        global $sc_recommend;
        //POST値を取得
        $from               = $this->request->getParam('from');
        $id                 = $this->request->getParam('id');
        $s_product_search   = $this->request->getParam('s_product_search');
        $s_categorys        = $this->request->getParam('s_categorys');
        $s_store            = $this->request->getParam('s_store');
        $s_sort             = $this->request->getParam('s_sort');
        $s_review_recommend = $this->request->getParam('s_review_recommend');
        $s_review_comment   = $this->request->getParam('s_review_comment');
        $s_review_user      = $this->request->getParam('s_review_user');
        $s_product_cd       = $this->request->getParam('s_product_cd');
        $s_text             = $this->request->getParam('s_text');
        $s_text_description = $this->request->getParam('s_text_description');
        $s_sort             = $this->request->getParam('s_sort');

        $pager_id           = $this->request->getParam('pager_id');
        $store_id           = $this->request->getParam('store_id');
        $pricestock_id      = $this->request->getParam('pricestock_id');
        $quantity           = $this->request->getParam('quantity');
        $download           = $this->request->getParam('download');
        //数量の入力状態
        $quantity_0         = $this->request->getParam('quantity_0');
        $quantity_1         = $this->request->getParam('quantity_1');

        //エラー時用のID
        $error_id = $this->request->getParam('error_id');
        //お気に入りから来た！
        $favorite_id = $this->request->getParam('favorite_id');
        //レビュー管理から来た
        $review_id = $this->request->getParam('review_id');

        $params = array();
        $params['product_id']         = $id;
        $params['s_product_search']   = $s_product_search;
        $params['s_store']            = $s_store;
        $params['s_sort']             = $s_sort;
        $params['s_review_recommend'] = $s_review_recommend;
        $params['s_review_comment']   = $s_review_comment;
        $params['s_review_user']      = $s_review_user;
        $params['s_product_cd']       = $s_product_cd;
        $params['s_text']             = $s_text;
        $params['s_text_description'] = $s_text_description;
        $params['s_sort']             = $s_sort;
        $params['s_categorys']        = $s_categorys;
        $params['from']               = $from;

        $this->model['product'] = SimpleCartPublicModel::getProduct($params);

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //商品プレビュー
            case 'preview':
                //商品情報をPOSTデータで上書き
                $this->model['sc_action'] = $this->request->getParam('sc_action');
                //商品登録前
                if (strpos($this->request->getParam('image_file_url1'), '?') > 0) {
                    $this->model['product']['image_prefix1']   = $this->request->getParam('image_prefix1');
                    $this->model['product']['image_file_url1'] = $this->createImage(1, $this->request->getParam('image_prefix1'), $this->request->getParam('image_file_url1'), 96);
                }
                if (strpos($this->request->getParam('image_file_url2'), '?') > 0) {
                    $this->model['product']['image_prefix2']   = $this->request->getParam('image_prefix2');
                    $this->model['product']['image_file_url2'] = $this->createImage(2, $this->request->getParam('image_prefix2'), $this->request->getParam('image_file_url2'), 192);
                }
                if (strpos($this->request->getParam('image_file_url3'), '?') > 0) {
                    $this->model['product']['image_prefix3']   = $this->request->getParam('image_prefix3');
                    $this->model['product']['image_file_url3'] = $this->createImage(3, $this->request->getParam('image_prefix3'), $this->request->getParam('image_file_url3'), 384);
                }
                $this->model['product']['store_id']                 = $this->user_ID;
                $this->model['product']['product_cd']               = $this->request->getParam('product_cd');
                $this->model['product']['name']                     = $this->request->getParam('product_name');
                $this->model['product']['description']              = $this->request->getParam('description');
                $this->model['product']['add_description']          = $this->request->getParam('add_description');
                $this->model['product']['price']                    = $this->request->getParam('price');
                $this->model['product']['off']                      = $this->request->getParam('off');
                $this->model['product']['categorys']                = $this->request->getParam('categorys');
                $this->model['product']['notax']                    = is_null($this->request->getParam('notax'))?'0':'1';
                $this->model['product']['stock_manage']             = 1;
                $this->model['product']['stock']                    = 0;
                $this->model['product']['download_publish']         = $this->request->getParam('download_publish');
                $this->model['product']['quantity_limit']           = $this->request->getParam('quantity_limit');
                $this->model['product']['store_name']               = $this->display_name;
                $this->model['product']['enable_variation']         = 0;
                $this->model['product']['enable_download_variation']= 0;
                SimpleCartFunctions::CalcPrices($this->model['product']);
                break;
            //製品をカートへ投入
            case 'cart':
                $chk_params = array();
                $chk_params['product_id']    = $id;
                $chk_params['pricestock_id'] = $pricestock_id;
                $chk_params['quantity']      = $quantity;
                $chk_params['error_id']      = $error_id;

                $this->model['results'] = SimpleCartPublicModel::checkQuantity($chk_params);
                if ($this->model['results']===true) {
                    $cart = array();
                    $cart['store_id']      = $store_id;
                    $cart['product_id']    = $id;
                    $cart['pricestock_id'] = $pricestock_id;
                    $cart['quantity']      = $quantity;
                    $cart['download']      = $download;
                    SimpleCartModel::saveCart($cart);
                }
                break;
            //
            default:
                break;
        }
        $product = $this->model['product'];
        $params['store_id'] = $product['store_id'];
        if ($product['enable_variation']=='1') {
            $params['download_publish'] = '0';
            $this->model['product_variations'] = SimpleCartPublicModel::getProductVariations($params);
            $this->model['product_variation_values'] = SimpleCartPublicModel::getProductVariationValues($params);
            if (is_array($this->model['product_variations'])) {
                foreach ($this->model['product_variations'] as $key=>$val) {
                    $params['variation_id'] = $val;
                    $this->model['variation_values'][$key] = SimpleCartPublicModel::getVariationValues($params);
                }
            }
        }
        if ($product['enable_download_variation']=='1') {
            $params['download_publish'] = '1';
            $this->model['product_download_variations'] = SimpleCartPublicModel::getProductVariations($params);
            $this->model['product_download_variation_values'] = SimpleCartPublicModel::getProductVariationValues($params);
            if (is_array($this->model['product_download_variations'])) {
                foreach ($this->model['product_download_variations'] as $key=>$val) {
                    $params['variation_id'] = $val;
                    $this->model['download_variation_values'][$key] = SimpleCartPublicModel::getVariationValues($params);
                }
            }
        }
        //レビュー一覧取得
        $this->model['review_list'] = SimpleCartPublicModel::getReviewList($params);

        $this->model['from']          = $from;
        $this->model['quantity_0']    = $quantity_0;
        $this->model['quantity_1']    = $quantity_1;
        $this->model['conditions']    = $params;
        $this->model['error_id']      = $error_id;
        $this->model['favorite_id']   = $favorite_id;
        $this->model['review_id']     = $review_id;
        $this->model['review_recommend_list']  = $sc_recommend;
        $this->hidden_vars = $params;
        $this->exec('public/product_detail');
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
        <h2><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }

    /**
     * プレビュー用サムネイル画像作成
     *
     */
    function createImage($seq, $prefix, $filler, $size) {
        if (!is_null($filler) && $filler!='') {
            $file = $filler;
            $file = explode('?', $file);
            $file = $file[0];
            $temp_file = SimpleCartFunctions::TemporaryDir($prefix) . '/' . $file;
            $filler_file = SimpleCartFunctions::TemporaryDir($prefix) . '/' . $seq . '_preview_' . $file;

            require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/class.Thumbnail.php');
            $thum_obj = new Thumbnail($temp_file, $size, $size);

            //サムネイル生成
            $thum_obj->save($filler_file);
            return $seq . '_preview_' . $file;
        }
        return '';
    }
}
