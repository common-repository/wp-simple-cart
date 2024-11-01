<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User History Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserHistory.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartUserHistory extends SimpleCart {

    /**
     * 一般会員：購入履歴
     *
     */
    function execute() {
        if (is_null($this->user_ID) || $this->user_ID==0) {
            $this->roleError();
            return;
        }

        global $sc_status;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //購入履歴詳細画面へ遷移
            case 'order':
                $params = array();
                $params['order_id'] = $this->request->getParam('order_id');
                $this->model['status'] = $sc_status;
                $this->model['order'] = SimpleCartUserHistoryModel::getOrder($params);
                $this->exec('user/user_history_detail');
                break;
            //
            default:
                $params = array();
                $pager_id = $this->request->getParam('pager_id');
                $params['pager_id'] = 1;
                if (!is_null($pager_id) && $pager_id!='') {
                    $params['pager_id'] = $pager_id;
                }
                $this->model['status'] = $sc_status;
                $this->model['order_count'] = SimpleCartUserHistoryModel::getOrderList($params, true);
                $this->model['order_list'] = SimpleCartUserHistoryModel::getOrderList($params);
                $this->exec('user/user_history');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_USER_HISTORY, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::userTabMenu(SC_PGID_USER_HISTORY);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        switch ($this->request->getParam('sc_action')) {
            //受注詳細画面へ遷移
            case 'order':
                $title = __(SCLNG_USER_HISTORY_DETAIL, SC_DOMAIN);
                break;
            default:
                $title = __(SCLNG_USER_HISTORY_LIST, SC_DOMAIN);
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
