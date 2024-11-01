<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Store Manage Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartAdminStoreManage.php 143340 2009-08-06 11:47:12Z tajima $
 */

class SimpleCartAdminStoreManage extends SimpleCartAdmin {

    /**
     * Store Manage Controller
     *
     */
    function execute() {
        global $sc_status, $sc_states;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //受注一覧検索
            case 'search_store_order':
                $params = array();
                $params['pager_id'] = 1;
                $pager_id = $this->request->getParam('pager_id');
                if (!is_null($pager_id) && $pager_id!='') {
                    $params['pager_id'] = $pager_id;
                }
                $params['s_store_id']     = $this->request->getParam('s_store_id');
                $params['s_order_fyy']    = $this->request->getParam('s_order_fyy');
                $params['s_order_fmm']    = $this->request->getParam('s_order_fmm');
                $params['s_order_fdd']    = $this->request->getParam('s_order_fdd');
                $params['s_order_no']     = $this->request->getParam('s_order_no');
                $params['s_order_status'] = $this->request->getParam('s_order_status');
                $params['s_order_login']  = $this->request->getParam('s_order_login');
                $params['s_order_user']   = $this->request->getParam('s_order_user');
                $this->model['status']    = $sc_status;
                $this->model['order_search'] = $params;
                $this->hidden_vars = $params;
                $this->model['store_user']  = SimpleCartAdminModel::getStoreUser($params['s_store_id']);
                $this->model['order_count'] = SimpleCartAdminModel::getOrderList($params, true);
                $this->model['order_list']  = SimpleCartAdminModel::getOrderList($params);
                $this->exec('admin/store_order_list');
                break;
            //受注情報閲覧
            case 'store_order':
                $hidden = array();
                $hidden['page_id']        = $this->request->getParam('page_id');
                $hidden['s_store_id']     = $this->request->getParam('s_store_id');
                $hidden['s_order_fyy']    = $this->request->getParam('s_order_fyy');
                $hidden['s_order_fmm']    = $this->request->getParam('s_order_fmm');
                $hidden['s_order_fdd']    = $this->request->getParam('s_order_fdd');
                $hidden['s_order_no']     = $this->request->getParam('s_order_no');
                $hidden['s_order_status'] = $this->request->getParam('s_order_status');
                $hidden['s_order_login']  = $this->request->getParam('s_order_login');
                $hidden['s_order_user']   = $this->request->getParam('s_order_user');
                $this->hidden_vars = $hidden;
                $params = array();
                $params['order_id'] = $this->request->getParam('order_id');
                $this->model['status'] = $sc_status;
                $this->model['states'] = $sc_states;
                $this->model['order'] = SimpleCartAdminModel::getOrder($params);

                $this->model['store_user']  = SimpleCartAdminModel::getStoreUser($hidden['s_store_id']);
                $message_thread_list = SimpleCartAdminModel::getMessageThreadList($params);
                foreach ($message_thread_list as $message_thread) {
                    if ($message_thread['thread_sender_id'] == $this->model['order']['store_id']) {
                        $this->model['message_list'] = $message_thread['message_list'];
                        break;
                    }
                }
                $this->exec('admin/store_order');
                break;
            //
            default:
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

                $this->model['ymd_info'] = $params;
                $this->model['sales_list'] = SimpleCartAdminModel::getSalesList($params);
                $this->exec('admin/store_manage');
                break;
        }
    }
}
