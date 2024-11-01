<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Functon Library
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartFunctions.php 155290 2009-09-16 11:08:12Z tajima $
 */

/******************************************************************************
 * SimpleCartFunctions
 * 
 * @author		Exbridge,inc.
 * @version		0.1
 * 
 *****************************************************************************/
class SimpleCartFunctions {

    /**
     * バージョンチェック（DBの変更等はここで吸収する予定）
     * 目論見が外れました。。。2009/07/26
     */
    function CheckVersion() {
        $sc_options = SimpleCartModel::getOptions();

        //0.9.0
        //新規公開バージョン
        if (!isset($sc_options['sc_version'])||is_null($sc_options['sc_version'])) {
            $sc_options['sc_version'] = '0.9.0';
        }
        //0.9.1
        if ($sc_options['sc_version'] < '0.9.1') {
            //0.9.1更新
            SimpleCartFunctions::Update091Version();
        }
        //1.0.0
        if ($sc_options['sc_version'] < '1.0.0') {
            //1.0.0更新
            SimpleCartFunctions::Update100Version();
        }
        //1.0.3
        if ($sc_options['sc_version'] < '1.0.3') {
            //1.0.3更新
            SimpleCartFunctions::Update103Version();
        }
        //1.0.4
        if ($sc_options['sc_version'] < '1.0.4') {
            //1.0.4更新
            SimpleCartFunctions::Update104Version();
        }
        //1.0.13
        if ($sc_options['sc_version'] < '1.0.13') {
            //1.0.13更新
            SimpleCartFunctions::Update113Version();
        }

        //最新を取得しなおし
        $sc_options = SimpleCartModel::getOptions();
        $sc_options['sc_version'] = SC_VERSION;
        SimpleCartModel::saveOptions($sc_options);
    }

    /**
     * バージョン0.9.1用の更新
     *
     */
    function Update091Version() {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        }

        $sc_options = SimpleCartModel::getOptions();
        $parent_id = $sc_options[SC_PGID_STORE_INFO];
        $user_parent_id = $sc_options[SC_PGID_USER_INFO];
        $mode = $sc_options['sc_buddypress'];

        //店舗会員用規格関連付け登録ページ生成
        if (!isset($sc_options[SC_PGID_STORE_VARIATION_RELATION])) {
            $post_data = array();
            $post_data['post_parent']  = $parent_id;
            $post_data['post_title']   = __(SCLNG_PAGE_STORE_VARIATION_RELATION, SC_DOMAIN);
            $post_data['post_content'] = '[SimpleCart_Store_Variation_Relation_Manage_Page]';
            $post_data['sc_page_id']   = SC_PGID_STORE_VARIATION_RELATION;
            SimpleCartModel::insertPage($post_data);
        }

        if ($mode!=SC_MODE_BP) {
            //店舗会員用商品レビュー管理ページ生成
            if (!isset($sc_options[SC_PGID_STORE_REVIEW])) {
                $post_data = array();
                $post_data['post_parent']  = $parent_id;
                $post_data['post_title']   = __(SCLNG_PAGE_STORE_REVIEW, SC_DOMAIN);
                $post_data['post_content'] = '[SimpleCart_Store_Review_Manage_Page]';
                $post_data['sc_page_id']   = SC_PGID_STORE_REVIEW;
                SimpleCartModel::insertPage($post_data);
            }
            //通常会員用お気に入りページ生成
            if (!isset($sc_options[SC_PGID_USER_FAVORITE])) {
                $post_data['post_parent']  = $user_parent_id;
                $post_data['post_title']   = __(SCLNG_PAGE_USER_FAVORITE, SC_DOMAIN);
                $post_data['post_content'] = '[SimpleCart_Favorite_Page]';
                $post_data['sc_page_id']   = SC_PGID_USER_FAVORITE;
                SimpleCartModel::insertPage($post_data);
            }
        }

        //商品規格用中間テーブル作成
        $table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
            $query = "CREATE TABLE {$table_name} (
                id               bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                store_id         bigint(20)  NOT NULL,
                product_id       bigint(20)  NOT NULL,
                min_stock        varchar(20) NOT NULL,
                max_stock        varchar(20) NOT NULL,
                min_price        varchar(32) NOT NULL,
                max_price        varchar(32) NOT NULL,
                download_publish varchar(1)  NOT NULL DEFAULT 0,
                KEY key_{$wpdb->blogid}_sc_tmp_product_variation (store_id, product_id)
                ) {$charset_collate};";
            dbDelta($query);
        }

        $variation_associations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_associations';
        $values_associations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values_associations';
        $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
        $combinations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';

        $wpdb->query($wpdb->prepare("alter table `{$priceandstock_table}` drop index `ukey_{$wpdb->blogid}_sc_variation_priceandstock`"));
        $wpdb->query($wpdb->prepare("alter table `{$priceandstock_table}` add index `key_`{$wpdb->blogid}`_sc_variation_priceandstock` (`store_id`, `product_id`)"));
        $wpdb->query($wpdb->prepare("alter table `{$combinations_table}` change `all_variation_id` `all_variation_id` varchar( 64 ) not null"));
        $wpdb->query($wpdb->prepare("alter table `{$values_associations_table}` drop `visible`"));
        $wpdb->query($wpdb->prepare("alter table `{$priceandstock_table}` add `visible` varchar( 1 ) not null"));
        $wpdb->query($wpdb->prepare("alter table `{$priceandstock_table}` change `stock` `stock` varchar( 20 ) not null"));
        $wpdb->query($wpdb->prepare("alter table `{$variation_associations_table}` add `download_publish` varchar( 1 ) not null default 0"));
        $wpdb->query($wpdb->prepare("alter table `{$values_associations_table}` add `download_publish` varchar( 1 ) not null default 0"));
        $wpdb->query($wpdb->prepare("alter table `{$priceandstock_table}` add `download_publish` varchar( 1 ) not null default 0"));
        $wpdb->query($wpdb->prepare("alter table `{$combinations_table}` add `download_publish` varchar( 1 ) not null default 0"));
        $wpdb->query($wpdb->prepare("alter table `{$order_detail_table}` add `product_cd` varchar( 20 ) not null after `download`"));
        $wpdb->query($wpdb->prepare("alter table `{$order_detail_table}` add `product_name` varchar( 200 ) not null after `product_cd`"));
        $wpdb->query($wpdb->prepare("alter table `{$order_detail_table}` add `variation_name` varchar( 200 ) null after `product_name`"));
    }

    /**
     * バージョン1.0.0用の更新
     *
     */
    function Update100Version() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

        $review_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';
        $wpdb->query($wpdb->prepare("alter table `{$review_table}` change `commnet` `comment` longtext character set utf8 collate utf8_general_ci not null"));
        $wpdb->query($wpdb->prepare("alter table `{$review_table}` drop index `ukey_1_sc_product_review`"));
        $wpdb->query($wpdb->prepare("alter table `{$review_table}` add index `key_1_sc_product_review` (`store_id`)"));
    }

    /**
     * バージョン1.0.3用の更新
     *
     */
    function Update103Version() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

        $favorite_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user_favorite';
        $wpdb->query($wpdb->prepare("alter table `{$favorite_table}` add `regist_date` datetime not null"));
    }

    /**
     * バージョン1.0.4用の更新
     *
     */
    function Update104Version() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $wpdb->query($wpdb->prepare("alter table `{$order_table}` add `deliver_name` text null after `deliver_id`"));
        $wpdb->query($wpdb->prepare("alter table `{$order_table}` add `deliver_value_name` text null after `deliver_value_id`"));
        $wpdb->query($wpdb->prepare("alter table `{$order_table}` add `paid_method_name` text not null after `paid_method`"));
        $wpdb->query($wpdb->prepare("alter table `{$order_table}` add `delivery_time_name` text not null after `delivery_time`"));
    }

    /**
     * バージョン1.0.13用の更新
     *
     */
    function Update113Version() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
        $wpdb->query($wpdb->prepare("alter table `{$order_detail_table}` drop index `ukey_1_sc_order_detail` "));
        $wpdb->query($wpdb->prepare("add index `key_1_sc_order_detail` (`order_id`,`product_id`) "));
    }

    /**
     * 管理者メニュー生成
     *
     */
    function attachMenus() {
        $sc_ad_base_file         = 'SimpleCartAdminBase.php';
        $sc_ad_store_manage_file = 'SimpleCartAdminStoreManage.php';
        $sc_ad_store_user_file   = 'SimpleCartAdminStoreUser.php';
        $sc_ad_store_list_file   = 'SimpleCartAdminStoreList.php';
        $sc_ad_zip_import_file   = 'SimpleCartAdminZipImport.php';

        require_once(PLUGIN_SIMPLE_CART . '/_controller/admin/' . $sc_ad_base_file);
        $sc_ad_base = & new SimpleCartAdminBase();

        require_once(PLUGIN_SIMPLE_CART . '/_controller/admin/' . $sc_ad_store_manage_file);
        $sc_ad_store_manage = & new SimpleCartAdminStoreManage();

        require_once(PLUGIN_SIMPLE_CART . '/_controller/admin/' . $sc_ad_store_user_file);
        $sc_ad_store_user = & new SimpleCartAdminStoreUser();

        require_once(PLUGIN_SIMPLE_CART . '/_controller/admin/' . $sc_ad_store_list_file);
        $sc_ad_store_list = & new SimpleCartAdminStoreList();

        require_once(PLUGIN_SIMPLE_CART . '/_controller/admin/' . $sc_ad_zip_import_file);
        $sc_ad_zip_import = & new SimpleCartAdminZipImport();

        //add top menu
        add_menu_page(__(SCLNG_MENU_ADMIN_STORE, SC_DOMAIN), __(SCLNG_MENU_ADMIN_STORE, SC_DOMAIN), 10, SC_PLUGIN_FILE, array(&$sc_ad_base, 'execute'), WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/cart.png');
        //add submenus
        add_submenu_page(SC_PLUGIN_FILE, __(SCLNG_MENU_ADMIN_STORE_MANAGE, SC_DOMAIN), __(SCLNG_MENU_ADMIN_STORE_MANAGE, SC_DOMAIN), 10, $sc_ad_store_manage_file, array(&$sc_ad_store_manage, 'execute'));
        add_submenu_page(SC_PLUGIN_FILE, __(SCLNG_MENU_ADMIN_STORE_USER_LIST, SC_DOMAIN), __(SCLNG_MENU_ADMIN_STORE_USER_LIST, SC_DOMAIN), 10, $sc_ad_store_list_file, array(&$sc_ad_store_list, 'execute'));
        add_submenu_page(SC_PLUGIN_FILE, __(SCLNG_MENU_ADMIN_STORE_USER, SC_DOMAIN), __(SCLNG_MENU_ADMIN_STORE_USER, SC_DOMAIN), 10, $sc_ad_store_user_file, array(&$sc_ad_store_user, 'execute'));
        add_submenu_page(SC_PLUGIN_FILE, __(SCLNG_MENU_ADMIN_ZIP_IMPORT, SC_DOMAIN), __(SCLNG_MENU_ADMIN_ZIP_IMPORT, SC_DOMAIN), 10, $sc_ad_zip_import_file, array(&$sc_ad_zip_import, 'execute'));
     }

    /**
     * HTML BuddyPress Menu (store)
     *
     */
    function storeBuddyPressMenu() {
        global $bp;

        if ($bp->displayed_user->id != $bp->loggedin_user->id) {
            return;
        }
        //roleの確認
        $u = new WP_User($bp->displayed_user->id);
        if (!$u->has_cap(SC_CAP)) {
            return;
        }

        $simplecart_store_link = $bp->loggedin_user->domain . $bp->simplecart_store->slug . '/';

        /* Add 'SimpleCart Store' to the main navigation */
        bp_core_add_nav_item(__(SCLNG_MENU_STORE, SC_DOMAIN), $bp->simplecart_store->slug, false, false);
        /* Set a specific sub nav item as the default when the top level item is clicked */
        bp_core_add_nav_default($bp->simplecart_store->slug, 'sc_store_info', SC_BPID_STORE_INFO);
        /* Create two sub nav items for this component */
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_STORE_INFO,         __(SCLNG_MENU_STORE_INFO, SC_DOMAIN),               $simplecart_store_link, 'sc_store_info');
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_ORDER,              __(SCLNG_MENU_STORE_ORDER, SC_DOMAIN),              $simplecart_store_link, 'sc_store_order_manage',              false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_PRODUCT_LIST,       __(SCLNG_MENU_STORE_PRODUCT_LIST, SC_DOMAIN),       $simplecart_store_link, 'sc_store_product_list',              false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_PRODUCT,            __(SCLNG_MENU_STORE_PRODUCT, SC_DOMAIN),            $simplecart_store_link, 'sc_store_product_register',          false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_VARIATION,          __(SCLNG_MENU_STORE_VARIATION, SC_DOMAIN),          $simplecart_store_link, 'sc_store_variation_manage',          false, bp_is_home());
        if ($bp->current_action==SC_BPID_VARIATION_RELATION) {
            bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_VARIATION_RELATION, __(SCLNG_MENU_STORE_VARIATION_RELATION, SC_DOMAIN), $simplecart_store_link, 'sc_store_variation_relation_manage', false, bp_is_home());
        }
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_DELIVERY,           __(SCLNG_MENU_STORE_DELIVERY, SC_DOMAIN),           $simplecart_store_link, 'sc_store_delivery_manage',           false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_TAX,                __(SCLNG_MENU_STORE_TAX, SC_DOMAIN),                $simplecart_store_link, 'sc_store_tax_manage',                false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_PAID,               __(SCLNG_MENU_STORE_PAID_METHOD, SC_DOMAIN),        $simplecart_store_link, 'sc_store_paid_manage',               false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_STORE_USER,         __(SCLNG_MENU_STORE_USER, SC_DOMAIN),               $simplecart_store_link, 'sc_store_user_manage',               false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_store->slug, SC_BPID_REVIEW,             __(SCLNG_MENU_STORE_REVIEW, SC_DOMAIN),             $simplecart_store_link, 'sc_store_review_manage',             false, bp_is_home());

        /* Only execute the following code if we are actually viewing this component */
        if ($bp->current_component == $bp->simplecart_store->slug) {
            if (bp_is_home()) {
                /* If the user is viewing their own profile area set the title to "My Simple Cart" */
                $bp->bp_options_title = __(SCLNG_MENU_STORE, SC_DOMAIN);
            }
            else {
                /* If the user is viewing someone elses profile area, set the title to "[user fullname]" */
                $bp->bp_options_avatar = bp_core_get_avatar($bp->displayed_user->id, 1);
                $bp->bp_options_title = $bp->displayed_user->fullname;
            }
        }
    }

    /**
     * HTML BuddyPress Menu (user)
     *
     */
    function userBuddyPressMenu() {
        global $bp;

        $simplecart_user_link = $bp->loggedin_user->domain . $bp->simplecart_user->slug . '/';

        /* Add 'SimpleCart Store' to the main navigation */
        bp_core_add_nav_item(__(SCLNG_MENU_USER, SC_DOMAIN), $bp->simplecart_user->slug, false, false);
        /* Set a specific sub nav item as the default when the top level item is clicked */
        bp_core_add_nav_default($bp->simplecart_user->slug, 'sc_user_cart', SC_BPID_CART);
        /* Create two sub nav items for this component */
        bp_core_add_subnav_item($bp->simplecart_user->slug, SC_BPID_USER_INFO,    __(SCLNG_MENU_USER_INFO, SC_DOMAIN),     $simplecart_user_link, 'sc_user_info');
        bp_core_add_subnav_item($bp->simplecart_user->slug, SC_BPID_CART,         __(SCLNG_MENU_USER_CART, SC_DOMAIN),     $simplecart_user_link, 'sc_user_cart',     false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_user->slug, SC_BPID_HISTORY,      __(SCLNG_MENU_USER_HISTORY, SC_DOMAIN),  $simplecart_user_link, 'sc_user_history',  false, bp_is_home());
        bp_core_add_subnav_item($bp->simplecart_user->slug, SC_BPID_FAVORITE,     __(SCLNG_MENU_USER_FAVORITE, SC_DOMAIN), $simplecart_user_link, 'sc_user_favorite', false, bp_is_home());

        /* Only execute the following code if we are actually viewing this component */
        if ($bp->current_component == $bp->simplecart_user->slug) {
            if (bp_is_home()) {
                /* If the user is viewing their own profile area set the title to "My Simple Cart" */
                $bp->bp_options_title = __(SCLNG_MENU_USER, SC_DOMAIN);
            }
            else {
                /* If the user is viewing someone elses profile area, set the title to "[user fullname]" */
                $bp->bp_options_avatar = bp_core_get_avatar($bp->displayed_user->id, 1);
                $bp->bp_options_title = $bp->displayed_user->fullname;
            }
        }
    }

    /**
     * HTML Normal Menu (store)
     *
     */
    function storeTabMenu($active=null) {
        $sc_options = SimpleCartModel::getOptions();
        $info_id         = $sc_options[SC_PGID_STORE_INFO];
        $order_id        = $sc_options[SC_PGID_STORE_ORDER];
        $product_list_id = $sc_options[SC_PGID_STORE_PRODUCT_LIST];
        $product_id      = $sc_options[SC_PGID_STORE_PRODUCT];
        $variation_id    = $sc_options[SC_PGID_STORE_VARIATION];
        $delivery_id     = $sc_options[SC_PGID_STORE_DELIVERY];
        $tax_id          = $sc_options[SC_PGID_STORE_TAX];
        $paid_id         = $sc_options[SC_PGID_STORE_PAID];
        $store_id        = $sc_options[SC_PGID_STORE_USER];
        $review_id       = $sc_options[SC_PGID_STORE_REVIEW];

        $link_info = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($info_id), 'value'=>__(SCLNG_MENU_STORE_INFO, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_INFO) {
            $link_info = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($info_id), 'value'=>__(SCLNG_MENU_STORE_INFO, SC_DOMAIN))));
        }

        $link_order = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($order_id), 'value'=>__(SCLNG_MENU_STORE_ORDER, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_ORDER) {
            $link_order = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($order_id), 'value'=>__(SCLNG_MENU_STORE_ORDER, SC_DOMAIN))));
        }

        $link_product_list = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($product_list_id), 'value'=>__(SCLNG_MENU_STORE_PRODUCT_LIST, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_PRODUCT_LIST) {
            $link_product_list = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($product_list_id), 'value'=>__(SCLNG_MENU_STORE_PRODUCT_LIST, SC_DOMAIN))));
        }

        $link_product = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($product_id), 'value'=>__(SCLNG_MENU_STORE_PRODUCT, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_PRODUCT) {
            $link_product = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($product_id), 'value'=>__(SCLNG_MENU_STORE_PRODUCT, SC_DOMAIN))));
        }

        $link_variation = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($variation_id), 'value'=>__(SCLNG_MENU_STORE_VARIATION, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_VARIATION) {
            $link_variation = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($variation_id), 'value'=>__(SCLNG_MENU_STORE_VARIATION, SC_DOMAIN))));
        }

        $link_delivery = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($delivery_id), 'value'=>__(SCLNG_MENU_STORE_DELIVERY, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_DELIVERY) {
            $link_delivery = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($delivery_id), 'value'=>__(SCLNG_MENU_STORE_DELIVERY, SC_DOMAIN))));
        }

        $link_tax = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($tax_id), 'value'=>__(SCLNG_MENU_STORE_TAX, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_TAX) {
            $link_tax = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($tax_id), 'value'=>__(SCLNG_MENU_STORE_TAX, SC_DOMAIN))));
        }

        $link_paid = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($paid_id), 'value'=>__(SCLNG_MENU_STORE_PAID_METHOD, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_PAID) {
            $link_paid = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($paid_id), 'value'=>__(SCLNG_MENU_STORE_PAID_METHOD, SC_DOMAIN))));
        }

        $link_store = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($store_id), 'value'=>__(SCLNG_MENU_STORE_USER, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_USER) {
            $link_store = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($store_id), 'value'=>__(SCLNG_MENU_STORE_USER, SC_DOMAIN))));
        }

        $link_review = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($review_id), 'value'=>__(SCLNG_MENU_STORE_REVIEW, SC_DOMAIN))));
        if ($active==SC_PGID_STORE_REVIEW) {
            $link_review = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($review_id), 'value'=>__(SCLNG_MENU_STORE_REVIEW, SC_DOMAIN))));
        }
        ?>
        <div class="tabmenu">
        <?php echo $link_info ?>
        <?php echo $link_order ?>
        <?php echo $link_product_list ?>
        <?php echo $link_product ?>
        <?php echo $link_variation ?>
        <?php echo $link_delivery ?>
        <?php echo $link_tax ?>
        <?php echo $link_paid ?>
        <?php echo $link_store ?>
        <?php echo $link_review ?>
        </div>
        <?php
    }

    /**
     * HTML Normal Menu (user)
     *
     */
    function userTabMenu($active=null) {
        $sc_options  = SimpleCartModel::getOptions();
        $info_id     = $sc_options[SC_PGID_USER_INFO];
        $cart_id     = $sc_options[SC_PGID_USER_CART];
        $history_id  = $sc_options[SC_PGID_USER_HISTORY];
        $favorite_id = $sc_options[SC_PGID_USER_FAVORITE];

        $link_info = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($info_id), 'value'=>__(SCLNG_MENU_USER_INFO, SC_DOMAIN))));
        if ($active==SC_PGID_USER_INFO) {
            $link_info = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($info_id), 'value'=>__(SCLNG_MENU_USER_INFO, SC_DOMAIN))));
        }

        $link_cart = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($cart_id), 'value'=>__(SCLNG_MENU_USER_CART, SC_DOMAIN))));
        if ($active==SC_PGID_USER_CART) {
            $link_cart = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($cart_id), 'value'=>__(SCLNG_MENU_USER_CART, SC_DOMAIN))));
        }

        $link_history = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($history_id), 'value'=>__(SCLNG_MENU_USER_HISTORY, SC_DOMAIN))));
        if ($active==SC_PGID_USER_HISTORY) {
            $link_history = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($history_id), 'value'=>__(SCLNG_MENU_USER_HISTORY, SC_DOMAIN))));
        }

        $link_favorite = sprintf("<span>%s</span>", $this->link(array('href'=>get_permalink($favorite_id), 'value'=>__(SCLNG_MENU_USER_FAVORITE, SC_DOMAIN))));
        if ($active==SC_PGID_USER_FAVORITE) {
            $link_favorite = sprintf("<span class='tab-selected'>%s</span>", $this->link(array('href'=>get_permalink($favorite_id), 'value'=>__(SCLNG_MENU_USER_FAVORITE, SC_DOMAIN))));
        }
        ?>
        <div class="tabmenu">
        <?php echo $link_info ?>
        <?php echo $link_cart ?>
        <?php echo $link_history ?>
        <?php echo $link_favorite ?>
        </div>
        <?php
    }

    /**
     * CSS設定
     *
     */
    function attachCss() {
        //wp_enqueue_style(SC_PLUGIN_NAME, WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/css/structure.css');
        $css = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/css/structure.css';
        echo "<link rel='stylesheet' href='{$css}' type='text/css' media='' />";

        $css = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/css/ie/ie6.css';
        echo "<!--[if IE 6]>";
        echo "<link rel='stylesheet' href='{$css}' type='text/css' media='screen' />";
        echo "<![endif]-->";
    }

    /**
     * JS設定
     *
     */
    function attachJs() {
        wp_enqueue_script('jquery');
        wp_enqueue_script(SC_PLUGIN_NAME, WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/js/simple-cart.js');
        wp_enqueue_script(SC_PLUGIN_NAME . '-json', WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/js/json2.js');
        wp_enqueue_script(SC_PLUGIN_NAME . '-upload', WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/js/ajaxupload.3.5.js');
        wp_enqueue_script(SC_PLUGIN_NAME . '-jquery-ui-block', WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/js/jquery.blockUI.js');
    }

    /**
     * 全てのカテゴリーを取得
     *
     */
    function getAllCategory() {
        if (!$cat_all=wp_cache_get('all_category_all', 'category')) {
            $cat_all = get_terms('category', 'fields=all&get=all');
            wp_cache_add('all_category_all', $cat_all, 'category' );
        }
        $cates = array();
        foreach ($cat_all as $cat) {
            $cates[$cat->term_id] = $cat;
        }
        return $cates;
    }

    /**
     * 指定したカテゴリーで子供を全て取得（再帰）
     *
     */
    function getCategoryList($cate) {
        $terms = SimpleCartFunctions::getAllCategory();
        $cates = array();
        foreach ($terms as $term) {
            if ($term->parent == $cate && $term->term_id > 1) {
                $child[] = SimpleCartFunctions::getCategoryList($term->term_id);
            }
        }
        $terms[$cate]->child = $child;
        return $terms[$cate];
    }

    /**
     * カテゴリーリストタグ出力（チェックボックス）
     *
     */
    function HTMLCategorys($categorys, $selected_categorys, $lv='', $prefix='') {
        $tags = array();
        $lv = $lv . "&nbsp;&nbsp;&nbsp;&nbsp;";
        if (is_array($categorys)) {
            foreach ($categorys as $category) {
                $checked = '';
                if (isset($selected_categorys[$category->term_id])) {
                    $checked = 'checked="checked"';
                }
                if ($category->parent==null||$category->parent==0) {
                    $lv = "";
                }
                $tag_info = array(
                                'type'    => 'checkbox',
                                'id'      => $prefix.'category_'.$category->term_id,
                                'name'    => $prefix.'categorys['.$category->term_id.']',
                                'value'   => $category->term_id,
                                'checked' => $checked
                            );
                $check_tag = SimpleCartFunctions::HTMLInput($tag_info);
                $tags[] = $lv . "{$check_tag} {$category->name}";
                if (isset($category->child)) {
                    $tags[] = SimpleCartFunctions::HTMLCategorys($category->child, $selected_categorys, $lv, $prefix);
                }
            }
        }
        return @implode("<br/>", $tags);
    }

    /**
     * カテゴリーリストタグ出力（リンク）
     *
     */
    function HTMLCategorysLink($categorys, $lv='') {
        $tags = array();
        $lv = $lv . "&nbsp;&nbsp;&nbsp;&nbsp;";
        if (is_array($categorys)) {
            foreach ($categorys as $category) {
                $checked = '';
                if (isset($selected_categorys[$category->term_id])) {
                    $checked = 'checked="checked"';
                }
                if ($category->parent==null||$category->parent==0) {
                    $lv = "";
                }
                $tag_info = array(
                                'href'  => site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_PRODUCT_LIST . '/?s_categorys='.$category->term_id),
                                'value' => $category->name
                            );
                $cnt = SimpleCartPublicModel::getProductList(array('s_categorys'=>$category->term_id), true);
                $check_tag = SimpleCartFunctions::HTMLLink($tag_info);
                $tags[] = "<div class='sc_top_data'>" . $lv . "{$check_tag} ({$cnt})</div>";
                if (isset($category->child)) {
                    $tags[] = SimpleCartFunctions::HTMLCategorysLink($category->child, $lv);
                }
            }
        }
        return @implode("", $tags);
    }

    /**
     * Output Variation HTML Tag
     *
     */
    function HTMLVariationValues($name=null, $results, $product, $product_variations, $product_variation_values, $variation_values) {
        //エラー判定
        if (isset($results)) {
            $err_price = $results->errors['price'];
            $err_stock = $results->errors['stock'];
        }

        if (is_array($product_variations)) {
            //選択済み規格でループ
            foreach ($product_variations as $key=>$val) {
                if (is_array($variation_values[$key])) {
                    //規格詳細でループ
                    foreach ($variation_values[$key] as $k=>$v) {
                        //自身を抹消（再帰用）
                        unset($product_variations[$key]);
                        //配列がなくなった（出力処理）
                        if (count($product_variations)==0) {
                            $name_td = '';
                            $id = '';
                            //規格１つの場合
                            if (count($name)==0) {
                                $name_td = '<td class="wd200">' . $v['name'] . '</td>';
                            }
                            //規格組合せの場合
                            else {
                                //規格詳細を連結
                                $nm = '';
                                foreach ($name as $n) {
                                    if ($nm=='') {
                                        $id = $n['id'];
                                        $nm = $n['name'];
                                    }
                                    else {
                                        $id = $id . '_' . $n['id'];
                                        $nm = $nm . ', ' . $n['name'];
                                    }
                                }
                                $name_td = '<td class="wd200">' . $nm . ', ' . $v['name'] . '</td>';
                            }
                            //IDを作成する
                            if ($id=='') {
                                $id = $v['id'];
                            }
                            else {
                                $id = $id . '_' . $v['id'];
                            }
                            $check_val = array();
                            $check_val['type']  = 'checkbox';
                            $check_val['id']    = 'values_visible['.$id.']';
                            $check_val['value'] = $id;
                            if (!isset($product_variation_values[$id])) {
                                $check_val['checked'] = '"checked"';
                            }
                            else if (isset($product_variation_values[$id]['visible']) && $product_variation_values[$id]['visible']=='1') {
                                $check_val['checked'] = '"checked"';
                            }
                            $price = $product['price'];
                            $stock = 0;
                            if (isset($product_variation_values[$id])) {
                                $price = $product_variation_values[$id]['price'];
                                $stock = $product_variation_values[$id]['stock'];
                            }
                            echo '<div style="display:none;">';
                            echo SimpleCartFunctions::HTMLInput($check_val);
                            echo '</div>';
                            echo '<tr>';
                            echo $name_td;
                            if ($product['stock_manage']=='1') {
                                echo '<td>' . SimpleCartFunctions::HTMLInput(array('id'=>'values_stock['.$id.']', 'value'=>$stock, 'class'=>'input_80')) . SimpleCartFunctions::HTMLError($err_stock[$id]) . '</td>';
                            }
                            echo '<td>' . SimpleCartFunctions::HTMLInput(array('id'=>'values_price['.$id.']', 'value'=>$price, 'class'=>'input_80')) . SimpleCartFunctions::HTMLError($err_price[$id]) . '</td>';
                            echo SimpleCartFunctions::HTMLInput(array('type'=>'hidden', 'id'=>'values_ids['.$id.']', 'value'=>$id, 'class'=>'input_40'));
                            echo '</tr>';
                        }
                        //配列がまだある（再帰するぜ）
                        else {
                            if (is_null($name)) {
                                $name = array();
                                $name[$key]['id']   = $v['id'];
                                $name[$key]['name'] = $v['name'];
                            }
                            else {
                                $name[$key]['id']   = $v['id'];
                                $name[$key]['name'] = $v['name'];
                            }
                            SimpleCartFunctions::HTMLVariationValues($name, $results, $product, $product_variations, $product_variation_values, $variation_values);
                        }
                    }
                }
                return;
            }
        }
    }

    /**
     * Output Cart Variation HTML Tag
     *
     */
    function HTMLCartVariationValues($name=null, $results, $product, $product_variations, $product_variation_values, $variation_values, $quantitys) {
        //エラー判定
        if (isset($results)) {
            $err_quantity = $results->errors['quantity'];
        }

        if (is_array($product_variations)) {
            //選択済み規格でループ
            foreach ($product_variations as $key=>$val) {
                if (is_array($variation_values[$key])) {
                    //規格詳細でループ
                    foreach ($variation_values[$key] as $k=>$v) {
                        //自身を抹消（再帰用）
                        unset($product_variations[$key]);
                        //配列がなくなった（出力処理）
                        if (count($product_variations)==0) {
                            $name_lbl = '';
                            $id = '';
                            //規格１つの場合
                            if (count($name)==0) {
                                $name_lbl = '<strong>' . $v['name'] . '</strong>';
                            }
                            //規格組合せの場合
                            else {
                                //規格詳細を連結
                                $nm = '';
                                foreach ($name as $n) {
                                    if ($nm=='') {
                                        $id = $n['id'];
                                        $nm = '<strong>' . $n['name'] . '</strong>';
                                    }
                                    else {
                                        $id = $id . '_' . $n['id'];
                                        $nm = $nm . ', <strong>' . $n['name'] . '</strong>';
                                    }
                                }
                                $name_lbl = $nm . ', <strong>' . $v['name'] . '</strong>';
                            }
                            //IDを作成する
                            if ($id=='') {
                                $id = $v['id'];
                            }
                            else {
                                $id = $id . '_' . $v['id'];
                            }
                            $price = $product['price'];
                            if (isset($product_variation_values[$id])) {
                                $dl_pub = $product_variation_values[$id]['download_publish'];
                                if ($dl_pub==0) {
                                    $price = $product_variation_values[$id]['price'] - $product['off'];
                                }
                                else {
                                    $price = $product_variation_values[$id]['price'] - $product['download_off'];
                                }
                                $stock = $product_variation_values[$id]['stock'];
                                $pricestock_id = $product_variation_values[$id]['pricestock_id'];
                            }
                            $quantity = 1;
                            if (!is_null($quantitys[$id]) && $quantitys[$id]!='') {
                                $quantity = $quantitys[$id];
                            }

                            echo '<div class="pd_buy_row">';
                            echo '<div class="pd_buy_lbl">';
                            echo $name_lbl;
                            echo '</div>';
                            echo '<div class="pd_buy_price">';
                            _e(SCLNG_PUBLIC_PRODUCT_DETAIL_VARIATION_PRICE, SC_DOMAIN);
                            echo SimpleCartFunctions::MoneyFormat($price);
                            if ($product['stock_manage']=='1'&& $dl_pub==0 ) {
                                echo '<br/>';
                                _e(sprintf(SCLNG_PUBLIC_PRODUCT_DETAIL_VARIATION_STOCK, $stock), SC_DOMAIN);
                            }
                            echo '</div>';
                            echo '<div class="pd_buy_quantity">';

                            if ($product['stock_manage']=='1' && $dl_pub==0 && $stock==0) {
                                echo _e(SCLNG_PUBLIC_PRODUCT_DETAIL_LACK_STOCK, SC_DOMAIN);
                            }
                            else {
                                echo SimpleCartFunctions::HTMLInput(array('id'=>'quantity_'.$dl_pub.'_'.$id, 'name'=>'quantitys_'.$dl_pub.'['.$id.']', 'value'=>$quantity, 'class'=>'input_40'));
                                echo SimpleCartFunctions::HTMLSubmit(array('id'=>'buy_delivery_'.$dl_pub.'_'.$id, 'onclick'=>"
                                        Javascript:
                                            document.getElementById('download').value = ".$dl_pub.";
                                            document.getElementById('pricestock_id').value = ".$pricestock_id.";
                                            document.getElementById('quantity').value = document.getElementById('quantity_".$dl_pub."_".$id."').value;
                                            document.getElementById('error_id').value = '".$dl_pub."_".$id."';
                                            ", 'class'=>'button_buy', 'value'=>__(SCLNG_BUY_NOW, SC_DOMAIN)));
                                echo SimpleCartFunctions::HTMLError($err_quantity[$dl_pub."_".$id]);
                            }
                            echo '</div>';
                            echo '</div>';
                        }
                        //配列がまだある（再帰するぜ）
                        else {
                            if (is_null($name)) {
                                $name = array();
                                $name[$key]['id']   = $v['id'];
                                $name[$key]['name'] = $v['name'];
                            }
                            else {
                                $name[$key]['id']   = $v['id'];
                                $name[$key]['name'] = $v['name'];
                            }
                            SimpleCartFunctions::HTMLCartVariationValues($name, $results, $product, $product_variations, $product_variation_values, $variation_values, $quantitys);
                        }
                    }
                }
                return;
            }
        }
    }

    /**
     * Output Input HTML Tag
     *
     */
    function HTMLInput ($params) {
        if (!isset($params['type'])) {
            $params['type'] = 'text';
        }
        if (!isset($params['name'])) {
            if (isset($params['id'])) {
                $params['name'] = $params['id'];
            }
        }
        $ck = $params['checked'];
        unset($params['checked']);
        $str = '';
        foreach ($params as $k=>$v) {
            $str .= " {$k}=\"{$v}\"";
        }
        return "<input {$str} {$ck} />";
    }

    /**
     * Output Submit HTML Tag
     *
     */
    function HTMLSubmit ($params) {
        if (!isset($params['name'])) {
            if (isset($params['id'])) {
                $params['name'] = $params['id'];
            }
        }
        $val = $params['value'];
        unset($params['value']);
        $str = '';
        foreach ($params as $k=>$v) {
            $str .= " {$k}=\"{$v}\"";
        }
        return "<button type='submit' {$str} >{$val}</button>";
    }

    /**
     * Output Textarea HTML Tag
     *
     */
    function HTMLText ($params) {
        if (!isset($params['name'])) {
            if (isset($params['id'])) {
                $params['name'] = $params['id'];
            }
        }
        $val = $params['value'];
        unset($params['value']);
        $str = '';
        foreach ($params as $k=>$v) {
            $str .= " {$k}=\"{$v}\"";
        }
        return "<textarea {$str} >{$val}</textarea>";
    }

    /**
     * Output Select HTML Tag
     *
     */
    function HTMLSelect ($params, $options) {
        if (!isset($params['name'])) {
            $params['name'] = $params['id'];
        }
        $str = '';
        foreach ($params as $k=>$v) {
            $str .= " {$k}=\"{$v}\"";
        }
        $prm = '';
        if (isset($options['params'])) {
            foreach ($options['params'] as $k=>$v) {
                $prm .= " {$k}=\"{$v}\"";
            }
        }
        $opn = '';
        foreach ($options['list'] as $k=>$v) {
            $selected = '';
            if (isset($options['default'])&&!is_null($options['default'])&&$options['default']!='') {
                if ($k == $options['default']) {
                    $selected = "selected=\"selected\"";
                }
            }
            $opn.= "<option value='{$k}' {$prm} {$selected}>{$v}</option>";
        }
        return "<select {$str} >{$opn}</select>";
    }

    /**
     * Output Link HTML Tag
     *
     */
    function HTMLLink ($params) {
        $val = $params['value'];
        unset($params['value']);
        $str = '';
        foreach ($params as $k=>$v) {
            $str .= " {$k}=\"{$v}\"";
        }
        return "<a {$str} >{$val}</a>";
    }

    /**
     * Output Error HTML Tag
     *
     */
    function HTMLError($param) {
        return ($param=='')?'':'<br/><font color="red">'.$param.'</font>';
    }

    /**
     * Output Ymd HTML Tag
     *
     */
    //==================================================================
    //データ取得部分
    //引数
    //$params['year_id']      : リストボックス年ＩＤ
    //$params['month_id']     : リストボックス月ＩＤ
    //$params['day_id']       : リストボックス日ＩＤ
    //$params['min_year']     : 最小年
    //$params['max_year']     : 最大年
    //$params['year']         : 年
    //$params['month']        : 月
    //$params['day']          : 日
    //$params['year_options'] : 年のリストボックスのオプション
    //$params['month_options']: 月のリストボックスのオプション
    //$params['day_options']  : 日のリストボックスのオプション
    //==================================================================
    function HTMLYyyymmdd($params) {
        $yy_id  = $params['year_id'];
        $mm_id  = $params['month_id'];
        $dd_id  = $params['day_id'];
        $yy_val = $params['year'];
        $mm_val = $params['month'];
        $dd_val = $params['day'];

        //現在年月日
        $tm = time();
        $current_yy = date('Y', $tm);
        $current_mm = date('m', $tm);
        $current_dd = date('d', $tm);

        //開始年が指定されていない場合、現在年-9を開始年とする。
        $min_yy = !isset($params['min_year'])?($current_yy - 3):$params['min_year'];
        //終了年が指定されていない場合、現在年+1を終了年とする。
        $max_yy = !isset($params['max_year'])?($current_yy + 1):$params['max_year'];

        //年リスト
        $yy_list = array();
        $yy_list[] = '';
        for ($y=$min_yy; $y<=$max_yy; $y++) {
            $yy_list[$y] = $y;
        }
        echo SimpleCartFunctions::HTMLSelect(array('id'=>$yy_id), array('list'=>$yy_list));
        echo '年';

        //月リスト
        $mm_list = array();
        $mm_list[] = '';
        for ($m=1; $m<=12; $m++) {
            $mm_list[$m] = SimpleCartFunctions::LPAD($m, 2);
        }
        echo SimpleCartFunctions::HTMLSelect(array('id'=>$mm_id), array('list'=>$mm_list));
        echo '月';

        //日リスト
        $dd_list = array();
        $dd_list[] = '';
        echo SimpleCartFunctions::HTMLSelect(array('id'=>$dd_id), array('list'=>$dd_list));
        echo '日';

        $js_function_create_dd_list = 'create_dd_list_' . $yy_id;
        ?>
        <script type="text/javascript" charset="utf-8">
        jQuery(function() {
            jQuery("#<?php echo $yy_id;?>")
                .change(function() {
                    document.getElementById('<?php echo $dd_id?>').length = 0;
                    setTimeout("<?php echo $js_function_create_dd_list?>('" + jQuery("#<?php echo $dd_id;?>").val() + "');", 100);
                }).val("<?php echo $yy_val;?>");
            jQuery("#<?php echo $mm_id;?>")
                .change(function() {
                    document.getElementById('<?php echo $dd_id?>').length = 0;
                    setTimeout("<?php echo $js_function_create_dd_list?>('" + jQuery("#<?php echo $dd_id;?>").val() + "');", 100);
                }).val("<?php echo $mm_val;?>");

            document.getElementById('<?php echo $dd_id?>').length = 0;
            setTimeout("<?php echo $js_function_create_dd_list?>('<?php echo $dd_val;?>');", 100);
        });

        /**
         * 年月日：日リストを作成
         *
         */
        function <?php echo $js_function_create_dd_list;?>(dd) {
            var yy = jQuery("#<?php echo $yy_id;?>").val();
            var mm = jQuery("#<?php echo $mm_id;?>").val();
            if (yy == '' || mm == '') {
                return true;
            }
            var dt = new Date(yy, mm, 0);
            var day_cnt = dt.getDate();

            var option_arr = new Array();
            option_arr[0] = "<option value=''></option>";
            for (var i=1; i<=day_cnt; i++) {
                var did = i;
                var dvl = i;
                if (i<10) {
                    dvl = '0'+ i;
                }
                var selected = "";
                if (did == dd) {
                    selected = "selected";
                }
                option_arr[i] = "<option value='" + did + "' " + selected + ">" + dvl + "</option>";
            }
            jQuery('#<?php echo $dd_id?>').append(option_arr.join('\n'));
        }
        </script>
        <?php
    }

    /**
     * Output Page HTML Tag
     *
     * $params
     * 'total-count':total count
     */
    function HTMLPage ($params) {
        if (!isset($params['total-count'])) {
            return '';
        }

        //REQUEST_URIを解析
        $uris = @explode("?", $_SERVER['REQUEST_URI']);
        $base_url = $uris[0];
        $query_params = array();
        if (count($uris)>1) {
            $querys = @explode("&", $uris[1]);
            $query_params = array();
            foreach ($querys as $query) {
                $tmp_params = @explode("=", $query);
                $query_params[$tmp_params[0]] = $tmp_params[1];
            }
        }
        $now_page = 1;
        if (isset($query_params['pager_id'])) {
            $now_page = $query_params['pager_id'];
        }
        unset($query_params['pager_id']);

        //POSTデータから条件を設定する「s_」を対象とする仕様。
        foreach ($_POST as $key=>$val) {
            if (substr($key, 0 ,2)=='s_') {
                if (is_array($val)) {
                    $i=0;
                    foreach ($val as $k=>$v) {
                        if (is_null($k)) {
                            $k = $i++;
                        }
                        $str = $key."[".$k."]";
                        $query_params[$str] = htmlentities($v);
                    }
                }
                else {
                    $query_params[$key] = htmlentities($val);
                }
            }
        }

        //再構成
        $base_url = site_url($base_url);
        if (count($query_params)>0) {
            $querys = array();
            foreach ($query_params as $key=>$val) {
                $querys[] = $key . '=' . $val;
            }
            $base_url = $base_url . "?" . @implode('&', $querys);
        }

        //全ページ数を算出
        $total_page = ceil($params['total-count'] / SC_PAGE_COUNT);
        $total_pages = array();
        for ($i=1; $i<=$total_page; $i++) {
            $total_pages[$i] = $i;
        }
        //現在ページから最大５つページ番号を表示するための制御
        $page_data = array();
        for ($i=($now_page-5); $i<=($now_page+5); $i++) {
            if (isset($total_pages[$i])) {
                $page_data[$i] = $base_url . '?pager_id=' . $i;
                if (count($query_params)>0) {
                    $page_data[$i] = $base_url . '&pager_id=' . $i;
                }
            }
        }

        //固定部生成
        $next_page = $now_page + 1;
        if ($next_page > $total_page) {
            $next_page = $total_page;
        }
        $prev_page = $now_page - 1;
        if ($prev_page < 1) {
            $prev_page = 1;
        }
        $link1 = $base_url . '?pager_id=1';
        if (count($query_params)>0) {
            $link1 = $base_url . '&pager_id=1';
        }
        $link2 = $base_url . '?pager_id=' . $prev_page;
        if (count($query_params)>0) {
            $link2 = $base_url . '&pager_id=' . $prev_page;
        }
        $link3 = $base_url . '?pager_id=' . $next_page;
        if (count($query_params)>0) {
            $link3 = $base_url . '&pager_id=' . $next_page;
        }
        $link4 = $base_url . '?pager_id=' . $total_page;
        if (count($query_params)>0) {
            $link4 = $base_url . '&pager_id=' . $total_page;
        }

        $page_html = array();
        if ($total_page > 0) {
            $page_html[] = SimpleCartFunctions::HTMLLink(array('href'=>$link1, 'value'=>__(SCLNG_PAGER_FIRST, SC_DOMAIN)));
            $page_html[] = SimpleCartFunctions::HTMLLink(array('href'=>$link2, 'value'=>__(SCLNG_PAGER_PREV, SC_DOMAIN)));
            foreach ($page_data as $key=>$data) {
                if ($key == $now_page) {
                    $page_html[] = $key;
                }
                else {
                    $page_html[] = SimpleCartFunctions::HTMLLink(array('href'=>$data, 'value'=>$key));
                }
            }
            $page_html[] = SimpleCartFunctions::HTMLLink(array('href'=>$link3, 'value'=>__(SCLNG_PAGER_NEXT, SC_DOMAIN)));
            $page_html[] = SimpleCartFunctions::HTMLLink(array('href'=>$link4, 'value'=>__(SCLNG_PAGER_LAST, SC_DOMAIN)));
        }
        $page_html[] = __(SCLNG_PAGER_TOTAL, SC_DOMAIN);
        $page_html[] = ":";
        $page_html[] = $params['total-count'];
        return @implode(' ', $page_html);
    }

    /**
     * Output Hidden HTML Tag's
     *
     */
    function EchoHTMLHiddens ($params) {
        if (is_array($params)) {
            foreach ($params as $key=>$val) {
                if (!is_array($val)) {
                    echo SimpleCartFunctions::HTMLInput(array('type'=>'hidden', 'id'=>$key, 'value'=>$val));
                }
                else {
                    foreach ($val as $k=>$v) {
                        $kname = $key . '[' . $k . ']';
                        echo SimpleCartFunctions::HTMLInput(array('type'=>'hidden', 'id'=>$key.'_'.$k, 'id'=>$kname, 'value'=>$v));
                    }
                }
            }
        }
    }

    /**
     * リダイレクト
     *
     */
    function Redirect($uris) {
        global $sc_mode, $current_user;
        $sc_redirect = $uris['_sc_redirect'];
        unset($uris['_sc_redirect']);
        if ($sc_mode==SC_MODE_BP && isset($uris['bp-slug-main'])) {
        //if ($sc_mode==SC_MODE_BP) {
            unset($uris['mu-slug-main']);
            unset($uris['mu-slug-sub']);
            $url = site_url('members/' . $current_user->user_login . '/' . @implode('/', $uris) . $sc_redirect);
            bp_core_redirect($url);
        }
        else {
            //リダイレクトしているように見せかける
            unset($uris['bp-slug-main']);
            unset($uris['bp-slug-sub']);
            $url = site_url(@implode('/', $uris) . $sc_redirect);
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function() {
                location.href = '<?php echo $url ?>'
            });
            </script>
            <?php
        }
    }

    /**
     * 乱数発生
     *
     */
    function Rand8() {
        mt_srand((double) microtime() * 1000000);
        return substr(mt_rand(), 0, 8);
    }

    /**
     * 乱数発生
     *
     */
    function RandMd5() {
        mt_srand((double) microtime() * 1000000);
        return md5(sha1(mt_rand()));
    }

    /**
     * イメージ一時保存用URL取得
     *
     */
    function ImageTemporaryUrl($prefix) {
        global $current_user;
        $image_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/files/' . $current_user->ID . '/temporary/' . $prefix . '/';
        return $image_url;
    }

    /**
     * 製品イメージ保存用URL取得
     *
     */
    function ProductImageUrl($product_id=null, $seq, $file, $user=null, $prefix=null) {
        global $current_user;

        $rand = SimpleCartFunctions::Rand8();

        if (is_null($user)) {
            $user = $current_user->ID;
        }
        if (is_null($file) || $file=='') {
            return SimpleCartFunctions::NoImageUrl($seq);
        }
        if (file_exists(SimpleCartFunctions::ProductImageDir($product_id, $seq, $user) . '/' . $file)) {
            return WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/files/' . $user . '/P_' . SimpleCartFunctions::LPAD($product_id, 6) . '/' . $seq . '/' . $file . '?' . $rand;
        }
        else {
            if (is_null($product_id) && !is_null($prefix)) {
                return SimpleCartFunctions::ImageTemporaryUrl($prefix) . $file;
            }
            else if (is_null($prefix)) {
                return SimpleCartFunctions::NoImageUrl($seq);
            }
            else {
                return SimpleCartFunctions::ImageTemporaryUrl($prefix) . $file . '?' . $rand;
            }
        }
    }

    /**
     * ダウンロード製品保存用URL取得
     *
     */
    function DownloadPoductUrl($product_id, $file, $user=null) {
        global $current_user;
        if (is_null($user)) {
            $user = $current_user->ID;
        }
        return WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/files/' . $user . '/P_' . SimpleCartFunctions::LPAD($product_id, 6) . '/download_product/' . $file;
    }

    /**
     * NoImage URL取得
     *
     */
    function NoImageUrl($seq) {
        return WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/noimage_' . $seq . '.jpg';
    }

    /**
     * イメージ製品保存用ディレクトリ取得
     *
     */
    function ProductImageDir($product_id, $seq, $user=null) {
        global $current_user;
        if (is_null($user)) {
            $user = $current_user->ID;
        }

        $image_dir1 = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/' . $user;
        $image_dir2 = $image_dir1 . '/P_' . SimpleCartFunctions::LPAD($product_id, 6);
        $image_dir3 = $image_dir2 . '/' . $seq;

        SimpleCartFunctions::Mkdir($image_dir1);
        SimpleCartFunctions::Mkdir($image_dir2);
        SimpleCartFunctions::Mkdir($image_dir3);
        return $image_dir3;
    }

    /**
     * ダウンロード製品保存用ディレクトリ取得
     *
     */
    function DownloadPoductDir($product_id) {
        global $current_user;

        $down_dir1 = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/' . $current_user->ID;
        $down_dir2 = $down_dir1 . '/P_' . SimpleCartFunctions::LPAD($product_id, 6);
        $down_dir3 = $down_dir2 . '/download_product';

        SimpleCartFunctions::Mkdir($down_dir1);
        SimpleCartFunctions::Mkdir($down_dir2);
        SimpleCartFunctions::Mkdir($down_dir3);
        return $down_dir3;
    }

    /**
     * 一時保存用ディレクトリ取得
     *
     */
    function TemporaryDir($prefix) {
        global $current_user;

        $user_dir = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/' . $current_user->ID;
        $temp_dir = $user_dir . '/temporary';
        $rand_dir = $temp_dir . '/' . $prefix;

        //ディレクトリ存在確認
        SimpleCartFunctions::Mkdir($user_dir);
        SimpleCartFunctions::Mkdir($temp_dir);
        SimpleCartFunctions::Mkdir($rand_dir);
        return $rand_dir;
    }

    /**
     * フォルダを作成する
     *
     */
    function Mkdir($dir) {
        if (!is_dir($dir)) {
            mkdir($dir);
            chmod($dir, 0755);
        }
    }

    /**
     * ファイルを移動する
     *
     */
    function Move($from, $to) {
        if (file_exists($from)) {
            @unlink($to);
            if (@copy($from, $to)) {
                @unlink($from);
                return true;
            }
            else {
                return false;
            }
        }
        return false;
    }

    /**
     * ファイルをフォルダごと削除
     *
     */
    function Rm($file) {
        require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/pear/System.php');
        System::rm("-r " . $file);
    }

    /**
     * 左0埋め
     *
     */
    function LPAD($val1, $val2){
        return str_pad($val1, $val2, '0', STR_PAD_LEFT);
    }

    /**
     * 税込み価格を取得する
     *
     */
    function Price($params) {
        return SimpleCartModel::calcPrice($params);
    }

    /**
     * 税込み価格（OFF考慮）を取得する
     *
     */
    function OffPrice($params) {
        return SimpleCartModel::calcOffPrice($params);
    }

    /**
     * 税込み価格を取得する（ダウンロード製品）
     *
     */
    function DownloadPrice($params) {
        return SimpleCartModel::calcDownloadPrice($params);
    }

    /**
     * 税込み価格（OFF考慮）を取得する（ダウンロード製品）
     *
     */
    function DownloadOffPrice($params) {
        return SimpleCartModel::calcDownloadOffPrice($params);
    }

    /**
     * 各種価格を計算する
     *
     */
    function CalcPrices(&$product) {
        $product['calc_price'] = SimpleCartFunctions::Price($product);
        $product['calc_off_price'] = SimpleCartFunctions::OffPrice($product);
        $product['calc_download_price'] = SimpleCartFunctions::DownloadPrice($product);
        $product['calc_download_off_price'] = SimpleCartFunctions::DownloadOffPrice($product);

        //規格連携の場合の上下限値で計算
        $calc = array();
        $calc['store_id'] = $product['store_id'];
        $calc['notax'] = $product['notax'];
        $calc['off'] = $product['off'];
        $calc['download_off'] = $product['download_off'];
        //通常製品（通常価格）
        $calc['price'] = $product['min_price'];
        $product['calc_min_price'] = SimpleCartFunctions::Price($calc);
        $calc['price'] = $product['max_price'];
        $product['calc_max_price'] = SimpleCartFunctions::Price($calc);
        //通常製品（値引価格）
        $calc['price'] = $product['min_price'];
        $product['calc_min_off_price'] = SimpleCartFunctions::OffPrice($calc);
        $calc['price'] = $product['max_price'];
        $product['calc_max_off_price'] = SimpleCartFunctions::OffPrice($calc);

        //ダウンロード製品（通常価格）
        $calc['download_price'] = $product['download_min_price'];
        $product['calc_download_min_price'] = SimpleCartFunctions::DownloadPrice($calc);
        $calc['download_price'] = $product['download_max_price'];
        $product['calc_download_max_price'] = SimpleCartFunctions::DownloadPrice($calc);
        //ダウンロード製品（値引価格）
        $calc['download_off_price'] = $product['download_min_price'];
        $product['calc_download_min_off_price'] = SimpleCartFunctions::DownloadOffPrice($calc);
        $calc['download_off_price'] = $product['download_max_price'];
        $product['calc_download_max_off_price'] = SimpleCartFunctions::DownloadOffPrice($calc);
    }

    /**
     * 消費税計算
     *
     */
    function CalcTax($price=0, $tax=0, $method=1) {
        //税計算
        $cost = $price + ($price * ($tax / 100));
        switch ($method) {
            case '1'://四捨五入
                $cost = round($cost);
                break;
            case '2'://切上げ
                $cost = ceil($cost);
                break;
            case '3'://切捨て
                $cost = floor($cost);
                break;
        }
        return $cost;
    }

    /**
     * テンプレートコンバーター
     *
     */
    function TemplateConvert($template_file=null, $model=null) {
        $file = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/template/' . $template_file;
        if (file_exists($file)) {
            ob_start();
            include($file);
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
    }

    /**
     * カートタイムアウト確認
     *
     */
    function CartTimeoutCheck() {
        SimpleCartModel::cartTimeout();
    }

    /**
     * 新着商品の判定
     *
     */
    function NewValid($regist_date) {
        $sc_option = SimpleCartModel::getOptions();
        $new_day = $sc_option['sc_new'];
        if (isset($new_day)) {
            if (!is_null($new_day) && $new_day!='' && $new_day!=0) {
                $tm = time();
                $yy = date('Y', $tm);
                $mm = date('m', $tm);
                $dd = date('d', $tm);
                $new_limit = date('Ymd', mktime(0, 0, 0, $mm, ($dd-$new_day), $yy));
                $regist_dt = substr(str_replace('-', '', $regist_date), 0, 8);
                if ($regist_dt >= $new_limit) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 納品書PDF作成
     *
     */
    function CreateOrderPDF($order_id) {
        //set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/fpdf');
        define('FPDF_FONTPATH', PLUGIN_SIMPLE_CART . '/_component/fpdf/font/');
        require_once(PLUGIN_SIMPLE_CART . '/_component/fpdf/japanese.php');
        require_once(PLUGIN_SIMPLE_CART . '/_component/SimpleCartInvoicePDF.php');

        $sc_pdf = new SimpleCartInvoicePDF($order_id);
        $sc_pdf->create();
    }

    /**
     * 金額フォーマット
     *
     */
    function MoneyFormat($val) {
        //return number_format($val);
        return sprintf(SC_PRICE_FORMAT, number_format($val));
    }
}
