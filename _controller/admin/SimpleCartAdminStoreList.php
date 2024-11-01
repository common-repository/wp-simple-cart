<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Store User Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartAdminStoreList.php 151036 2009-09-01 06:50:07Z tajima $
 */

class SimpleCartAdminStoreList extends SimpleCartAdmin {

    /**
     * Store List Controller
     *
     */
    function execute() {
        global $wp_roles;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //店舗会員削除
            case 'allusers':
                //停止
                if (isset($_REQUEST['alluser_non_active'])) {
                    $users = $this->request->getParam('allusers');
                    if (is_array($users)) {
                        foreach ($users as $id) {
                            SimpleCartAdminModel::stopStoreUser($id);
                        }
                    }
                }
                //復活
                else if (isset($_REQUEST['alluser_active'])) {
                    $users = $this->request->getParam('allusers');
                    if (is_array($users)) {
                        foreach ($users as $id) {
                            SimpleCartAdminModel::revivalStoreUser($id);
                        }
                    }
                }
                //削除
                else if (isset($_REQUEST['alluser_delete'])) {
                    $users = $this->request->getParam('allusers');
                    if (is_array($users)) {
                        foreach ($users as $id) {
                            SimpleCartAdminModel::deleteStoreUser($id);
                        }
                    }
                }
                break;
            //既存ユーザーの店舗会員登録
            case 'adduser':
                $results = SimpleCartAdminModel::addExistsUserCap($this->request->getParam('user_login'));
                if ($results==1) {
                    $this->model['add_user_error'] = __(SCLNG_CHECK_REQUIRED_STORE_LOGIN_ID, SC_DOMAIN);
                }
                else if ($results==2) {
                    $this->model['add_user_error'] = __(SCLNG_CHECK_NOT_FOUND_USER, SC_DOMAIN);
                }
                break;
            //
            default:
                break;
        }
        $this->model['roles'] = $wp_roles;
        $this->model['user_list'] = SimpleCartAdminModel::getStoreList();
        $this->exec('admin/store_list');
    }
}
