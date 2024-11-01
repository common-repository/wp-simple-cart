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
 * @version     svn:$Id: SimpleCartAdminStoreUser.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartAdminStoreUser extends SimpleCartAdmin {

    /**
     * Store User Controller
     *
     */
    function execute() {
        global $sc_states;
        $id = null;
        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //店舗ユーザー作成
            case 'new_store_user':
                $params = array();
                $params['id'] = (is_null($this->request->getParam('id'))?'0':$this->request->getParam('id'));
                $params['user_login']   = $this->request->getParam('user_login');
                $params['pass1']        = $this->request->getParam('pass1');
                $params['email']        = $this->request->getParam('email');
                $params['display_name'] = $this->request->getParam('display_name');
                $params['url']          = $this->request->getParam('url');
                $params['pobox']        = $this->request->getParam('pobox');
                $params['address']      = $this->request->getParam('address');
                $params['street']       = $this->request->getParam('street');
                $params['city']         = $this->request->getParam('city');
                $params['state']        = $this->request->getParam('state');
                $params['zip']          = $this->request->getParam('zip');
                $params['country']      = $this->request->getParam('country');
                $params['tel']          = $this->request->getParam('tel');
                $params['fax']          = $this->request->getParam('fax');
                $params['mobile']       = $this->request->getParam('mobile');
                $params['role']         = $this->request->getParam('role');
                $this->model['results'] = SimpleCartAdminModel::saveStoreUser($params);
                $results = $this->model['results'];
                if (!is_object($results)) {
                    $id = $results;
                }
                //エラー時
                else {
                    $this->model['user']['user_login']   = $this->request->getParam('user_login');
                    $this->model['user']['pass1']        = $this->request->getParam('pass1');
                    $this->model['user']['email']        = $this->request->getParam('email');
                    $this->model['user']['display_name'] = $this->request->getParam('display_name');
                    $this->model['user']['url']          = $this->request->getParam('url');
                    $this->model['user']['address']      = $this->request->getParam('address');
                    $this->model['user']['street']       = $this->request->getParam('street');
                    $this->model['user']['state']        = $this->request->getParam('state');
                    $this->model['user']['zip']          = $this->request->getParam('zip');
                    $this->model['user']['tel']          = $this->request->getParam('tel');
                    $this->model['user']['fax']          = $this->request->getParam('fax');
                    $this->model['user']['role']         = $this->request->getParam('role');
                }
                break;
            //
            default:
                $id = $this->request->getParam('id');
                break;
        }
        $this->model['states'] = $sc_states;
        $this->model['roles'] = SimpleCartAdminModel::getRole();
        if (!is_null($id) && $id!='' && !is_object($id)) {
            $this->model['user'] = SimpleCartAdminModel::getStoreUser($id);
        }
        $this->exec('admin/store');
    }
}
