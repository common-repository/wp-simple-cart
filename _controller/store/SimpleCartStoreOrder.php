<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Order Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreOrder.php 143626 2009-08-07 12:22:47Z tajima $
 */

class SimpleCartStoreOrder extends SimpleCart {

    /**
     * 店舗会員：受注管理
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

        global $sc_status, $sc_states, $bp;

        $search = array();
        $pager_id = $this->request->getParam('pager_id');
        $search['pager_id'] = 1;
        if (!is_null($pager_id) && $pager_id!='') {
            $search['pager_id'] = $pager_id;
        }

        $search['s_order_status'] = $this->request->getParam('s_order_status');
        $search['s_order_no']  = $this->request->getParam('s_order_no');
        $search['s_order_fyy'] = $this->request->getParam('s_order_fyy');
        $search['s_order_fmm'] = $this->request->getParam('s_order_fmm');
        $search['s_order_fdd'] = $this->request->getParam('s_order_fdd');
        $search['s_order_tyy'] = $this->request->getParam('s_order_tyy');
        $search['s_order_tmm'] = $this->request->getParam('s_order_tmm');
        $search['s_order_tdd'] = $this->request->getParam('s_order_tdd');

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //受注検索
            case 'search_order':
                $this->model['status'] = $sc_status;
                $this->model['order_search'] = $search;
                $this->model['order_count'] = SimpleCartStoreOrderModel::getOrderList($search, true);
                $this->model['order_list'] = SimpleCartStoreOrderModel::getOrderList($search);
                $this->hidden_vars = $search;
                $this->exec('store/order_list');
                break;
            //受注削除
            case 'delete_order':
                $params = array();
                $params['order_id'] = $this->request->getParam('order_id');
                SimpleCartStoreOrderModel::deleteOrder($params);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_store->slug,
                    'bp-slug-sub'  => SC_BPID_ORDER,
                    'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_STORE_ORDER
                );
                $this->redirect($r);
                break;
            //受注詳細画面へ遷移
            case 'order':
                $params = array();
                $params['order_id'] = $this->request->getParam('order_id');
                $this->model['status'] = $sc_status;
                $this->model['states'] = $sc_states;
                $this->model['order'] = SimpleCartStoreOrderModel::getOrder($params);
                $this->hidden_vars = $search;
                $this->exec('store/order');
                break;
            //受注状況変更
            case 'change_status':
                $params = array();
                $params['order_id'] = $this->request->getParam('order_id');
                $params['status'] = $this->request->getParam('status');
                $params['download'] = $this->request->getParam('download');
                SimpleCartStoreOrderModel::saveOrderStatus($params);
                $this->model['status'] = $sc_status;
                $this->model['states'] = $sc_states;
                $this->model['order'] = SimpleCartStoreOrderModel::getOrder($params);
                $this->hidden_vars = $search;
                $this->exec('store/order');
                break;
            //
            default:
                $this->model['status'] = $sc_status;
                $this->model['order_search'] = $search;
                $this->model['order_count'] = SimpleCartStoreOrderModel::getOrderList($search, true);
                $this->model['order_list'] = SimpleCartStoreOrderModel::getOrderList($search);
                $this->exec('store/order_list');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_ORDER_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_ORDER);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        switch ($this->request->getParam('sc_action')) {
            case 'order':
                $title = __(SCLNG_STORE_ORDER_INFO_DETAIL, SC_DOMAIN);
                break;
            default:
                $title = __(SCLNG_STORE_ORDER_INFO_LIST, SC_DOMAIN);
                break;
        }
        ?>
        <h2><?php echo $title; ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
