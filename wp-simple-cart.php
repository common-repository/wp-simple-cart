<?php
/*
Plugin Name: WP Simple Cart
Plugin URI: http://eos.exbridge.jp/projects/show/wp-simple-cart
Description: Simple Shopping Cart Plugin For <strong><font color="blue">WordPress Mu</font></strong>.<br/>Optional <strong>BuddyPress</strong><br/><font color="red"><strong>Do not Auto Upgrading!! When upgrading, Do not forget to back up the files,template,css,images folder.</strong></font>
Version: 1.0.15
Author: Exbridge,inc.
Author URI: http://exbridge.jp
*/

/*  Copyright 2009  Exbrige,inc.  (email : info@exbridge.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

///******************************************************************************
// * wp-personal-point - WordPress Interface Define
// *****************************************************************************/
define('PLUGIN_SIMPLE_CART',    WP_PLUGIN_DIR . '/wp-simple-cart');
define('BP_PLUGIN_SIMPLECART',  PLUGIN_SIMPLE_CART . '/bp-simplecart');

if (get_locale()=='ja'||get_locale()=='en') {
    require_once(PLUGIN_SIMPLE_CART . '/languages/language_' . get_locale() . '.php');
}
else {
    require_once(PLUGIN_SIMPLE_CART . '/languages/language_ja.php');
}
//require_once(PLUGIN_SIMPLE_CART . '/languages/language_en.php');
require_once(PLUGIN_SIMPLE_CART . '/SimpleCartDefine.php');
require_once(PLUGIN_SIMPLE_CART . '/SimpleCartGlobal.php');
require_once(PLUGIN_SIMPLE_CART . '/SimpleCartModel.php');
require_once(PLUGIN_SIMPLE_CART . '/_component/SimpleCart.php');
require_once(PLUGIN_SIMPLE_CART . '/_component/SimpleCartFunctions.php');
require_once(PLUGIN_SIMPLE_CART . '/_component/SimpleCartRequestVo.php');
require_once(PLUGIN_SIMPLE_CART . '/_component/logger/SimpleCartLogger.php');
require_once(PLUGIN_SIMPLE_CART . '/_component/functions.php');
require_once(PLUGIN_SIMPLE_CART . '/wp-simple-cart-public.php');

//動作モード用Global変数
$sc_mode;

//JS/CSS設定
add_action('wp_print_styles', array('SimpleCartFunctions', 'attachCss'));
add_action('wp_print_scripts', array('SimpleCartFunctions', 'attachJs'));

//ダッシュボードまたは管理パネルが表示されている
if (is_admin()) {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/admin/SimpleCartAdmin.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/admin/SimpleCartAdminModel.php');

    //カート機能用クラスのインスタンス化
    $sc_admin = & new SimpleCartAdmin();

    //プラグインアクティベートhook
    register_activation_hook(__FILE__, array(&$sc_admin, 'initialize'));

    //プラグインディアクティベートhook
    register_deactivation_hook(__FILE__, array(&$sc_admin, 'destroy'));

    //メニューhook
    add_action('admin_menu', array(&$sc_admin, 'addAdminMenu'));

    //バージョンアップ確認
    $sc_functions = & new SimpleCartFunctions();
    add_action('admin_menu', array(&$sc_functions, 'CheckVersion'));
}
else {
    require_once(PLUGIN_SIMPLE_CART . '/_contents/SimpleCartContents.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/SimpleCartCommonModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/admin/SimpleCartAdminModel.php');

    //動作モードの取得
    global $sc_mode;
    $sc_options = SimpleCartModel::getOptions();
    $sc_mode = $sc_options['sc_buddypress'];
    if ($sc_mode==SC_MODE_BP) {
        require_once(PLUGIN_SIMPLE_CART . '/bp-simplecart/bp-simplecart-store.php');
        require_once(PLUGIN_SIMPLE_CART . '/bp-simplecart/bp-simplecart-user.php');
    }

    //コンテンツ差替え
    add_filter('the_content', 'simplecart_contents_controller', 99);

    //タイトル行を強制的に消す
    add_filter('the_title', 'simplecart_title_controller', 99);

    //カートのタイムアウト確認
    SimpleCartFunctions::CartTimeoutCheck();
}

add_action('send_headers', 'sc_cart_headers');

add_filter('authenticate', 'sc_user_login', 99);

function sc_user_login($user) {
    if (isset($user)) {
        if ($user->user_status==0||$user->user_status==SC_USER_ENABLE) {
            return $user;
        }
    }
    return null;
}

function sc_cart_headers() {
    //@header("Cache-Control: private");
    //@header("Pragma: no-cahce");
    //@header("Expires: ");
    //@header("Last-Modified: ");
}
