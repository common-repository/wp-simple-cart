<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Base Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartAdminBase.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartAdminBase extends SimpleCartAdmin {

    /**
     * Base Controller
     *
     */
    function execute() {
        //----------------------------------------------------------------
        //処理の振分け
        //----------------------------------------------------------------
        switch ($this->request->getParam('sc_action')) {
            //----------------------------------------------------------------
            //設定情報の保存処理
            //----------------------------------------------------------------
            case 'setting':
                $params = array();
                $params['sc_timeout'] = $this->request->getParam('sc_timeout');
                $params['sc_new'] = $this->request->getParam('sc_new');
                $this->model['results'] = SimpleCartAdminModel::checkOptions($params);
                if ($this->model['results']===true) {
                    //ページ生成
                    $sc_option = SimpleCartModel::getOptions();
                    $sc_buddypress = $this->request->getParam('sc_buddypress');
                    if ($sc_option['sc_buddypress']!=$sc_buddypress) {
                        if ($sc_buddypress==1) {
                            SimpleCartModel::deletePostPage();
                        }
                        else {
                            SimpleCartModel::addPostPage();
                        }
                    }
                    //最新取得
                    $sc_option = SimpleCartModel::getOptions();
                    $sc_option['sc_categorys'] = $this->request->getParam('sc_categorys');
                    $sc_option['sc_buddypress'] = $this->request->getParam('sc_buddypress');
                    $sc_option['sc_timeout'] = intval($this->request->getParam('sc_timeout'));
                    $sc_option['sc_new'] = intval($this->request->getParam('sc_new'));
                    SimpleCartModel::saveOptions($sc_option);
                }
            //----------------------------------------------------------------
            //
            //----------------------------------------------------------------
            default:
                $this->model['sc_options'] = SimpleCartModel::getOptions();
                $this->model['exists_bp'] = SimpleCartModel::existsBuddyPress();
                $this->exec('admin/base');
                break;
        }
    }
}
