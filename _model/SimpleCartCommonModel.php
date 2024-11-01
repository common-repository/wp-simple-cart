<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Common Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartCommonModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartCommonModel {

    var $user_ID;   //ログインユーザーID
    var $mode;      //動作モード変数

    /**
     * Constructor
     *
     */
    function SimpleCartCommonModel() {
        global $current_user, $sc_mode;
        $this->mode = $sc_mode;
        $this->user_ID = $current_user->ID;
    }

    /**
     * カテゴリー一覧取得
     *
     */
    function getCategorys() {

        $sc_options = SimpleCartModel::getOptions();
        $categorys = $sc_options['sc_categorys'];
        $cates = array();
        foreach ($categorys as $category) {
            $cates[] = SimpleCartFunctions::getCategoryList($category);
        }
        return $cates;
    }
}
