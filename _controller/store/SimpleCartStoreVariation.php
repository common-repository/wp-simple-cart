<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Variation Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreVariation.php 140359 2009-07-29 09:13:26Z tajima $
 */

class SimpleCartStoreVariation extends SimpleCart {

    /**
     * 店舗会員：規格管理
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

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //規格追加／変更画面へ遷移
            case 'update_variation':
                $params = array();
                $params['id'] = $this->request->getParam('variation_id');
                $this->model['variation'] = SimpleCartStoreVariationModel::getVariation($params);
                $this->model['values_count'] = $this->request->getParam('values_count');
                if (is_null($this->model['values_count']) || $this->model['values_count']=='') {
                    $this->model['values_count'] = count($this->model['variation']['values_value']);
                }
                $this->exec('store/variation');
                break;
            //規格詳細数変更
            case 'change_values_count':
                $params = array();
                $params['id']   = $this->request->getParam('variation_id');
                $params['name'] = $this->request->getParam('variation_name');
                $params['values_value'] = $this->request->getParam('values_value');
                $this->model['values_count'] = $this->request->getParam('values_count');
                $this->model['variation'] = $params;
                $this->exec('store/variation');
                break;
            //規格追加／変更
            case 'save_variation':
                $params = array();
                $params['id']   = $this->request->getParam('variation_id');
                $params['name'] = $this->request->getParam('variation_name');
                $params['values_value'] = $this->request->getParam('values_value');
                $this->model['results'] = SimpleCartStoreVariationModel::checkVariation($params);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreVariationModel::saveVariation($params);
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_store->slug,
                        'bp-slug-sub'  => SC_BPID_VARIATION,
                        'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_STORE_VARIATION
                    );
                    $this->redirect($r);
                }
                //エラー有り
                else {
                    $this->model['values_count'] = $this->request->getParam('values_count');
                    $this->model['variation'] = $params;
                    $this->exec('store/variation');
                }
                break;
            //規格削除
            case 'delete_variation':
                $variations = $this->request->getParam('variations');
                SimpleCartStoreVariationModel::deleteVariations($variations);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_store->slug,
                    'bp-slug-sub'  => SC_BPID_VARIATION,
                    'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_STORE_VARIATION
                );
                $this->redirect($r);
                break;
            //
            default:
                $this->model['variation_list'] = SimpleCartStoreVariationModel::getVariationList();
                $this->exec('store/variation_list');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_VARIATION_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_VARIATION);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_VARIATION_INFO_UPDATE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
