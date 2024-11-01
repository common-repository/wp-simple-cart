<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Variation Relation Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreVariationRelation.php 143626 2009-08-07 12:22:47Z tajima $
 */

class SimpleCartStoreVariationRelation extends SimpleCart {

    /**
     * 店舗会員：規格関連付け
     *
     */
    function execute() {
        global $sc_tax_method;

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
            //規格チェックイベント
            case 'checked_variation':
                $params = array();
                $params['product_id'] = $this->request->getParam('product_id');
                $params['download_publish'] = $this->request->getParam('download_publish');

                $this->model['product'] = SimpleCartStoreVariationRelationModel::getProduct($params);
                $this->model['download_publish'] = $params['download_publish'];
                $this->model['variations'] = SimpleCartStoreVariationRelationModel::getVariations();
                $this->model['product_variations'] = $this->request->getParam('product_variations');
                $this->model['product_variation_values'] = SimpleCartStoreVariationRelationModel::getProductVariationValues($params);
                if (is_array($this->model['product_variations'])) {
                    foreach ($this->model['product_variations'] as $key=>$val) {
                        $params['variation_id'] = $val;
                        $this->model['variation_values'][$key] = SimpleCartStoreVariationRelationModel::getVariationValues($params);
                    }
                }
                $this->exec('store/variation_relation');
                break;
            //規格チェックイベント
            case 'save_variation_relation':
                $params = array();
                $params['product_id']         = $this->request->getParam('product_id');
                $params['download_publish']   = $this->request->getParam('download_publish');
                $params['values_visible']     = $this->request->getParam('values_visible');
                $params['values_stock']       = $this->request->getParam('values_stock');
                $params['values_price']       = $this->request->getParam('values_price');
                $params['values_ids']         = $this->request->getParam('values_ids');
                $params['product_variations'] = $this->request->getParam('product_variations');

                $this->model['results'] = SimpleCartStoreVariationRelationModel::checkVariationValues($params);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreVariationRelationModel::saveVariationValues($params);
                }
                else {
                    $this->model['product'] = SimpleCartStoreVariationRelationModel::getProduct($params);
                    $this->model['download_publish'] = $params['download_publish'];
                    $this->model['variations'] = SimpleCartStoreVariationRelationModel::getVariations();
                    $this->model['product_variations'] = $params['product_variations'];
                    //product_variation_values用データを生成
                    $product_variation_values = array();
                    if (is_array($params['values_ids'])) {
                        foreach ($params['values_ids'] as $id) {
                            $product_variation_values[$id] = array();
                            $product_variation_values[$id]['price'] = $params['values_price'][$id];
                            if ($this->model['product']['stock_manage']=='1') {
                                $product_variation_values[$id]['stock'] = $params['values_stock'][$id];
                            }
                            $product_variation_values[$id]['visible'] = $params['values_visible'][$id];
                        }
                    }
                    $this->model['product_variation_values'] = $product_variation_values;

                    if (is_array($this->model['product_variations'])) {
                        foreach ($this->model['product_variations'] as $key=>$val) {
                            $params['variation_id'] = $val;
                            $this->model['variation_values'][$key] = SimpleCartStoreVariationRelationModel::getVariationValues($params);
                        }
                    }
                    $this->exec('store/variation_relation');
                    break;
                }
            //
            default:
                $params = array();
                $params['product_id'] = $this->request->getParam('product_id');
                $params['download_publish'] = $this->request->getParam('download_publish');

                $this->model['product'] = SimpleCartStoreVariationRelationModel::getProduct($params);
                $this->model['download_publish'] = $params['download_publish'];
                $this->model['variations'] = SimpleCartStoreVariationRelationModel::getVariations();
                $this->model['product_variations'] = SimpleCartStoreVariationRelationModel::getProductVariations($params);
                $this->model['product_variation_values'] = SimpleCartStoreVariationRelationModel::getProductVariationValues($params);
                if (is_array($this->model['product_variations'])) {
                    foreach ($this->model['product_variations'] as $key=>$val) {
                        $params['variation_id'] = $val;
                        $this->model['variation_values'][$key] = SimpleCartStoreVariationRelationModel::getVariationValues($params);
                    }
                }
                $this->model['from_product'] = $this->request->getParam('from_product');
                $this->exec('store/variation_relation');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_VARIATION_RELATION_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_VARIATION_RELATION);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_VARIATION_RELATION_INFO_UPDATE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
