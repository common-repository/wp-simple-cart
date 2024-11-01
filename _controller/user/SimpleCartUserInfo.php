<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Info Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserInfo.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartUserInfo extends SimpleCart {

    /**
     * 一般会員：会員情報管理
     *
     */
    function execute() {
        if (is_null($this->user_ID) || $this->user_ID==0) {
            $this->roleError();
            return;
        }

        global $sc_states;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //入力画面へ遷移
            case 'update':
                $this->model['states'] = $sc_states;
                $this->model['user'] = SimpleCartUserInfoModel::getUser();
                $this->exec('user/user_info_update');
                break;
            //確認=>入力（戻る）へ遷移
            case 'update_back':
                $params = array();
                $params['id']              = $this->request->getParam('id');
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
                $this->model['states'] = $sc_states;
                $this->model['user'] = $params;
                $this->exec('user/user_info_update');
                break;
            //確認画面へ遷移
            case 'confirm':
                $params = array();
                $params['id']              = $this->request->getParam('id');
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
                $this->model['results'] = SimpleCartUserInfoModel::checkUser($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $this->hidden_vars = $params;
                    $this->model['states'] = $sc_states;
                    $this->model['user'] = $params;
                    $this->exec('user/user_info_update_confirm');
                }
                //エラー有り
                else {
                    $this->model['states'] = $sc_states;
                    $this->model['user'] = $params;
                    $this->exec('user/user_info_update');
                }
                break;
            //ユーザー情報更新
            case 'save_user':
                $params = array();
                $params['id']              = $this->request->getParam('id');
                $params['display_name']    = $this->request->getParam('display_name');
                $params['email']           = $this->request->getParam('email');
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
                $this->model['results'] = SimpleCartUserInfoModel::saveUser($params);
                $this->exec('user/user_info_update_complete');
                break;
            //パスワード変更画面へ遷移
            case 'change_password':
                $this->exec('user/user_info_password');
                break;
            //パスワード変更
            case 'save_password':
                $params = array();
                $params['pass1'] = $this->request->getParam('pass1');
                $params['pass2'] = $this->request->getParam('pass2');
                $this->model['results'] = SimpleCartUserInfoModel::checkPassword($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $this->model['results'] = SimpleCartUserInfoModel::savePassword($params);
                    $this->exec('user/user_info_password_complete');
                }
                //エラー有り
                else {
                    $this->exec('user/user_info_password');
                }
                break;
            //
            default:
                $this->model['states'] = $sc_states;
                $this->model['user'] = SimpleCartUserInfoModel::getUser();
                $this->exec('user/user_info');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_USER_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::userTabMenu(SC_PGID_USER_INFO);
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
                $title = SCLNG_USER_INFO_PASSWORD;
                break;
            //パスワード変更
            case 'save_password':
                $title = SCLNG_USER_INFO_PASSWORD;
                break;
            //
            default:
                $title = SCLNG_USER_INFO_UPDATE;
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
