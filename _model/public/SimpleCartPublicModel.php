<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartPublicModel.php 151641 2009-09-03 00:44:19Z tajima $
 */

class SimpleCartPublicModel extends SimpleCartCommonModel {

    var $user_ID;   //ログインユーザーID

    /**
     * コンストラクタ
     *
     */
    function SimpleCartPublicModel() {
        global $current_user;
        $this->user_ID = $current_user->ID;
    }

    /**
     * 商品一覧取得
     *
     */
    function getProductList($params=null, $count=false) {
        global $wpdb;

        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $tmp_product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';

        //検索条件構築
        $cond = array();
        $cond[] = "d.`user_status` = " . SC_USER_ENABLE;
        $cond[] = "a.`publish` = 1";
        if (isset($params['s_store']) && $params['s_store']!='') {
            $cond[] = "a.`store_id` = ".$params['s_store'];
        }
        if (isset($params['s_categorys']) && $params['s_categorys']!='') {
            $cond[] = "a.`categorys` like '%i:" . $params['s_categorys'] . ";%'";
        }
        if (isset($params['s_text']) && $params['s_text']!='') {
            $c = array();
            $c[] = "(a.`name` like '%" . $params['s_text'] . "%')";
            if (isset($params['s_text_description']) && $params['s_text_description']!='') {
                $c[] = "(a.`description` like '%" . $params['s_text'] . "%')";
                $c[] = "(a.`add_description` like '%" . $params['s_text'] . "%')";
            }
            $cond[] = "(" . @implode(' or ', $c) . ")";
        }
        $where = '';
        if (count($cond) > 0) {
            $where = 'where ' . @implode(' and ', $cond);
        }

        //並び順
        if ($count==false) {
            if (!isset($params['s_sort']) || is_null($params['s_sort'])) {
                $params['s_sort'] = 1;
            }
            if ($params['s_sort']==1) {
                $where .= ' order by a.`regist_date` desc, a.`store_id`, a.`product_cd`';
            }
            else if ($params['s_sort']==2) {
                $where .= ' order by cast(a.`price` AS signed) asc, a.`store_id`, a.`product_cd`';
            }
            else if ($params['s_sort']==3) {
                $where .= ' order by cast(a.`price` AS signed) desc, a.`store_id`, a.`product_cd`';
            }
            else {
                $where .= ' order by a.`store_id`, a.`product_cd`';
            }
        }

        $query = '';
        //情報取得の場合
        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $where .= " limit " . $offset . ", " . $limit;
            }
            $query = "
                select 
                    a.*, 
                    d.`display_name` as store_name,
                    case when b.`product_id` is null then 0 else 1 end enable_variation,
                    b.`min_stock`, 
                    b.`max_stock`, 
                    b.`min_price`, 
                    b.`max_price`, 
                    case when c.`product_id` is null then 0 else 1 end enable_download_variation,
                    c.`min_stock` as download_min_stock, 
                    c.`max_stock` as download_max_stock, 
                    c.`min_price` as download_min_price, 
                    c.`max_price` as download_max_price 
                from `{$product_table}` as a 
                     left outer join `{$tmp_product_table}` as b on (a.`id`=b.`product_id` and b.`download_publish`='0') 
                     left outer join `{$tmp_product_table}` as c on (a.`id`=c.`product_id` and c.`download_publish`='1') 
                     left outer join `{$wpdb->users}` as d on (a.`store_id`=d.`ID`)
                " . $where;
        }
        //件数取得の場合
        else {
            $query = "select count(a.`id`) as count from `{$product_table}` as a left outer join `{$wpdb->users}` as d on (a.`store_id`=d.`ID`) " . $where;
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($count==false) {
                $res = array();
                foreach ($results as $r) {
                    //各種整形処理
                    $r['categorys'] = unserialize($r['categorys']);
                    SimpleCartFunctions::CalcPrices($r);
                    $res[$r['id']] = $r;
                }
                return $res;
            }
            else {
                return $results[0]['count'];
            }
        }
        else {
            if ($count==false) {
                return false;
            }
            else {
                return 0;
            }
        }
    }

    /**
     * 商品情報取得
     *
     */
    function getProduct($params) {
        global $wpdb;

        //検索条件構築
        $cond = array();
        $cond[] = "d.`user_status` = " . SC_USER_ENABLE;
        $cond[] = "a.`publish` = 1";
        $cond[] = "a.`id` = " . $params['product_id'];
        $where = '';
        if (count($cond) > 0) {
            $where = 'where ' . @implode(' and ', $cond);
        }

        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $tmp_product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';

        $query = "
            select 
                a.*, 
                d.`display_name` as store_name,
                case when b.`product_id` is null then 0 else 1 end enable_variation,
                b.`min_stock`, 
                b.`max_stock`, 
                b.`min_price`, 
                b.`max_price`, 
                case when c.`product_id` is null then 0 else 1 end enable_download_variation,
                c.`min_stock` as download_min_stock, 
                c.`max_stock` as download_max_stock, 
                c.`min_price` as download_min_price, 
                c.`max_price` as download_max_price 
            from `{$product_table}` as a 
                 left outer join `{$tmp_product_table}` as b on (a.`id`=b.`product_id` and b.`download_publish`='0') 
                 left outer join `{$tmp_product_table}` as c on (a.`id`=c.`product_id` and c.`download_publish`='1') 
                 left outer join `{$wpdb->users}` as d on (a.`store_id`=d.`ID`)
            " . $where;

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $res = array();
            foreach ($results as $r) {
                //各種整形処理
                $r['categorys'] = unserialize($r['categorys']);
                SimpleCartFunctions::CalcPrices($r);
                $res[] = $r;
                break;
            }
            return $res[0];
        }
        else {
            return false;
        }
    }

    /**
     * 店舗一覧取得
     *
     */
    function getStores() {
        global $wpdb;

        $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';

        //店舗一覧を取得
        $query = "select a.* from `{$wpdb->users}` as a, `{$store_table}` as b  where a.`user_status`=" . SC_USER_ENABLE . " and a.`ID`=b.`user_id` ";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results;
        }
        else {
            return false;
        }
    }

    /**
     * 数量チェック
     *
     */
    function checkQuantity($params) {
        global $wpdb;

        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['quantity']) || $params['quantity']==0 || $params['quantity']=='') {
            $error->errors['quantity'][$params['error_id']] = __(SCLNG_CHECK_REQUIRED_QUANTITY, SC_DOMAIN);
        }
        else if (!is_numeric($params['quantity'])) {
            $error->errors['quantity'][$params['error_id']] = __(SCLNG_CHECK_NUMEIRIC_QUANTITY, SC_DOMAIN);
        }
        else {
            //１回の購入制限数は商品単位とする
            //現在のカートを確認する
            $quantity = 0;
            $cart_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
            $query = "select sum(su) as su from `{$cart_table}` where `user_id`={$this->user_ID} and `product_id`={$params['product_id']}";
            $results = $wpdb->get_results($query, ARRAY_A);
            if (is_array($results)) {
                $quantity = $results[0]['su'];
            }
            $quantity = $quantity + $params['quantity'];
            //購入制限数を確認する
            $quantity_limit = 0;
            $stock = 0;
            $stock_manage = 0;
            $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
            $query = "select * from `{$product_table}` where `id`={$params['product_id']}";
            $results = $wpdb->get_results($query, ARRAY_A);
            if (is_array($results)) {
                $quantity_limit = $results[0]['quantity_limit'];
                $stock = $results[0]['stock'];
                $stock_manage = $results[0]['stock_manage'];
            }
            //購入制限判定をする
            if ($quantity_limit > 0) {
                if ($quantity > $quantity_limit) {
                    $error->errors['quantity'][$params['error_id']] = __(sprintf(SCLNG_CHECK_LIMITOVER_QUANTITY, $quantity_limit), SC_DOMAIN);
                }
            }
            //在庫数の判定をする
            //規格を考慮する
            else if ($stock_manage==1) {
                //規格なし
                if ($params['pricestock_id']==0) {
                    if ($quantity > $stock) {
                        $error->errors['quantity'][$params['error_id']] = __(SCLNG_CHECK_LACK_QUANTITY, SC_DOMAIN);
                    }
                }
                //規格有り
                else {
                    $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
                    $query = "select * from `{$priceandstock_table}` where `id`={$params['pricestock_id']}";
                    $results = $wpdb->get_results($query, ARRAY_A);
                    if (is_array($results)) {
                        $stock = $results[0]['stock'];
                    }
                    if ($quantity > $stock) {
                        $error->errors['quantity'][$params['error_id']] = __(SCLNG_CHECK_LACK_QUANTITY, SC_DOMAIN);
                    }
                }
            }
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 店舗情報取得
     *
     */
    function getStore($params) {
        global $wpdb;

        $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';

        $query = "select b.*, a.`display_name`, a.`user_email` from `{$wpdb->users}` as a, `{$store_table}` as b where a.`user_status`=" . SC_USER_ENABLE . " and a.`ID`={$params['store_id']} and a.`ID`=b.`user_id`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 入力内容のチェック
     *
     */
    function checkUser($params) {
        //-----------------------------------------------------
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        //-----------------------------------------------------
        $error = new WP_Error();
        if (is_null($params['user_login']) || $params['user_login']=='') {
            $error->errors['user_login'] = __(SCLNG_CHECK_REQUIRED_USER_LOGIN_ID, SC_DOMAIN);
        }
        else if (!validate_username($params['user_login'])) {
            $error->errors['user_login'] = __(SCLNG_CHECK_INVALID_USER_LOGIN_ID, SC_DOMAIN);
        }
        else if (username_exists($params['user_login'])) {
            $error->errors['user_login'] = __(SCLNG_CHECK_EXISTS_USER_LOGIN_ID, SC_DOMAIN);
        }
        if (is_null($params['email']) || $params['email']=='') {
            $error->errors['email'] = __(SCLNG_CHECK_REQUIRED_USER_EMAIL, SC_DOMAIN);
        }
        else if (!is_email($params['email'])) {
            $error->errors['email'] = __(SCLNG_CHECK_INVALID_STORE_EMAIL, SC_DOMAIN);
        }
        else if (email_exists($params['email'])) {
            $error->errors['email'] = __(SCLNG_CHECK_EXISTS_USER_EMAIL, SC_DOMAIN);
        }
        //エラーがあれば帰る
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * ユーザー登録
     *
     */
    function saveUser($params) {
        global $wpdb, $current_site;

        $key = substr(md5(time() . rand() . $params['email']), 0, 16);

        $user = array();
        $user['domain']         = '';
        $user['path']           = '';
        $user['title']          = '';
        $user['user_login']     = $params['user_login'];
        $user['user_email']     = $params['email'];
        $user['registered']     = current_time('mysql', true);
        $user['activation_key'] = $key;
        $user['meta']           = '';
        $wpdb->insert($wpdb->signups, $user);

        $admin_email = get_site_option("admin_email");
        if($admin_email == '') {
            $admin_email = 'support@' . $_SERVER['SERVER_NAME'];
        }
        $from_name = get_site_option("site_name")==''?'WordPress':wp_specialchars(get_site_option("site_name"));

        //
        $info = array();
        $info['site_domain']  = $current_site->domain;
        $info['site_path']    = $current_site->path;
        $info['activate_key'] = $key;

        //新規会員登録情報をメール送信
        $mail_data = array();
        $mail_data['from']    = $from_name;
        $mail_data['to']      = $params['email'];
        $mail_data['subject'] = __(sprintf(SCLNG_MAIL_MEMBER_ENTRY_SUBJECT, $params['user_login']), SC_DOMAIN);
        $mail_data['body']    = SimpleCartFunctions::TemplateConvert('mail/member_entry.txt', $info);
        $mres = sc_mail($mail_data);
        return true;
    }

    /**
     * 規格詳細取得
     *
     */
    function getVariationValues($params) {
        global $wpdb;

        $values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';

        $query = "select * from {$values_table} where `store_id`={$params['store_id']} and `variation_id`={$params['variation_id']} order by `id`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $variation = array();
            foreach ($results as $r) {
                $variation[$r['id']] = $r;
            }
            return $variation;
        }
        else {
            return false;
        }
    }

    /**
     * 製品規格一覧取得
     *
     */
    function getProductVariations($params) {
        global $wpdb;

        $variation_associations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_associations';

        $query = "
            select * 
            from {$variation_associations_table} 
            where `store_id`={$params['store_id']} 
            and   `associated_id`={$params['product_id']} 
            and   `download_publish`='{$params['download_publish']}'
            order by `id`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $variations = array();
            foreach ($results as $r) {
                $variations[$r['variation_id']] = $r['variation_id'];
            }
            return $variations;
        }
        else {
            return false;
        }
    }

    /**
     * 製品規格詳細情報取得
     *
     */
    function getProductVariationValues($params) {
        global $wpdb;

        $combinations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';
        $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';

        $query = "
            select a.*,
                   b.stock,
                   b.price,
                   b.visible
            from {$combinations_table} as a,
                 {$priceandstock_table} as b
            where a.`store_id`={$params['store_id']} 
            and   a.`product_id`={$params['product_id']}
            and   a.`download_publish`='{$params['download_publish']}'
            and   a.`pricestock_id`=b.`id`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $combinations = array();
            foreach ($results as $r) {
                $combinations[$r['all_variation_id']] = $r;
            }
            return $combinations;
        }
        else {
            return false;
        }
    }

    /**
     * レビュー一覧取得
     *
     */
    function getReviewList($params, $count=false) {
        global $wpdb;

        $review_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';

        $query = '';
        $page_cond = '';
        //購入履歴情報取得の場合
        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $page_cond = " limit " . $offset . ", " . $limit;
            }
            $query = "
                select 
                    a.*,
                    b.`display_name` as user_name 
                from `{$review_table}` as a 
                     left outer join `{$wpdb->users}` as b on (a.`user_id`=b.`ID`)
                where a.`product_id`={$params['product_id']}
                order by `regist_date` desc " . $page_cond;
        }
        //件数情報取得の場合
        else {
            $query = "select count(id) as count from `{$review_table}` where `product_id`={$params['product_id']}";
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($count==false) {
                return $results;
            }
            else {
                return $results[0]['count'];
            }
        }
        else {
            return false;
        }
    }

    /**
     * レビューの保存
     *
     */
    function saveReview($params) {
        global $current_user, $wpdb;

        $user_id = $current_user->ID;
        if (!$user_id || !isset($params['store_id']) || !isset($params['product_id'])) {
            return false;
        }

        $review_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';
        $wpdb->insert($review_table, array(
                        'store_id'    => $params['store_id'],
                        'product_id'  => $params['product_id'],
                        'user_id'     => $user_id,
                        'comment'     => $params['comment'],
                        'recommend'   => $params['recommend'],
                        'regist_date' => date('YmdHis', time())
               ));
        return true;
    }

    /**
     * お気に入り情報保存
     *
     */
    function saveFavorite($params) {
        global $current_user, $wpdb;

        $user_id = $current_user->ID;
        if (!$user_id || !isset($params['product_id'])) {
            return false;
        }

        $favorite_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user_favorite';
        $query = "select count(`id`) as count from `{$favorite_table}` where `user_id`={$user_id} and `product_id`={$params['product_id']}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($results[0]['count'] == 0) {
                $wpdb->insert($favorite_table, array(
                                'user_id'    => $user_id,
                                'product_id' => $params['product_id'],
                                'regist_date' => date('YmdHis', time())
                       ));
            }
        }

        return true;
    }

}
