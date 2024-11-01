<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public MemberEntry Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartPublicMemberEntry.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartPublicMemberEntry extends SimpleCart {

    /**
     * 全体公開：会員登録
     *
     */
    function execute() {

        require_once(ABSPATH . 'wp-includes/registration-functions.php');

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //確認画面へ遷移
            case 'confirm':
                $params = array();
                $params['user_login'] = $this->request->getParam('user_login');
                $params['email']      = $this->request->getParam('email');
                $this->model['results'] = SimpleCartPublicModel::checkUser($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $this->hidden_vars = $params;
                    $this->model['user'] = $params;
                    $this->exec('public/member_entry_confirm');
                }
                //エラー有り
                else {
                    $this->model['user'] = $params;
                    $this->exec('public/member_entry');
                }
                break;
            //確認=>入力（戻る）へ遷移
            case 'entry_back':
                $params = array();
                $params['user_login'] = $this->request->getParam('user_login');
                $params['email']      = $this->request->getParam('email');
                $this->model['user'] = $params;
                $this->exec('public/member_entry');
                break;
            //新規会員登録
            case 'save_user':
                $params = array();
                $params['user_login'] = $this->request->getParam('user_login');
                $params['email']      = $this->request->getParam('email');
                $this->model['results'] = SimpleCartPublicModel::checkUser($params);
                //エラー無し
                if ($this->model['results']===true) {
                    $this->model['results'] = SimpleCartPublicModel::saveUser($params);
                    $this->exec('public/member_entry_complete');
                }
                //エラー有り
                else {
                    $this->model['user'] = $params;
                    $this->exec('public/member_entry');
                }
                break;
            //
            default:
                $this->exec('public/member_entry');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_PUBLIC_MEMBER_ENTRY, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
