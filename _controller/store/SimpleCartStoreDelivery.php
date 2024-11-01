<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Delivery Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreDelivery.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreDelivery extends SimpleCart {

    /**
     * 店舗会員：配送管理
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
            //配送先追加／変更画面へ遷移
            case 'update_delivery':
                $delivery_id = $this->request->getParam('delivery_id');
                $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
                $this->exec('store/delivery');
                break;
            //配送先追加／変更
            case 'save_delivery':
                $params = array();
                $params['id']       = $this->request->getParam('delivery_id');
                $params['name']     = $this->request->getParam('delivery_name');
                $params['sort']     = $this->request->getParam('sort');
                $this->model['results'] = SimpleCartStoreDeliveryModel::checkDelivery($params);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreDeliveryModel::saveDelivery($params);
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_store->slug,
                        'bp-slug-sub'  => SC_BPID_DELIVERY,
                        'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_STORE_DELIVERY
                    );
                    $this->redirect($r);
                }
                //エラー有り
                else {
                    $this->model['delivery'] = $params;
                    $this->exec('store/delivery');
                }
                break;
            //配送先オプション更新
            case 'save_delivery_option':
                $params = array();
                $params['id'] = $this->request->getParam('delivery_free_id');
                $params['price_limit'] = $this->request->getParam('price_limit');
                $this->model['results'] = SimpleCartStoreDeliveryModel::checkDeliveryLimitOption($params);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreDeliveryModel::saveDeliveryLimitOption($params);
                    $this->model['delivery_list'] = SimpleCartStoreDeliveryModel::getDeliveryList();
                    $this->model['delivery_free'] = $params;
                    $this->exec('store/delivery_list');
                }
                //エラー有り
                else {
                    $this->model['delivery_list'] = SimpleCartStoreDeliveryModel::getDeliveryList();
                    $this->model['delivery_free'] = $params;
                    $this->exec('store/delivery_list');
                }
                break;
            //配送先削除
            case 'delete_delivery':
                $deliverys = $this->request->getParam('deliverys');
                SimpleCartStoreDeliveryModel::deleteDeliverys($deliverys);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_store->slug,
                    'bp-slug-sub'  => SC_BPID_DELIVERY,
                    'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_STORE_DELIVERY
                );
                $this->redirect($r);
                break;
            //配送先並順変更
            case 'delivery_order':
                $sort = $this->request->getParam('sort');
                $this->model['results'] = SimpleCartStoreDeliveryModel::checkDeliverySortOrder($sort);
                if ($this->model['results']===true) {
                    SimpleCartStoreDeliveryModel::saveDeliverySortOrder($sort);
                }
                $this->model['delivery_list'] = SimpleCartStoreDeliveryModel::getDeliveryList();
                $this->model['delivery_free'] = SimpleCartStoreDeliveryModel::getDeliveryLimitOption();
                //エラーだった場合、リクエストデータで詰替え
                if ($this->model['results']!==true) {
                    foreach ($sort as $k=>$v) {
                        $this->model['delivery_list'][$k]['sort'] = $v;
                    }
                }
                $this->exec('store/delivery_list');
                break;
            //配送料金一覧へ遷移
            case 'delivery_values_list':
                $delivery_id = $this->request->getParam('delivery_id');
                $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
                $this->model['delivery_values_list'] = SimpleCartStoreDeliveryModel::getDeliveryValuesList($delivery_id);
                $this->exec('store/delivery_values_list');
                break;
            //配送料金の追加／更新へ遷移
            case 'update_delivery_values':
                $delivery_id = $this->request->getParam('delivery_id');
                $delivery_values_id = $this->request->getParam('delivery_values_id');
                $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
                $this->model['delivery_values'] = SimpleCartStoreDeliveryModel::getDeliveryValues($delivery_values_id);
                $this->exec('store/delivery_values');
                break;
            //配送先追加／変更
            //都道府県からの料金計算ができないので追加／編集／削除なしで
            //実装する場合もリダイレクトがちと困るので別ページにするのがよさげ
            //case 'save_delivery_values':
            //    $params = array();
            //    $params['id']           = $this->request->getParam('delivery_values_id');
            //    $params['delivery_id']  = $this->request->getParam('delivery_id');
            //    $params['name']         = $this->request->getParam('delivery_values_name');
            //    $params['delivery_fee'] = $this->request->getParam('delivery_fee');
            //    $params['sort']         = $this->request->getParam('sort');
            //    $this->model['results'] = SimpleCartStoreDeliveryModel::checkDeliveryValues($params);
            //    //エラー無し
            //    if ($this->model['results']===true) {
            //        SimpleCartStoreDeliveryModel::saveDeliveryValues($params);
            //        $this->redirect(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_DELIVERY . '?action=delivery_values_list');
            //    }
            //    //エラー有り
            //    else {
            //        $delivery_id = $this->request->getParam('delivery_id');
            //        $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
            //        $this->model['delivery_values'] = $params;
            //        $this->exec('store/delivery_values');
            //    }
            //    break;
            //配送料金削除
            //case 'delete_delivery_values':
            //    $delivery_values = $this->request->getParam('delivery_values');
            //    SimpleCartStoreDeliveryModel::deleteDeliveryValues($delivery_values);
            //    $delivery_id = $this->request->getParam('delivery_id');
            //    $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
            //    $this->model['delivery_values_list'] = SimpleCartStoreDeliveryModel::getDeliveryValuesList($delivery_id);
            //    $this->exec('store/delivery_values_list');
            //    break;
            //配送料金並順変更
            case 'delivery_values_order':
                $sort = $this->request->getParam('sort');
                $this->model['results'] = SimpleCartStoreDeliveryModel::checkDeliveryValuesSortOrder($sort);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreDeliveryModel::saveDeliveryValuesSortOrder($sort);
                }
                $delivery_id = $this->request->getParam('delivery_id');
                $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
                $this->model['delivery_values_list'] = SimpleCartStoreDeliveryModel::getDeliveryValuesList($delivery_id);
                //エラーだった場合、リクエストデータで詰替え
                if ($this->model['results']!==true) {
                    foreach ($sort as $k=>$v) {
                        $this->model['delivery_values_list'][$k]['sort'] = $v;
                    }
                }
                $this->exec('store/delivery_values_list');
                break;
            //配送料金変更
            case 'delivery_values_fee':
                $delivery_fee = $this->request->getParam('delivery_fee');
                $this->model['results'] = SimpleCartStoreDeliveryModel::checkDeliveryValuesFee($delivery_fee);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreDeliveryModel::saveDeliveryValuesFee($delivery_fee);
                }
                $delivery_id = $this->request->getParam('delivery_id');
                $this->model['delivery'] = SimpleCartStoreDeliveryModel::getDelivery($delivery_id);
                $this->model['delivery_values_list'] = SimpleCartStoreDeliveryModel::getDeliveryValuesList($delivery_id);
                //エラーだった場合、リクエストデータで詰替え
                if ($this->model['results']!==true) {
                    foreach ($delivery_fee as $k=>$v) {
                        $this->model['delivery_values_list'][$k]['delivery_fee'] = $v;
                    }
                }
                $this->exec('store/delivery_values_list');
                break;
            //
            default:
                $this->model['delivery_list'] = SimpleCartStoreDeliveryModel::getDeliveryList();
                $this->model['delivery_free'] = SimpleCartStoreDeliveryModel::getDeliveryLimitOption();
                $this->exec('store/delivery_list');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_DELIVERY_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_DELIVERY);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_DELIVERY_INFO_UPDATE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
