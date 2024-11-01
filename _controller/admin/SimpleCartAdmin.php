<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartAdmin.php 140016 2009-07-28 06:57:23Z tajima $
 */

/******************************************************************************
 * SimpleCartAdmin
 * 
 * @author      Exbridge,inc.
 * @version     0.1
 * 
 *****************************************************************************/
class SimpleCartAdmin extends SimpleCart {

    /**
     * 有効化時の処理
     *
     */
    function initialize() {
        SimpleCartModel::initialyze();
    }

    /**
     * 無効化時の処理
     *
     */
    function destroy() {
        SimpleCartModel::destroy();
    }

    /**
     * 管理者用メニュー生成
     *
     */
    function addAdminMenu() {
        SimpleCartFunctions::attachMenus();
    }

    /**
     * Output Admin HTML Header
     *
     * @access private
     */
    function header() {
        ?>
        <div class='wrap'>
        <h2><?php _e(SCLNG_ADMIN, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * Output Admin HTML Footer
     *
     * @access private
     */
    function footer() {
        ?>
        </div>
        <?php
    }
}
