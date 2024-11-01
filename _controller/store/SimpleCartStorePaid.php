<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Paid Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStorePaid.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStorePaid extends SimpleCart {

    /**
     * 店舗会員：支払方法管理
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
            //支払方法追加／変更画面へ遷移
            case 'update_paid':
                $paid_id = $this->request->getParam('paid_id');
                $this->model['paid'] = SimpleCartStorePaidModel::getPaid($paid_id);
                $this->exec('store/paid_method');
                break;
            //支払方法追加／変更
            case 'save_paid':
                $params = array();
                $params['id']         = $this->request->getParam('paid_id');
                $params['name']       = $this->request->getParam('paid_name');
                $params['commission'] = $this->request->getParam('commission');
                $params['sort']       = $this->request->getParam('sort');
                $this->model['results'] = SimpleCartStorePaidModel::checkPaid($params);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStorePaidModel::savePaid($params);
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_store->slug,
                        'bp-slug-sub'  => SC_BPID_PAID,
                        'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_STORE_PAID_METHOD
                    );
                    $this->redirect($r);
                }
                //エラー有り
                else {
                    $this->model['paid'] = $params;
                    $this->exec('store/paid_method');
                }
                break;
            //支払方法削除
            case 'delete_paid':
                $paids = $this->request->getParam('paids');
                SimpleCartStorePaidModel::deletePaids($paids);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_store->slug,
                    'bp-slug-sub'  => SC_BPID_PAID_METHOD,
                    'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_STORE_PAID_METHOD
                );
                $this->redirect($r);
                break;
            //支払方法並順変更
            case 'paid_order':
                $sort = $this->request->getParam('sort');
                $this->model['results'] = SimpleCartStorePaidModel::checkPaidSortOrder($sort);
                if ($this->model['results']===true) {
                    SimpleCartStorePaidModel::savePaidSortOrder($sort);
                }
                $this->model['paid_list'] = SimpleCartStorePaidModel::getPaidList();
                //エラーだった場合、リクエストデータで詰替え
                if ($this->model['results']!==true) {
                    foreach ($sort as $k=>$v) {
                        $this->model['paid_list'][$k]['sort'] = $v;
                    }
                }
                $this->exec('store/paid_method_list');
                break;
            //
            default:
                $this->model['paid_list'] = SimpleCartStorePaidModel::getPaidList();
                $this->exec('store/paid_method_list');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_PAID_METHOD_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_PAID);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_PAID_METHOD_INFO_UPDATE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
