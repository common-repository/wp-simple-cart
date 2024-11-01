<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store User Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreUser.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreUser extends SimpleCart {

    /**
     * 店舗会員：ユーザー情報管理
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

        global $sc_states;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //入力画面へ遷移
            case 'update':
                $this->model['states'] = $sc_states;
                $this->model['store_info'] = SimpleCartStoreUserModel::getStore();
                $this->exec('store/store_info_update');
                break;
            //確認=>入力（戻る）へ遷移
            case 'update_back':
                $params = array();
                $params['ID']           = $this->request->getParam('ID');
                $params['user_login']   = $this->request->getParam('user_login');
                $params['email']        = $this->request->getParam('email');
                $params['display_name'] = $this->request->getParam('display_name');
                $params['url']          = $this->request->getParam('url');
                $params['address']      = $this->request->getParam('address');
                $params['street']       = $this->request->getParam('street');
                $params['state']        = $this->request->getParam('state');
                $params['zip']          = $this->request->getParam('zip');
                $params['tel']          = $this->request->getParam('tel');
                $params['fax']          = $this->request->getParam('fax');
                $this->model['store_info'] = $params;
                $this->model['states'] = $sc_states;
                $this->exec('store/store_info_update');
                break;
            //確認画面へ遷移
            case 'confirm':
                $params = array();
                $params['ID']           = $this->request->getParam('ID');
                $params['user_login']   = $this->request->getParam('user_login');
                $params['email']        = $this->request->getParam('email');
                $params['display_name'] = $this->request->getParam('display_name');
                $params['url']          = $this->request->getParam('url');
                $params['address']      = $this->request->getParam('address');
                $params['street']       = $this->request->getParam('street');
                $params['state']        = $this->request->getParam('state');
                $params['zip']          = $this->request->getParam('zip');
                $params['tel']          = $this->request->getParam('tel');
                $params['fax']          = $this->request->getParam('fax');
                $this->model['results'] = SimpleCartStoreUserModel::checkStore($params);
                $this->model['states'] = $sc_states;
                //エラー無し
                if ($this->model['results']===true) {
                    $this->hidden_vars = $params;
                    $this->exec('store/store_info_update_confirm');
                }
                //エラー有り
                else {
                    $this->model['store_info'] = $params;
                    $this->exec('store/store_info_update');
                }
                break;
            //変更内容の更新
            case 'completed':
                $params = array();
                $params['ID']           = $this->request->getParam('ID');
                $params['user_login']   = $this->request->getParam('user_login');
                $params['email']        = $this->request->getParam('email');
                $params['display_name'] = $this->request->getParam('display_name');
                $params['url']          = $this->request->getParam('url');
                $params['address']      = $this->request->getParam('address');
                $params['street']       = $this->request->getParam('street');
                $params['state']        = $this->request->getParam('state');
                $params['zip']          = $this->request->getParam('zip');
                $params['tel']          = $this->request->getParam('tel');
                $params['fax']          = $this->request->getParam('fax');
                $this->model['results'] = SimpleCartStoreUserModel::saveStore($params);
                $this->exec('store/store_info_update_complete');
                break;
            //パスワード変更画面へ遷移
            case 'change_password':
                $this->exec('store/store_info_password');
                break;
            //パスワード変更
            case 'save_password':
                $params = array();
                $params['pass1'] = $this->request->getParam('pass1');
                $params['pass2'] = $this->request->getParam('pass2');
                $this->model['results'] = SimpleCartStoreUserModel::checkPassword($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $this->model['results'] = SimpleCartStoreUserModel::savePassword($params);
                    $this->exec('store/store_info_password_complete');
                }
                //エラー有り
                else {
                    $this->exec('store/store_info_password');
                }
                break;
            //
            default:
                $this->model['states'] = $sc_states;
                $this->model['store_info'] = SimpleCartStoreUserModel::getStore();
                $this->exec('store/store_info');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_USER_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_USER);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        switch ($this->request->getParam('sc_action')) {
            //パスワード変更画面へ遷移
            case 'change_password':
                $title = SCLNG_STORE_USER_INFO_PASSWORD;
                break;
            //パスワード変更
            case 'save_password':
                $title = SCLNG_STORE_USER_INFO_PASSWORD;
                break;
            //
            default:
                $title = SCLNG_STORE_USER_INFO_UPDATE;
                break;
        }
        ?>
        <h2><?php _e($title, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
