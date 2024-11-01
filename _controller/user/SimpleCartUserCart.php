<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Cart Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserCart.php 140849 2009-07-30 09:07:35Z tajima $
 */

class SimpleCartUserCart extends SimpleCart {

    /**
     * 一般会員：会員情報管理
     *
     */
    function execute() {
        if (is_null($this->user_ID) || $this->user_ID==0) {
            $this->roleError();
            return;
        }

        global $sc_states, $sc_delivery_times, $bp;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //カートから削除
            case 'delete_cart':
                $params = array();
                $params['product_id'] = $this->request->getParam('product_id');
                $params['pricestock_id'] = $this->request->getParam('pricestock_id');
                $params['download'] = $this->request->getParam('download');
                SimpleCartUserCartModel::deleteCart($params);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_user->slug,
                    'bp-slug-sub'  => SC_BPID_CART,
                    'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                );
                $this->redirect($r);
                break;
            //購入点数変更 - up
            case 'change_quantity_up':
                $product_id = $this->request->getParam('product_id');
                $pricestock_id = $this->request->getParam('pricestock_id');
                $quantity = 1;

                $chk_params = array();
                $chk_params['product_id']    = $product_id;
                $chk_params['pricestock_id'] = $pricestock_id;
                $chk_params['quantity']      = $quantity;
                $this->model['results'] = SimpleCartUserCartModel::checkQuantity($chk_params);
                if ($this->model['results']===true) {
                    $params = array();
                    $params['store_id']      = $this->request->getParam('store_id');
                    $params['product_id']    = $product_id;
                    $params['pricestock_id'] = $pricestock_id;
                    $params['quantity']      = $quantity;
                    $params['download']      = $this->request->getParam('download');
                    SimpleCartModel::saveCart($params);
                }
                $r = array(
                    'bp-slug-main' => $bp->simplecart_user->slug,
                    'bp-slug-sub'  => SC_BPID_CART,
                    'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                );
                $this->redirect($r);
                break;
            //購入点数変更 - down
            case 'change_quantity_down':
                $product_id = $this->request->getParam('product_id');
                $pricestock_id = $this->request->getParam('pricestock_id');
                $quantity = -1;

                $chk_params = array();
                $chk_params['product_id']    = $product_id;
                $chk_params['pricestock_id'] = $pricestock_id;
                $chk_params['quantity']      = $quantity;
                $this->model['results'] = SimpleCartUserCartModel::checkQuantity($chk_params);
                if ($this->model['results']===true) {
                    $params = array();
                    $params['store_id']      = $this->request->getParam('store_id');
                    $params['product_id']    = $product_id;
                    $params['pricestock_id'] = $pricestock_id;
                    $params['quantity']      = $quantity;
                    $params['download']      = $this->request->getParam('download');
                    SimpleCartModel::saveCart($params);
                }
                $r = array(
                    'bp-slug-main' => $bp->simplecart_user->slug,
                    'bp-slug-sub'  => SC_BPID_CART,
                    'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                );
                $this->redirect($r);
                break;
            //送付先／請求先情報入力画面へ遷移
            case 'update_send_address':
                $this->model['states'] = $sc_states;
                $this->model['cart'] = SimpleCartUserCartModel::getCart();
                $this->model['user'] = SimpleCartUserCartModel::getUser();
                //店舗別に希望配送業者、希望配送時間帯、希望支払方法を取得する
                $stores = array();
                if (is_array($this->model['cart'])) {
                    foreach ($this->model['cart'] as $key=>$val) {
                        $stores[] = $key;
                    }
                }
                $this->model['deliverys'] = SimpleCartUserCartModel::getDeliverys($stores);
                $this->model['deliverys_times'] = SimpleCartUserCartModel::getDeliverysTimes($stores);
                $this->model['paid_methods'] = SimpleCartUserCartModel::getPaidMethods($stores);
                if (is_array($this->model['cart'])) {
                    $this->exec('user/user_cart_send_info');
                }
                else {
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_user->slug,
                        'bp-slug-sub'  => SC_BPID_CART,
                        'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                    );
                    $this->redirect($r);
                }
                break;
            //入力内容確認=>送付先／請求先へ戻る
            case 'back_send_address':
                $params = array();
                $params['display_name']    = $this->request->getParam('display_name');
                $params['user_email']      = $this->request->getParam('user_email');
                $params['send_first_name'] = $this->request->getParam('send_first_name');
                $params['send_last_name']  = $this->request->getParam('send_last_name');
                $params['send_first_furi'] = $this->request->getParam('send_first_furi');
                $params['send_last_furi']  = $this->request->getParam('send_last_furi');
                $params['send_address']    = $this->request->getParam('send_address');
                $params['send_street']     = $this->request->getParam('send_street');
                $params['send_state']      = $this->request->getParam('send_state');
                $params['send_zip']        = $this->request->getParam('send_zip');
                $params['send_tel']        = $this->request->getParam('send_tel');
                $params['send_fax']        = $this->request->getParam('send_fax');
                $params['send_mobile']     = $this->request->getParam('send_mobile');
                $params['bill_first_name'] = $this->request->getParam('bill_first_name');
                $params['bill_last_name']  = $this->request->getParam('bill_last_name');
                $params['bill_first_furi'] = $this->request->getParam('bill_first_furi');
                $params['bill_last_furi']  = $this->request->getParam('bill_last_furi');
                $params['bill_address']    = $this->request->getParam('bill_address');
                $params['bill_street']     = $this->request->getParam('bill_street');
                $params['bill_state']      = $this->request->getParam('bill_state');
                $params['bill_zip']        = $this->request->getParam('bill_zip');
                $params['bill_tel']        = $this->request->getParam('bill_tel');
                $params['bill_fax']        = $this->request->getParam('bill_fax');
                $params['bill_mobile']     = $this->request->getParam('bill_mobile');
                $params['deliverys']       = $this->request->getParam('deliverys');
                $params['deliverys_times'] = $this->request->getParam('deliverys_times');
                $params['paid_methods']    = $this->request->getParam('paid_methods');
                $params['frees']           = $this->request->getParam('frees');
                $this->model['states'] = $sc_states;
                $this->model['cart'] = SimpleCartUserCartModel::getCart();
                $this->model['user'] = $params;
                //店舗別に希望配送業者、希望配送時間帯、希望支払方法を取得する
                $stores = array();
                foreach ($this->model['cart'] as $key=>$val) {
                    $stores[] = $key;
                }
                $this->model['deliverys'] = SimpleCartUserCartModel::getDeliverys($stores);
                $this->model['deliverys_times'] = SimpleCartUserCartModel::getDeliverysTimes($stores);
                $this->model['paid_methods']= SimpleCartUserCartModel::getPaidMethods($stores);
                if (is_array($this->model['cart'])) {
                    $this->exec('user/user_cart_send_info');
                }
                else {
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_user->slug,
                        'bp-slug-sub'  => SC_BPID_CART,
                        'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                    );
                    $this->redirect($r);
                }
                break;
            //入力内容確認画面へ遷移
            case 'confirm':
                $params = array();
                $params['display_name']    = $this->request->getParam('display_name');
                $params['user_email']      = $this->request->getParam('user_email');
                $params['send_first_name'] = $this->request->getParam('send_first_name');
                $params['send_last_name']  = $this->request->getParam('send_last_name');
                $params['send_first_furi'] = $this->request->getParam('send_first_furi');
                $params['send_last_furi']  = $this->request->getParam('send_last_furi');
                $params['send_address']    = $this->request->getParam('send_address');
                $params['send_street']     = $this->request->getParam('send_street');
                $params['send_state']      = $this->request->getParam('send_state');
                $params['send_zip']        = $this->request->getParam('send_zip');
                $params['send_tel']        = $this->request->getParam('send_tel');
                $params['send_fax']        = $this->request->getParam('send_fax');
                $params['send_mobile']     = $this->request->getParam('send_mobile');
                $params['bill_first_name'] = $this->request->getParam('bill_first_name');
                $params['bill_last_name']  = $this->request->getParam('bill_last_name');
                $params['bill_first_furi'] = $this->request->getParam('bill_first_furi');
                $params['bill_last_furi']  = $this->request->getParam('bill_last_furi');
                $params['bill_address']    = $this->request->getParam('bill_address');
                $params['bill_street']     = $this->request->getParam('bill_street');
                $params['bill_state']      = $this->request->getParam('bill_state');
                $params['bill_zip']        = $this->request->getParam('bill_zip');
                $params['bill_tel']        = $this->request->getParam('bill_tel');
                $params['bill_fax']        = $this->request->getParam('bill_fax');
                $params['bill_mobile']     = $this->request->getParam('bill_mobile');
                $params['deliverys']       = $this->request->getParam('deliverys');
                $params['deliverys_value'] = $this->request->getParam('deliverys_value');
                $params['deliverys_times'] = $this->request->getParam('deliverys_times');
                $params['paid_methods']    = $this->request->getParam('paid_methods');
                $params['frees']           = $this->request->getParam('frees');
                $this->model['results'] = SimpleCartUserCartModel::checkUser($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $this->model['states'] = $sc_states;
                    //配送料金を算出しておく
                    $this->model['cart'] = SimpleCartUserCartModel::getCart();
                    $this->model['user'] = $params;
                    $this->hidden_vars = $params;
                    //店舗別に希望配送業者、配送料金、希望配送時間帯、希望支払方法を取得する
                    $stores = array();
                    $deliverys = array();
                    foreach ($this->model['cart'] as $key=>$val) {
                        $stores[] = $key;
                        $calc_costs[$key] = array('delivery'=>$params['deliverys'][$key], 'state'=>$params['send_state'], 'paid_method'=>$params['paid_methods'][$key]);
                    }
                    $this->model['deliverys'] = SimpleCartUserCartModel::getDeliverys($stores);
                    $this->model['deliverys_times'] = SimpleCartUserCartModel::getDeliverysTimes($stores);
                    $this->model['paid_methods'] = SimpleCartUserCartModel::getPaidMethods($stores);
                    $this->model['deliverys_cost'] = SimpleCartUserCartModel::getDeliverysCost($calc_costs);
                    $this->model['deliverys_value'] = SimpleCartUserCartModel::getDeliverysValue($calc_costs);
                    $this->model['commissions'] = SimpleCartUserCartModel::getCommissions($calc_costs);
                    if (is_array($this->model['cart'])) {
                        $this->exec('user/user_cart_confirm');
                    }
                    else {
                        $r = array(
                            'bp-slug-main' => $bp->simplecart_user->slug,
                            'bp-slug-sub'  => SC_BPID_CART,
                            'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                            'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                        );
                        $this->redirect($r);
                    }
                }
                //エラー有り
                else {
                    $this->model['states'] = $sc_states;
                    $this->model['cart'] = SimpleCartUserCartModel::getCart();
                    $this->model['user'] = $params;
                    //店舗別に希望配送業者、希望配送時間帯、希望支払方法を取得する
                    $stores = array();
                    foreach ($this->model['cart'] as $key=>$val) {
                        $stores[] = $key;
                    }
                    $this->model['deliverys'] = SimpleCartUserCartModel::getDeliverys($stores);
                    $this->model['deliverys_times'] = SimpleCartUserCartModel::getDeliverysTimes($stores);
                    $this->model['paid_methods'] = SimpleCartUserCartModel::getPaidMethods($stores);
                    if (is_array($this->model['cart'])) {
                        $this->exec('user/user_cart_send_info');
                    }
                    else {
                        $r = array(
                            'bp-slug-main' => $bp->simplecart_user->slug,
                            'bp-slug-sub'  => SC_BPID_CART,
                            'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                            'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                        );
                        $this->redirect($r);
                    }
                }
                break;
            //購入完了画面へ遷移
            case 'complete':
                $params = array();
                $params['cart']            = SimpleCartUserCartModel::getCart();
                $params['send_first_name'] = $this->request->getParam('send_first_name');
                $params['send_last_name']  = $this->request->getParam('send_last_name');
                $params['send_first_furi'] = $this->request->getParam('send_first_furi');
                $params['send_last_furi']  = $this->request->getParam('send_last_furi');
                $params['send_address']    = $this->request->getParam('send_address');
                $params['send_street']     = $this->request->getParam('send_street');
                $params['send_state']      = $this->request->getParam('send_state');
                $params['send_zip']        = $this->request->getParam('send_zip');
                $params['send_tel']        = $this->request->getParam('send_tel');
                $params['send_fax']        = $this->request->getParam('send_fax');
                $params['send_mobile']     = $this->request->getParam('send_mobile');
                $params['bill_first_name'] = $this->request->getParam('bill_first_name');
                $params['bill_last_name']  = $this->request->getParam('bill_last_name');
                $params['bill_first_furi'] = $this->request->getParam('bill_first_furi');
                $params['bill_last_furi']  = $this->request->getParam('bill_last_furi');
                $params['bill_address']    = $this->request->getParam('bill_address');
                $params['bill_street']     = $this->request->getParam('bill_street');
                $params['bill_state']      = $this->request->getParam('bill_state');
                $params['bill_zip']        = $this->request->getParam('bill_zip');
                $params['bill_tel']        = $this->request->getParam('bill_tel');
                $params['bill_fax']        = $this->request->getParam('bill_fax');
                $params['bill_mobile']     = $this->request->getParam('bill_mobile');
                $params['deliverys']       = $this->request->getParam('deliverys');
                $params['deliverys_cost']  = $this->request->getParam('deliverys_cost');
                $params['deliverys_value'] = $this->request->getParam('deliverys_value');
                $params['deliverys_times'] = $this->request->getParam('deliverys_times');
                $params['paid_methods']    = $this->request->getParam('paid_methods');
                $params['commissions']     = $this->request->getParam('commissions');
                $params['totals']          = $this->request->getParam('totals');
                $params['frees']           = $this->request->getParam('frees');
                if (is_array($params['cart'])) {
                    $this->model['results'] = SimpleCartUserCartModel::saveOrder($params);
                    $this->exec('user/user_cart_complete');
                }
                else {
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_user->slug,
                        'bp-slug-sub'  => SC_BPID_CART,
                        'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_USER_CART
                    );
                    $this->redirect($r);
                }
                break;
            //
            default:
                $this->model['cart'] = SimpleCartUserCartModel::getCart();
                $this->exec('user/user_cart');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_USER_CART_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::userTabMenu(SC_PGID_USER_CART);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_USER_CART_INFO_LIST, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
