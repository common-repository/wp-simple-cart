<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartModel.php 140849 2009-07-30 09:07:35Z tajima $
 */

/******************************************************************************
 * SimpleCartModel
 * 
 * @author		Exbridge,inc.
 * @version		0.1
 * 
 *****************************************************************************/
class SimpleCartModel {

    /**
     * Initialyze Simple Cart
     */
    function initialyze() {
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

        //Simple Cart用オプションの追加
        $params = array();
        $params['sc_categorys'][1] = 1;//デフォルトで「Uncategorized」を選択状態にする
        $params['sc_buddypress'] = 0;  //デフォルトで使用していないを選択状態にする
        $params['sc_timeout'] = 1440;  //デフォルトで24時間にする
        $params['sc_new'] = 3;         //デフォルトで3日間にする
        $params['sc_version'] = SC_VERSION;//1.0.0より追加
        add_option('sc_options', $params);

        //Simple Cart用テーブルの作成
        SimpleCartModel::createTables();

        //SimpleCart用ロールの作成
        //SimpleCartModel::addRole();

        //SimpleCart用店舗会員／通常会員専用ページの作成
        SimpleCartModel::addPostPage();

        //SimpleCart用商品情報公開ページの作成
        SimpleCartModel::addPublicPage();

        //たぶんよくないが。。。
        SimpleCartModel::fromTempFiles();
    }

    /**
     * Destoroy Simple Cart
     */
    function destroy() {

        //Simple Cart用店舗会員の削除
        //SimpleCartModel::deleteMembers();

        //SimpleCart用ロールの削除
        //SimpleCartModel::deleteRole();

        //Simple Cart用テーブルの削除
        //SimpleCartModel::dropTables();

        //たぶんよくないが。。。
        SimpleCartModel::toTempFiles();

        //SimpleCart用店舗会員／通常会員専用ページの削除
        SimpleCartModel::deletePostPage();

        //SimpleCart用商品情報公開ページの削除
        SimpleCartModel::deletePublicPage();

        //Simple Cart用オプションの削除
        delete_option('sc_options');
    }

    /**
     * BuddyPress存在判定
     *
     */
    function existsBuddyPress() {
        $plugins = get_plugins();
        $active_plugins = array();
        foreach ($plugins as $plugin_file => $plugin_data) {
            //is_plugin_activeで引っかからないので、とりあえずこれで。。。
            if (trim($plugin_data['Name'])=='BuddyPress') {
                return true;
            }
        }
        return false;
    }

    /**
     * Add Store/User Pages
     *
     */
    function addPostPage() {

        //SimpleCart用店舗会員専用ページの作成
        SimpleCartModel::addStorePage();

        //SimpleCart用通常会員専用ページの作成
        SimpleCartModel::addUserPage();
    }

    /**
     * Delete Store/User Pages
     *
     */
    function deletePostPage() {

        //SimpleCart用店舗会員専用ページの削除
        SimpleCartModel::deleteStorePage();

        //SimpleCart用通常会員専用ページの削除
        SimpleCartModel::deleteUserPage();
    }

    /**
     * Getting Setting Infomation
     *
     */
    function getOptions() {
        $params = get_option('sc_options');
        return $params;
    }

    /**
     * Save Setting Infomation
     *
     */
    function saveOptions($params) {
        update_option('sc_options', $params);
    }

    /**
     * Create Simple Cart Tables
     *
     */
    function createTables() {
        global $wpdb;
        include(PLUGIN_SIMPLE_CART . '/_table/createtable.php');
        foreach ($wp_sc_tbls as $tbl_nm) {
            if ($wpdb->get_var("show tables like '$tbl_nm'") != $tbl_nm) {
                dbDelta($wp_sc_sqls[$tbl_nm]);
            }
        }
    }

    /**
     * Drop Simple Cart Tables
     *
     */
    function dropTables() {
        global $wpdb;
        include(PLUGIN_SIMPLE_CART . '/_table/createtable.php');
        foreach ($wp_sc_tbls as $tbl_nm) {
            if ($wpdb->get_var("show tables like '$tbl_nm'") == $tbl_nm) {
                $wpdb->query($wpdb->prepare("drop table {$tbl_nm}"));
            }
        }
    }

    /**
     * Add Simple Cart Role
     *
     */
    //function addRole() {
    //    //administrator、editor、author、contributor、subscriber
    //    add_role(SC_ROLE, __(SCLNG_ROLE, SC_DOMAIN));//独自ロール
    //    $role = get_role(SC_ROLE);
    //    $role->add_cap('upload_files');
    //    $role->add_cap('edit_posts');
    //    $role->add_cap('read');
    //    $role->add_cap('level_1');
    //    $role->add_cap('level_0');
    //    $role->add_cap(SC_CAP);//独自役割
    //}

    /**
     * Delete Simple Cart Role
     *
     */
    //function deleteRole() {
    //    remove_role(SC_ROLE);//独自ロール
    //}

    /**
     * Delete Store Members
     *
     */
    function deleteMembers() {
        global $wpdb;
        $query = "select * from {$wpdb->users}";
        $results = $wpdb->get_results($query);
        $user_list = array();
        foreach ($results as $user) {
            $u = new WP_User($user->ID);
            if ($u->has_cap(SC_CAP)) {
                //役割を削除
                //wpmu_delete_user($user->ID);
                $u->remove_cap(SC_CAP);
            }
        }
    }

    /**
     * Add Store Page
     *
     */
    function addStorePage() {
        global $post_ID, $user_ID;

        //店舗会員用メニューページ生成
        $post_data = array();
        $post_data['post_parent']  = 0;
        $post_data['post_title']   = __(SCLNG_PAGE_STORE_INFO, SC_DOMAIN);
        $post_data['post_content'] = '[SimpleCart_Store_Manage_Page]';
        $post_data['sc_page_id']   = SC_PGID_STORE_INFO;
        $post_ID = SimpleCartModel::insertPage($post_data);

        //親ID取得
        $parent_id = $post_ID;

        //子ページの生成
        $post_data = array();

        //店舗会員用受注管理ページ生成
        $post_data[0]['post_parent']  = $parent_id;
        $post_data[0]['post_title']   = __(SCLNG_PAGE_STORE_ORDER, SC_DOMAIN);
        $post_data[0]['post_content'] = '[SimpleCart_Store_Order_Manage_Page]';
        $post_data[0]['sc_page_id']   = SC_PGID_STORE_ORDER;
        //店舗会員用商品一覧ページ生成
        $post_data[1]['post_parent']  = $parent_id;
        $post_data[1]['post_title']   = __(SCLNG_PAGE_STORE_PRODUCT_LIST, SC_DOMAIN);
        $post_data[1]['post_content'] = '[SimpleCart_Store_Product_List_Page]';
        $post_data[1]['sc_page_id']   = SC_PGID_STORE_PRODUCT_LIST;
        //店舗会員用商品登録ページ生成
        $post_data[2]['post_parent']  = $parent_id;
        $post_data[2]['post_title']   = __(SCLNG_PAGE_STORE_PRODUCT, SC_DOMAIN);
        $post_data[2]['post_content'] = '[SimpleCart_Store_Product_Register_Page]';
        $post_data[2]['sc_page_id']   = SC_PGID_STORE_PRODUCT;
        //店舗会員用規格登録ページ生成
        $post_data[3]['post_parent']  = $parent_id;
        $post_data[3]['post_title']   = __(SCLNG_PAGE_STORE_VARIATION, SC_DOMAIN);
        $post_data[3]['post_content'] = '[SimpleCart_Store_Variation_Manage_Page]';
        $post_data[3]['sc_page_id']   = SC_PGID_STORE_VARIATION;
        //店舗会員用規格関連付け登録ページ生成
        $post_data[4]['post_parent']  = $parent_id;
        $post_data[4]['post_title']   = __(SCLNG_PAGE_STORE_VARIATION_RELATION, SC_DOMAIN);
        $post_data[4]['post_content'] = '[SimpleCart_Store_Variation_Relation_Manage_Page]';
        $post_data[4]['sc_page_id']   = SC_PGID_STORE_VARIATION_RELATION;
        //店舗会員用配送料金管理ページ生成
        $post_data[5]['post_parent']  = $parent_id;
        $post_data[5]['post_title']   = __(SCLNG_PAGE_STORE_DELIVERY, SC_DOMAIN);
        $post_data[5]['post_content'] = '[SimpleCart_Store_Delivery_Manage_Page]';
        $post_data[5]['sc_page_id']   = SC_PGID_STORE_DELIVERY;
        //店舗会員用税率管理ページ生成
        $post_data[6]['post_parent']  = $parent_id;
        $post_data[6]['post_title']   = __(SCLNG_PAGE_STORE_TAX, SC_DOMAIN);
        $post_data[6]['post_content'] = '[SimpleCart_Store_Tax_Manage_Page]';
        $post_data[6]['sc_page_id']   = SC_PGID_STORE_TAX;
        //店舗会員用支払方法管理ページ生成
        $post_data[7]['post_parent']  = $parent_id;
        $post_data[7]['post_title']   = __(SCLNG_PAGE_STORE_PAID_METHOD, SC_DOMAIN);
        $post_data[7]['post_content'] = '[SimpleCart_Store_Paid_Manage_Page]';
        $post_data[7]['sc_page_id']   = SC_PGID_STORE_PAID;
        //店舗会員用設定管理ページ生成
        $post_data[8]['post_parent']  = $parent_id;
        $post_data[8]['post_title']   = __(SCLNG_PAGE_STORE_USER, SC_DOMAIN);
        $post_data[8]['post_content'] = '[SimpleCart_Store_User_Manage_Page]';
        $post_data[8]['sc_page_id']   = SC_PGID_STORE_USER;
        //店舗会員用商品レビュー管理ページ生成
        $post_data[9]['post_parent']  = $parent_id;
        $post_data[9]['post_title']   = __(SCLNG_PAGE_STORE_REVIEW, SC_DOMAIN);
        $post_data[9]['post_content'] = '[SimpleCart_Store_Review_Manage_Page]';
        $post_data[9]['sc_page_id']   = SC_PGID_STORE_REVIEW;
        foreach ($post_data as $post) {
            SimpleCartModel::insertPage($post);
        }
    }

    /**
     * Delete Store Page
     *
     */
    function deleteStorePage() {
        $sc_options = SimpleCartModel::getOptions();
        $ids = array();

        $ids[] = SC_PGID_STORE_INFO;
        $ids[] = SC_PGID_STORE_ORDER;
        $ids[] = SC_PGID_STORE_PRODUCT_LIST;
        $ids[] = SC_PGID_STORE_PRODUCT;
        $ids[] = SC_PGID_STORE_VARIATION;
        $ids[] = SC_PGID_STORE_VARIATION_RELATION;
        $ids[] = SC_PGID_STORE_DELIVERY;
        $ids[] = SC_PGID_STORE_TAX;
        $ids[] = SC_PGID_STORE_PAID;
        $ids[] = SC_PGID_STORE_USER;
        $ids[] = SC_PGID_STORE_REVIEW;
        foreach ($ids as $id) {
            //ページIDを取得
            if (isset($sc_options[$id])) {
                wp_delete_post($sc_options[$id]);
                unset($sc_options[$id]);
            }
        }
        SimpleCartModel::saveOptions($sc_options);
    }

    /**
     * Add User Page
     *
     */
    function addUserPage() {
        global $post_ID, $user_ID;

        //通常会員用メニューページ生成
        $post_data = array();
        $post_data['post_parent']  = 0;
        $post_data['post_title']   = __(SCLNG_PAGE_USER_INFO, SC_DOMAIN);
        $post_data['post_content'] = '[SimpleCart_User_Information_Page]';
        $post_data['sc_page_id']   = SC_PGID_USER_INFO;
        $post_ID = SimpleCartModel::insertPage($post_data);

        //親ID取得
        $parent_id = $post_ID;

        //子ページの生成
        $post_data = array();

        //通常会員用購入履歴一覧ページ生成
        $post_data[0]['post_parent']  = $parent_id;
        $post_data[0]['post_title']   = __(SCLNG_PAGE_USER_HISTORY, SC_DOMAIN);
        $post_data[0]['post_content'] = '[SimpleCart_User_Purchase_History_List_Page]';
        $post_data[0]['sc_page_id']   = SC_PGID_USER_HISTORY;
        //通常会員用カート内容ページ生成
        $post_data[1]['post_parent']  = $parent_id;
        $post_data[1]['post_title']   = __(SCLNG_PAGE_USER_CART, SC_DOMAIN);
        $post_data[1]['post_content'] = '[SimpleCart_User_Cart_Page]';
        $post_data[1]['sc_page_id']   = SC_PGID_USER_CART;
        //通常会員用お気に入りページ生成
        $post_data[2]['post_parent']  = $parent_id;
        $post_data[2]['post_title']   = __(SCLNG_PAGE_USER_FAVORITE, SC_DOMAIN);
        $post_data[2]['post_content'] = '[SimpleCart_Favorite_Page]';
        $post_data[2]['sc_page_id']   = SC_PGID_USER_FAVORITE;
        foreach ($post_data as $post) {
            SimpleCartModel::insertPage($post);
        }
    }

    /**
     * Delete User Page
     *
     */
    function deleteUserPage() {
        $sc_options = SimpleCartModel::getOptions();
        $ids = array();
        $ids[] = SC_PGID_USER_INFO;
        $ids[] = SC_PGID_USER_HISTORY;
        $ids[] = SC_PGID_USER_CART;
        $ids[] = SC_PGID_USER_FAVORITE;
        foreach ($ids as $id) {
            //ページIDを取得
            if (isset($sc_options[$id])) {
                wp_delete_post($sc_options[$id]);
                unset($sc_options[$id]);
            }
        }
        SimpleCartModel::saveOptions($sc_options);
    }

    /**
     * Add Public Page
     *
     */
    function addPublicPage() {
        global $post_ID, $user_ID;

        //新規会員登録ページ生成
        $post_data = array();
        $post_data['post_parent']  = 0;
        $post_data['post_title']   = __(SCLNG_PAGE_PUBLIC_MEMBER_ENTRY, SC_DOMAIN);
        $post_data['post_content'] = '[SimpleCart_Public_Member_Entry_Page]';
        $post_data['sc_page_id']   = SC_PGID_PUBLIC_MEMEBER_ENTRY;
        $post_ID = SimpleCartModel::insertPage($post_data);

        //公開トップページ生成
        $post_data = array();
        $post_data['post_parent']  = 0;
        $post_data['post_title']   = __(SCLNG_PAGE_PUBLIC_TOP, SC_DOMAIN);
        $post_data['post_content'] = '[SimpleCart_Public_Top_Page]';
        $post_data['sc_page_id']   = SC_PGID_PUBLIC_TOP;
        $post_ID = SimpleCartModel::insertPage($post_data);

        //親ID取得
        $parent_id = $post_ID;

        //子ページの生成
        $post_data = array();

        //製品一覧ページ生成
        $post_data[0]['post_parent']  = $parent_id;
        $post_data[0]['post_title']   = __(SCLNG_PAGE_PUBLIC_PRODUCT_LIST, SC_DOMAIN);
        $post_data[0]['post_content'] = '[SimpleCart_Public_Product_List_Page]';
        $post_data[0]['sc_page_id']   = SC_PGID_PUBLIC_PRODUCT_LIST;
        //製品詳細ページ生成
        $post_data[1]['post_parent']  = $parent_id;
        $post_data[1]['post_title']   = __(SCLNG_PAGE_PUBLIC_PRODUCT_DETAIL, SC_DOMAIN);
        $post_data[1]['post_content'] = '[SimpleCart_Public_Product_Page]';
        $post_data[1]['sc_page_id']   = SC_PGID_PUBLIC_PRODUCT;
        //店舗情報ページ生成
        $post_data[2]['post_parent']  = $parent_id;
        $post_data[2]['post_title']   = __(SCLNG_PAGE_PUBLIC_STORE, SC_DOMAIN);
        $post_data[2]['post_content'] = '[SimpleCart_Public_Store_Page]';
        $post_data[2]['sc_page_id']   = SC_PGID_PUBLIC_STORE;
        foreach ($post_data as $post) {
            SimpleCartModel::insertPage($post);
        }
    }

    /**
     * Delete Public Page
     *
     */
    function deletePublicPage() {
        $sc_options = SimpleCartModel::getOptions();
        $ids = array();

        $ids[] = SC_PGID_PUBLIC_TOP;
        $ids[] = SC_PGID_PUBLIC_MEMEBER_ENTRY;
        $ids[] = SC_PGID_PUBLIC_PRODUCT_LIST;
        $ids[] = SC_PGID_PUBLIC_PRODUCT;
        $ids[] = SC_PGID_PUBLIC_STORE;
        foreach ($ids as $id) {
            //ページIDを取得
            if (isset($sc_options[$id])) {
                wp_delete_post($sc_options[$id]);
                unset($sc_options[$id]);
            }
        }
        SimpleCartModel::saveOptions($sc_options);
    }

    /**
     * Insert Page
     *
     */
    function insertPage($post_data) {
        global $post_ID, $user_ID;

        if (!isset($post_data['post_parent'])) {
            $post_data['post_parent'] = 0;
        }
        if (!isset($post_data['post_title'])) {
            return false;
        }
        if (!isset($post_data['post_content'])) {
            return false;
        }

        //ページ登録用配列へ移送
        $post_data['post_name']            = $post_data['post_title'];
        $post_data['post_type']            = 'page';
        $post_data['post_status']          = 'publish';
        $post_data['advanced_view']        = 1;
        $post_data['comment_status']       = 'closed';
        $post_data['ping_status']          = 'closed';
        $post_data['post_author_override'] = $user_ID;
        $post_data['menu_order']           = 0;
        $post_data['pingback']             = 1;
        $post_data['prev_status']          = 'publish';
        $post_data['publish']              = 'Publish';

        //ページ投稿
        $post_ID = wp_update_post($post_data);

        //ページIDを保存
        $sc_options = SimpleCartModel::getOptions();
        $sc_options[$post_data['sc_page_id']] = $post_ID;
        SimpleCartModel::saveOptions($sc_options);

        return $post_ID;
    }

    /**
     * 税込み価格を算出する
     *
     */
    function calcPrice($params) {
        global $wpdb;

        //税込み製品の場合、計算なし
        if ($params['notax']==0) {
            return $params['price'];
        }

        //税情報を取得する
        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $query = "select * from `{$tax_table}` where store_id=" . $params['store_id'];
        $tax = $wpdb->get_results($query);
        if (!is_array($tax)) {
            return $params['price'];
        }
        $tax = $tax[0];

        //税計算
        return SimpleCartFunctions::CalcTax($params['price'], $tax->tax, $tax->method);
    }

    /**
     * 税込み価格（OFF考慮）を算出する
     *
     */
    function calcOffPrice($params) {
        global $wpdb;

        //税込み製品の場合、計算なし
        if ($params['notax']==0) {
            return $params['price'] - $params['off'];
        }

        //税情報を取得する
        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $query = "select * from `{$tax_table}` where store_id=" . $params['store_id'];
        $tax = $wpdb->get_results($query);
        if (!is_array($tax)) {
            return $params['price'];
        }
        $tax = $tax[0];

        //税計算
        return SimpleCartFunctions::CalcTax(($params['price'] - $params['off']), $tax->tax, $tax->method);
    }

    /**
     * 税込み価格を算出する（ダウンロード）
     *
     */
    function calcDownloadPrice($params) {
        global $wpdb;

        //税込み製品の場合、計算なし
        if ($params['notax']==0) {
            return $params['download_price'];
        }

        //税情報を取得する
        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $query = "select * from `{$tax_table}` where store_id=" . $params['store_id'];
        $tax = $wpdb->get_results($query);
        if (!is_array($tax)) {
            return $params['download_price'];
        }
        $tax = $tax[0];

        //税計算
        return SimpleCartFunctions::CalcTax($params['download_price'], $tax->tax, $tax->method);
    }

    /**
     * 税込み価格（OFF考慮）を算出する（ダウンロード）
     *
     */
    function calcDownloadOffPrice($params) {
        global $wpdb;

        //税込み製品の場合、計算なし
        if ($params['notax']==0) {
            return $params['download_price'] - $params['download_off'];
        }

        //税情報を取得する
        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $query = "select * from `{$tax_table}` where store_id=" . $params['store_id'];
        $tax = $wpdb->get_results($query);
        if (!is_array($tax)) {
            return $params['download_price'];
        }
        $tax = $tax[0];

        //税計算
        return SimpleCartFunctions::CalcTax(($params['download_price'] - $params['download_off']), $tax->tax, $tax->method);
    }

    /**
     * Save Cart
     *
     */
    function saveCart($params) {
        global $wpdb, $current_user;

        //sc_cart作成
        $cart_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
        $wpdb->insert($cart_table,
                        array(
                        'user_id'       => $current_user->ID,
                        'store_id'      => $params['store_id'],
                        'product_id'    => $params['product_id'],
                        'pricestock_id' => $params['pricestock_id'],
                        'su'            => $params['quantity'],
                        'download'      => $params['download'],
                        'regist_date'   => date('YmdHis', time())));
    }

    /**
     * Cart Timeout
     *
     */
    function cartTimeout() {
        global $wpdb;

        $cart_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
        $combinations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';

        //現在時間を取得
        $tm = time();
        $y = date('Y', $tm);
        $m = date('m', $tm);
        $d = date('d', $tm);
        $h = date('H', $tm);
        $i = date('i', $tm);
        $s = date('s', $tm);

        //タイムアウト時間取得
        $sc_options = SimpleCartModel::getOptions();
        $timeout = $sc_options['sc_timeout'];

        if (isset($timeout)) {
            if (!is_null($timeout) && $timeout!='' && $timeout!=0) {
                //タイムアウト日時取得
                $out = date('Y-m-d H:i:s', mktime($h, ($i-$timeout), $s, $m, $d, $y));

                //カートからタイムアウトデータを全て削除する
                $wpdb->query($wpdb->prepare("delete from {$cart_table} where regist_date < '{$out}'"));
            }
        }

        //カートから在庫がなくなった商品を削除する（規格管理無し）
        $wpdb->query($wpdb->prepare("
                delete from `{$cart_table}` 
                where (`product_id`, `pricestock_id`) in (
                            select `id`, 0 
                            from  `{$product_table}` 
                            where `publish`=1 
                            and   `stock_manage`=1
                            and   `stock`=0)"));

        //カートから在庫がなくなった商品を削除する（規格管理有り）
        $wpdb->query($wpdb->prepare("
                delete from `{$cart_table}` 
                where (`product_id`, `pricestock_id`) in (
                            select a.`id`, b.`pricestock_id` 
                            from  (select * from `{$product_table}` where `publish`=1 and and `stock_manage`=1) as a,
                                  join `{$combinations_table}` as b on (a.`id`=b.`product_id`),
                                  join `{$priceandstock_table}` as c on (b.`pricestock_id`=c.`id` and c.`stock`=0))"));

        //$wpdb->query($wpdb->prepare("delete from {$cart_table} where product_id not in (select id from `{$product_table}`)"));
    }

    function toTempFiles() {
        $from = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files';
        $to   = ABSPATH . '/wp-content/blogs.dir/__tmp';
        SimpleCartFunctions::Mkdir($to);
        @rename($from, $to);
    }

    function fromTempFiles() {
        $from   = ABSPATH . '/wp-content/blogs.dir/__tmp';
        $to = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files';
        @rename($from, $to);
    }
}
