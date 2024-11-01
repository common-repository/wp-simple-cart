<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile User Cart Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserCartModel.php 147731 2009-08-21 05:28:05Z tajima $
 */

class SimpleCartUserCartModel extends SimpleCartCommonModel {

    /**
     * カート情報取得
     *
     */
    function getCart() {
        global $wpdb;

        //テンポラリテーブル名称用ランダム値取得
        $rand = SimpleCartFunctions::Rand8();

        $cart_table               = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
        $product_table            = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $free_table               = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_free';
        $value_table              = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        $product_variations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';
        $variation_values_table   = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';
        $priceandstock_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
        $combinations_table       = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';
        $user_table               = $wpdb->users;

        //一次検索
        $query = "
                create temporary table my_cart_" . $rand . "
                select 
                    a.*, 
                    d.`user_login`, 
                    d.`user_email`, 
                    c.`user_login` as store_login, 
                    c.`display_name` as store_name, 
                    c.`user_email` as store_email, 
                    b.`product_cd`, 
                    b.`name`, 
                    b.`download_publish`, 
                    b.`price`, 
                    b.`off`, 
                    b.`notax`, 
                    b.`download_price`, 
                    b.`download_off`, 
                    b.`image_file_url1`, 
                    b.`image_file_url3`, 
                    0 as variation_price,
                    9 as cart_download_publish
                from `{$cart_table}` as a, 
                     `{$product_table}` as b, 
                     `{$user_table}` as c, 
                     `{$user_table}` as d 
                where a.`user_id`=" . $this->user_ID . " 
                and   a.`pricestock_id`=0
                and   a.`product_id`=b.`id` 
                and   a.`store_id`=c.`ID` 
                and   a.`user_id`=d.`ID` 
                and   b.`stock_manage`=0
                union all
                select 
                    a.*, 
                    d.`user_login`, 
                    d.`user_email`, 
                    c.`user_login` as store_login, 
                    c.`display_name` as store_name, 
                    c.`user_email` as store_email, 
                    b.`product_cd`, 
                    b.`name`, 
                    b.`download_publish`, 
                    b.`price`, 
                    b.`off`, 
                    b.`notax`, 
                    b.`download_price`, 
                    b.`download_off`, 
                    b.`image_file_url1`, 
                    b.`image_file_url3`, 
                    0 as variation_price,
                    9 as cart_download_publish
                from `{$cart_table}` as a, 
                     `{$product_table}` as b, 
                     `{$user_table}` as c, 
                     `{$user_table}` as d 
                where a.`user_id`=" . $this->user_ID . " 
                and   a.`pricestock_id`=0
                and   a.`product_id`=b.`id` 
                and   a.`store_id`=c.`ID` 
                and   a.`user_id`=d.`ID` 
                and   b.`stock_manage`=1 
                and   b.`stock`>0
                union all
                select 
                    a.*, 
                    d.`user_login`, 
                    d.`user_email`, 
                    c.`user_login` as store_login, 
                    c.`display_name` as store_name, 
                    c.`user_email` as store_email, 
                    b.`product_cd`, 
                    b.`name`, 
                    b.`download_publish`, 
                    b.`price`, 
                    b.`off`, 
                    b.`notax`, 
                    b.`download_price`, 
                    b.`download_off`, 
                    b.`image_file_url1`, 
                    b.`image_file_url3`, 
                    e.`price` as variation_price,
                    e.`download_publish` as cart_download_publish
                from `{$cart_table}` as a, 
                     `{$product_table}` as b, 
                     `{$user_table}` as c, 
                     `{$user_table}` as d, 
                     `{$priceandstock_table}` as e 
                where a.`user_id`=" . $this->user_ID . " 
                and   a.`product_id`=b.`id` 
                and   a.`pricestock_id`=e.`id` 
                and   a.`store_id`=c.`ID` 
                and   a.`user_id`=d.`ID` 
                and   b.`stock_manage`=0
                union all
                select 
                    a.*, 
                    d.`user_login`, 
                    d.`user_email`, 
                    c.`user_login` as store_login, 
                    c.`display_name` as store_name, 
                    c.`user_email` as store_email, 
                    b.`product_cd`, 
                    b.`name`, 
                    b.`download_publish`, 
                    b.`price`, 
                    b.`off`, 
                    b.`notax`, 
                    b.`download_price`, 
                    b.`download_off`, 
                    b.`image_file_url1`, 
                    b.`image_file_url3`, 
                    e.`price` as variation_price,
                    e.`download_publish` as cart_download_publish
                from `{$cart_table}` as a, 
                     `{$product_table}` as b, 
                     `{$user_table}` as c, 
                     `{$user_table}` as d, 
                     `{$priceandstock_table}` as e 
                where a.`user_id`=" . $this->user_ID . " 
                and   a.`product_id`=b.`id` 
                and   a.`pricestock_id`=e.`id` 
                and   a.`store_id`=c.`ID` 
                and   a.`user_id`=d.`ID` 
                and   b.`stock_manage`=1 
                and   b.`stock`>0";
        $wpdb->query($wpdb->prepare($query));

        //カート情報取得
        $query = "select
                    a.`user_id`,
                    a.`user_login`,
                    a.`user_email`,
                    a.`store_id`,
                    a.`store_login`,
                    a.`store_name`,
                    a.`store_email`,
                    a.`product_id`,
                    a.`pricestock_id`,
                    a.`download`,
                    sum(a.`su`) as su,
                    min(a.`regist_date`) as regist_date,
                    a.`product_cd`,
                    a.`name`,
                    a.`download_publish`,
                    a.`price`,
                    a.`off`,
                    a.`notax`,
                    a.`download_price`,
                    a.`download_off`,
                    a.`image_file_url1`,
                    a.`image_file_url3`,
                    a.`variation_price`,
                    a.`cart_download_publish`,
                    case when c.`store_id` is null then 0 else 1 end as delivery_fee_flg,
                    b.`price_limit`
                from `my_cart_" . $rand . "` as a
                     left outer join `{$free_table}` as b
                     on (a.`store_id`=b.`store_id`)
                     left outer join (select `store_id` from `{$value_table}` where `delivery_fee` > 0 group by `store_id`) as c
                     on (a.`store_id`=c.`store_id`)
                group by 
                    a.`user_id`,
                    a.`user_login`,
                    a.`user_email`,
                    a.`store_id`,
                    a.`store_login`,
                    a.`store_name`,
                    a.`store_email`,
                    a.`product_id`,
                    a.`pricestock_id`,
                    a.`download`,
                    a.`product_cd`,
                    a.`name`,
                    a.`download_publish`,
                    a.`price`,
                    a.`off`,
                    a.`notax`,
                    a.`download_price`,
                    a.`download_off`,
                    a.`image_file_url1`,
                    a.`image_file_url3`,
                    a.`variation_price`,
                    a.`cart_download_publish`,
                    c.`store_id`,
                    b.`price_limit`
                order by
                    a.`store_name`,
                    a.`regist_date` desc";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $res = array();
            foreach ($results as $r) {
                //規格連携無し
                if ($r['pricestock_id']==0) {
                    //金額の計算
                    $r['calc_price'] = SimpleCartFunctions::Price($r);
                    $r['calc_tax'] = $r['calc_price'] - $r['price'];
                    $r['calc_off_price'] = SimpleCartFunctions::OffPrice($r);
                    $r['calc_off_tax'] = $r['calc_off_price'] - ($r['price']-$r['off']);
                    $r['calc_download_price'] = SimpleCartFunctions::DownloadPrice($r);
                    $r['calc_download_tax'] = $r['calc_download_price'] - $r['download_price'];
                    $r['calc_download_off_price'] = SimpleCartFunctions::DownloadOffPrice($r);
                    $r['calc_download_off_tax'] = $r['calc_download_off_price'] - ($r['download_price']-$r['download_off']);
                }
                //規格連携有り
                else {
                    //規格考慮の金額計算
                    $params = array();
                    $params['id']               = $r['product_id'];
                    $params['store_id']         = $r['store_id'];
                    $params['notax']            = $r['notax'];
                    $params['off']              = $r['off'];
                    $params['download_off']     = $r['download_off'];
                    if ($r['cart_download_publish']==0) {
                        //通常製品購入
                        $params['price'] = $r['variation_price'];
                        $r['calc_price'] = SimpleCartFunctions::Price($params);
                        $r['calc_tax'] = $r['calc_price'] - $params['price'];
                        $r['calc_off_price'] = SimpleCartFunctions::OffPrice($params);
                        $r['calc_off_tax'] = $r['calc_off_price'] - ($params['price']-$params['off']);
                        $r['calc_download_price'] = null;
                        $r['calc_download_tax'] = null;
                        $r['calc_download_off_price'] = null;
                        $r['calc_download_off_tax'] = null;
                    }
                    else {
                        //ダウンロード製品購入
                        $params['download_price'] = $r['variation_price'];
                        if ($r['download_publish']=='2') {
                            //併売品はダウンロード用金額へ
                            $r['calc_price'] = null;
                            $r['calc_tax'] = null;
                            $r['calc_off_price'] = null;
                            $r['calc_off_tax'] = null;
                            $r['calc_download_price'] = SimpleCartFunctions::DownloadPrice($params);
                            $r['calc_download_tax'] = $r['calc_download_price'] - $params['download_price'];
                            $r['calc_download_off_price'] = SimpleCartFunctions::DownloadOffPrice($params);
                            $r['calc_download_off_tax'] = $r['calc_download_off_price'] - ($params['download_price']-$params['download_off']);
                        }
                        else {
                            //ダウンロードのみの場合は金額へダウンロード金額を代入
                            $r['calc_price'] = SimpleCartFunctions::DownloadPrice($params);
                            $r['calc_tax'] = $r['calc_price'] - $params['download_price'];
                            $r['calc_off_price'] = SimpleCartFunctions::DownloadOffPrice($params);
                            $r['calc_off_tax'] = $r['calc_off_price'] - ($params['download_price']-$params['download_off']);
                            $r['calc_download_price'] = null;
                            $r['calc_download_tax'] = null;
                            $r['calc_download_off_price'] = null;
                            $r['calc_download_off_tax'] = null;
                        }
                    }

                    //規格名称の取得
                    $query = "
                        select 
                            a.*, 
                            b.`name` as variation_name,
                            c.`name` as value_name
                        from   `{$combinations_table}` as a,
                               `{$product_variations_table}` as b,
                               `{$variation_values_table}` as c
                        where a.`pricestock_id`={$r['pricestock_id']}
                        and   a.`variation_id`=b.`id`
                        and   a.`value_id`=c.`id`
                        order by a.`variation_id`, a.`value_id`";
                    $variations = $wpdb->get_results($query, ARRAY_A);
                    if (is_array($variations)) {
                        $variation_name = array();
                        foreach ($variations as $variation) {
                            $variation_name[] = array('variation'=>$variation['variation_name'], 'value'=>$variation['value_name']);
                        }
                        $r['variation_names'] = $variation_name;
                    }
                }

                //確定金額の判定
                $r['fixed_price'] = $r['calc_price'];
                $r['fixed_tax'] = $r['calc_tax'];
                $r['fixed_off_price'] = $r['calc_off_price'];
                $r['fixed_off_tax'] = $r['calc_off_tax'];
                if ($r['download']=='1' && $r['download_publish']=='2') {
                    $r['fixed_price'] = $r['calc_download_price'];
                    $r['fixed_price'] = $r['calc_download_tax'];
                    $r['fixed_off_price'] = $r['calc_download_off_price'];
                    $r['fixed_off_price'] = $r['calc_download_off_tax'];
                }

                //店舗情報の設定
                $res[$r['store_id']]['store_id']    = $r['store_id'];
                $res[$r['store_id']]['store_login'] = $r['store_login'];
                $res[$r['store_id']]['store_name']  = $r['store_name'];
                $res[$r['store_id']]['store_email'] = $r['store_email'];
                $res[$r['store_id']]['user_id']     = $r['user_id'];
                $res[$r['store_id']]['user_login']  = $r['user_login'];
                $res[$r['store_id']]['user_email']  = $r['user_email'];
                $res[$r['store_id']]['delivery_fee_flg'] = $r['delivery_fee_flg'];
                $res[$r['store_id']]['price_limit'] = $r['price_limit'];
                $res[$r['store_id']]['data'][] = $r;
            }
            return $res;
        }
        else {
            return false;
        }
    }

    /**
     * カート情報削除
     *
     */
    function deleteCart($params) {
        global $wpdb;

        //カートから商品を削除する
        $cart_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
        $query = "delete from `{$cart_table}` where `product_id`={$params['product_id']} and `pricestock_id`={$params['pricestock_id']} and `download`={$params['download']} and `user_id`={$this->user_ID}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 数量チェック
     *
     */
    function checkQuantity($params) {
        global $wpdb;

        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();

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
        if ($quantity==0) {
            $error->errors['quantity'] = __(SCLNG_CHECK_REQUIRED_QUANTITY, SC_DOMAIN);
        }
        //購入制限数を確認する
        $quantity_limit = 0;
        $stock = 0;
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
                $error->errors['quantity'] = __(SCLNG_CHECK_LIMITOVER_QUANTITY, SC_DOMAIN);
            }
        }
        //在庫数の判定をする
        //規格を考慮する
        else if ($stock_manage==1) {
            //規格なし
            if ($params['pricestock_id']==0) {
                if ($quantity > $stock) {
                    $error->errors['quantity'] = __(SCLNG_CHECK_LACK_QUANTITY, SC_DOMAIN);
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
                    $error->errors['quantity'] = __(SCLNG_CHECK_LACK_QUANTITY, SC_DOMAIN);
                }
            }
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * ユーザー情報取得
     *
     */
    function getUser() {
        global $wpdb;

        $user_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user';
        $query = "select a.user_login, a.user_email, a.display_name, b.* 
                  from {$wpdb->users} as a
                       left outer join {$user_table} as b on (a.`ID`=b.`user_id`)
                  where a.`ID`={$this->user_ID}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 手数料取得
     *
     */
    function getCommissions($params) {
        global $wpdb, $sc_states;

        $paid_method_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';

        //店舗別に手数料を取得する
        $commissions = array();
        if (is_array($params)) {
            foreach ($params as $key=>$val) {
                $query = "select * from `{$paid_method_table}` where `id`={$val['paid_method']}";
                $results = $wpdb->get_results($query, ARRAY_A);
                if (is_array($results)) {
                    $commissions[$key] = $results[0]['commission'];
                }
            }
        }
        return $commissions;
    }

    /**
     * 送料取得
     *
     */
    function getDeliverysCost($params) {
        global $wpdb, $sc_states;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';

        //店舗別に送料を取得する
        $deliverys_cost = array();
        if (is_array($params)) {
            foreach ($params as $key=>$val) {
                $query = "select * from `{$delivery_values_table}` where `store_id`={$key} and `delivery_id`={$val['delivery']} and `name`='{$sc_states[$val['state']]}'";
                $results = $wpdb->get_results($query, ARRAY_A);
                if (is_array($results)) {
                    $deliverys_cost[$key] = $results[0]['delivery_fee'];
                }
            }
        }
        return $deliverys_cost;
    }

    /**
     * 送料ID取得
     *
     */
    function getDeliverysValue($params) {
        global $wpdb, $sc_states;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';

        //送料自動算出
        $deliverys_value = array();
        if (is_array($params)) {
            foreach ($params as $key=>$val) {
                $query = "select * from `{$delivery_values_table}` where `store_id`={$key} and `delivery_id`={$val['delivery']} and `name`='{$sc_states[$val['state']]}'";
                $results = $wpdb->get_results($query, ARRAY_A);
                if (is_array($results)) {
                    $deliverys_value[$key] = $results[0]['id'];
                }
            }
        }
        return $deliverys_value;
    }

    /**
     * 希望配送方法取得
     *
     */
    function getDeliverys($stores) {
        global $wpdb;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';

        //店舗別に配送方法を取得する
        $delivery = array();
        if (is_array($stores)) {
            foreach ($stores as $store) {
                $query = "select * from `{$delivery_table}` where `store_id`={$store} order by sort";
                $results = $wpdb->get_results($query, ARRAY_A);
                if (is_array($results)) {
                    $deli = array();
                    foreach ($results as $r) {
                        $deli[$r['id']] = $r['name'];
                    }
                    $delivery[$store] = $deli;
                }
            }
        }
        return $delivery;
    }

    /**
     * 希望配送時間帯取得
     *
     */
    function getDeliverysTimes($stores) {
        global $sc_delivery_times;

        //店舗別に配送時間帯を取得する
        $delivery_times = array();
        if (is_array($stores)) {
            foreach ($stores as $store) {
                $delivery_times[$store] = $sc_delivery_times;
            }
        }
        return $delivery_times;
    }

    /**
     * 希望支払方法取得
     *
     */
    function getPaidMethods($stores) {
        global $wpdb;

        $paid_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';

        //店舗別に支払方法を取得する
        $paid_method = array();
        if (is_array($stores)) {
            foreach ($stores as $store) {
                $query = "select * from `{$paid_table}` where `store_id`={$store} order by sort";
                $results = $wpdb->get_results($query, ARRAY_A);
                if (is_array($results)) {
                    $paid = array();
                    foreach ($results as $r) {
                        $paid[$r['id']] = $r['name'];
                    }
                    $paid_method[$store] = $paid;
                }
            }
        }
        return $paid_method;
    }

    /**
     * 送付先／請求先情報のチェック
     *
     */
    function checkUser($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();

        if (is_null($params['send_first_name']) || $params['send_first_name']=='') {
            $error->errors['send_first_name'] = __(SCLNG_CHECK_REQUIRED_FIRST_NAME, SC_DOMAIN);
        }
        if (is_null($params['send_last_name']) || $params['send_last_name']=='') {
            $error->errors['send_last_name'] = __(SCLNG_CHECK_REQUIRED_LAST_NAME, SC_DOMAIN);
        }
        if (is_null($params['send_first_furi']) || $params['send_first_furi']=='') {
            $error->errors['send_first_furi'] = __(SCLNG_CHECK_REQUIRED_FIRST_FURI, SC_DOMAIN);
        }
        if (is_null($params['send_last_furi']) || $params['send_last_furi']=='') {
            $error->errors['send_last_furi'] = __(SCLNG_CHECK_REQUIRED_LAST_FURI, SC_DOMAIN);
        }
        if (is_null($params['send_address']) || $params['send_address']=='') {
            $error->errors['send_address'] = __(SCLNG_CHECK_REQUIRED_ADDRESS, SC_DOMAIN);
        }
        if (is_null($params['send_street']) || $params['send_street']=='') {
            $error->errors['send_street'] = __(SCLNG_CHECK_REQUIRED_STREET, SC_DOMAIN);
        }
        if (is_null($params['send_zip']) || $params['send_zip']=='') {
            $error->errors['send_zip'] = __(SCLNG_CHECK_REQUIRED_ZIP, SC_DOMAIN);
        }
        if (is_null($params['send_tel']) || $params['send_tel']=='') {
            $error->errors['send_tel'] = __(SCLNG_CHECK_REQUIRED_TEL, SC_DOMAIN);
        }
        //if (is_null($params['send_mobile']) || $params['send_mobile']=='') {
        //    $error->errors['send_mobile'] = __(SCLNG_CHECK_REQUIRED_MOBILE, SC_DOMAIN);
        //}
        if (is_null($params['bill_first_name']) || $params['bill_first_name']=='') {
            $error->errors['bill_first_name'] = __(SCLNG_CHECK_REQUIRED_FIRST_NAME, SC_DOMAIN);
        }
        if (is_null($params['bill_last_name']) || $params['bill_last_name']=='') {
            $error->errors['bill_last_name'] = __(SCLNG_CHECK_REQUIRED_LAST_NAME, SC_DOMAIN);
        }
        if (is_null($params['bill_first_furi']) || $params['bill_first_furi']=='') {
            $error->errors['bill_first_furi'] = __(SCLNG_CHECK_REQUIRED_FIRST_FURI, SC_DOMAIN);
        }
        if (is_null($params['bill_last_furi']) || $params['bill_last_furi']=='') {
            $error->errors['bill_last_furi'] = __(SCLNG_CHECK_REQUIRED_LAST_FURI, SC_DOMAIN);
        }
        if (is_null($params['bill_address']) || $params['bill_address']=='') {
            $error->errors['bill_address'] = __(SCLNG_CHECK_REQUIRED_ADDRESS, SC_DOMAIN);
        }
        if (is_null($params['bill_street']) || $params['bill_street']=='') {
            $error->errors['bill_street'] = __(SCLNG_CHECK_REQUIRED_STREET, SC_DOMAIN);
        }
        if (is_null($params['bill_zip']) || $params['bill_zip']=='') {
            $error->errors['bill_zip'] = __(SCLNG_CHECK_REQUIRED_ZIP, SC_DOMAIN);
        }
        if (is_null($params['bill_tel']) || $params['bill_tel']=='') {
            $error->errors['bill_tel'] = __(SCLNG_CHECK_REQUIRED_TEL, SC_DOMAIN);
        }
        //if (is_null($params['bill_mobile']) || $params['bill_mobile']=='') {
        //    $error->errors['bill_mobile'] = __(SCLNG_CHECK_REQUIRED_MOBILE, SC_DOMAIN);
        //}
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * オーダー情報の登録
     *
     */
    function saveOrder($params) {
        global $wpdb, $sc_states;

        $order_info = array();

        $user_table         = $wpdb->base_prefix . $wpdb->blogid . '_sc_user';
        $cart_table         = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
        $order_table        = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
        $message_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_message';
        $product_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';

        //店舗別各種情報取得
        $stores = array();
        foreach ($params['cart'] as $key=>$val) {
            $stores[] = $key;
        }
        $deliverys       = SimpleCartUserCartModel::getDeliverys($stores);
        $deliverys_times = SimpleCartUserCartModel::getDeliverysTimes($stores);
        $paid_methods    = SimpleCartUserCartModel::getPaidMethods($stores);

        //$status[store_id][status]
        //$status[store_id][order_id]
        //$status[store_id][message]
        $status = array();

        //登録時在庫数チェック
        foreach ($params['cart'] as $store_id=>$store) {
            $status[$store_id]['status'] = true;
            $status[$store_id]['download_only'] = 1;
            foreach ($store['data'] as $product) {
                //ダウンロード販売は対象外
                if ($product['download']=='0') {
                    //ついでに購入情報を整理しておく
                    $status[$store_id]['download_only'] = 0;

                    $query = "select * from `{$product_table}` where `id`={$product['product_id']}";
                    $results = $wpdb->get_results($query, ARRAY_A);
                    if (is_array($results)) {
                        //在庫数の判定をする（在庫管理対象）
                        if ($results[0]['stock_manage']==1) {
                            //規格なし
                            if ($product['pricestock_id']==0) {
                                if ($product['su'] > $results[0]['stock']) {
                                    //在庫不足エラー
                                    $status[$store_id]['status']  = false;
                                    $status[$store_id]['message'] = __(SCLNG_ERROR_STOCK, SC_DOMAIN);
                                    break;
                                }
                                else if ($product['su'] <= $results[0]['stock']) {
                                    //在庫データ更新
                                    $stock = $results[0]['stock'] - $product['su'];
                                    $wpdb->update($product_table, array('stock'=>$stock), array('id'=>$product['product_id']));
                                }
                            }
                            //規格有り
                            else {
                                $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
                                $query = "select * from `{$priceandstock_table}` where `id`={$product['pricestock_id']}";
                                $variation = $wpdb->get_results($query, ARRAY_A);
                                if (is_array($variation)) {
                                    if ($product['su'] > $variation[0]['stock']) {
                                        //在庫不足エラー
                                        $status[$store_id]['status']  = false;
                                        $status[$store_id]['message'] = __(SCLNG_ERROR_STOCK, SC_DOMAIN);
                                        break;
                                    }
                                    else if ($product['su'] <= $variation[0]['stock']) {
                                        //在庫データ更新
                                        $stock = $variation[0]['stock'] - $product['su'];
                                        $wpdb->update($priceandstock_table, array('stock'=>$stock), array('id'=>$product['pricestock_id']));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //店舗別にオーダーを登録
        foreach ($params['cart'] as $store_id=>$store) {

            if ($status[$store_id]['status']==true) {
                //頭の登録
                $order = array();
                $order['status']             = SC_NEW_RECEIPT;
                $order['user_id']            = $this->user_ID;
                $order['store_id']           = $store_id;
                $order['deliver_id']         = $params['deliverys'][$store_id];
                $order['deliver_name']       = $deliverys[$store_id][$order['deliver_id']];
                $order['deliver_value_id']   = $params['deliverys_value'][$store_id];
                $order['deliver_value_name'] = '';//@todo 未実装
                $order['message']            = $params['frees'][$store_id];
                $order['deliver_fee']        = $params['deliverys_cost'][$store_id];
                $order['commission']         = $params['commissions'][$store_id];
                $order['total']              = $params['totals'][$store_id];
                $order['download']           = $status[$store_id]['download_only'];
                $order['paid_method']        = $params['paid_methods'][$store_id];
                $order['paid_method_name']   = $paid_methods[$store_id][$order['paid_method']];
                $order['delivery_date']      = '';//@todo 未実装
                $order['delivery_time']      = $params['deliverys_times'][$store_id];
                $order['delivery_time_name'] = $deliverys_times[$store_id][$order['delivery_time']];
                $order['send_first_name']    = $params['send_first_name'];
                $order['send_last_name']     = $params['send_last_name'];
                $order['send_first_furi']    = $params['send_first_furi'];
                $order['send_last_furi']     = $params['send_last_furi'];
                $order['send_address']       = $params['send_address'];
                $order['send_street']        = $params['send_street'];
                $order['send_state']         = $params['send_state'];
                $order['send_zip']           = $params['send_zip'];
                $order['send_tel']           = $params['send_tel'];
                $order['send_fax']           = $params['send_fax'];
                $order['send_mobile']        = $params['send_mobile'];
                $order['send_mail']          = $store['user_email'];
                $order['bill_first_name']    = $params['bill_first_name'];
                $order['bill_last_name']     = $params['bill_last_name'];
                $order['bill_first_furi']    = $params['bill_first_furi'];
                $order['bill_last_furi']     = $params['bill_last_furi'];
                $order['bill_address']       = $params['bill_address'];
                $order['bill_street']        = $params['bill_street'];
                $order['bill_state']         = $params['bill_state'];
                $order['bill_zip']           = $params['bill_zip'];
                $order['bill_tel']           = $params['bill_tel'];
                $order['bill_fax']           = $params['bill_fax'];
                $order['bill_mobile']        = $params['bill_mobile'];
                $order['bill_mail']          = $store['user_email'];
                $order['regist_date']        = date('YmdHis', time());
                $order['update_date']        = date('YmdHis', time());
                $wpdb->insert($order_table, $order);

                $order_id = $wpdb->insert_id;
                $status[$store_id]['order_id'] = $order_id;
                $status[$store_id]['store_name'] = $store['store_name'];
                $status[$store_id]['store_email'] = $store['store_email'];

                //登録情報の移送（メール送信用）
                $order_info[$order_id]['store_id']      = $store_id;
                $order_info[$order_id]['store_login']   = $store['store_login'];
                $order_info[$order_id]['store_name']    = $store['store_name'];
                $order_info[$order_id]['store_email']   = $store['store_email'];
                $order_info[$order_id]['user_id']       = $store['user_id'];
                $order_info[$order_id]['user_login']    = $store['user_login'];
                $order_info[$order_id]['user_email']    = $store['user_email'];
                $order_info[$order_id]['bill_name']     = $params['bill_last_name'] . ' ' . $params['bill_first_name'];
                $order_info[$order_id]['send_name']     = $params['send_last_name'] . ' ' . $params['send_first_name'];
                $order_info[$order_id]['order_id']      = SimpleCartFunctions::LPAD($order_id, 8);
                $order_info[$order_id]['delivery_fee']  = $order['deliver_fee'];
                $order_info[$order_id]['commission']    = $order['commission'];
                $order_info[$order_id]['total']         = $order['total'];
                $order_info[$order_id]['download']      = $order['download'];
                $order_info[$order_id]['delivery']      = $deliverys[$store_id][$order['deliver_id']];
                $order_info[$order_id]['paid_method']   = $paid_methods[$store_id][$order['paid_method']];
                $order_info[$order_id]['delivery_time'] = $deliverys_times[$store_id][$order['delivery_time']];
                $order_info[$order_id]['zip']           = $order['send_zip'];
                $order_info[$order_id]['address']       = $sc_states[$order['send_state']] . ' ' . $order['send_street'] . ' ' . $order['send_address'];
                $order_info[$order_id]['tel']           = $order['send_tel'];

                $order_info_detail = array();
                $i = 0;
                foreach ($store['data'] as $product) {
                    //通常購入
                    if ($product['fixed_price'] == $product['fixed_off_price']) {
                        $price = $product['fixed_price'];
                    }
                    else {
                        $price = $product['fixed_off_price'];
                    }
                    if ($product['no_tax']=='1') {
                        if ($product['fixed_price'] == $product['fixed_off_price']) {
                            $tax = $product['fixed_tax'];
                        }
                        else {
                            $tax = $product['fixed_off_tax'];
                        }
                    }
                    else {
                        $tax = 0;
                    }

                    //規格情報の整形
                    $variation_name = '';
                    if (isset($product['variation_names'])) {
                        foreach ($product['variation_names'] as $variation_names) {
                            $variation_name .= $variation_names['variation'] . ':' . $variation_names['value'] . chr(10);
                        }
                    }

                    //詳細の登録
                    $order_detail = array();
                    $order_detail['order_id']      = $order_id;
                    $order_detail['product_id']    = $product['product_id'];
                    $order_detail['pricestock_id'] = $product['pricestock_id'];
                    $order_detail['download_hash'] = ($product['download']=='0')?'0':substr(SimpleCartFunctions::RandMd5(),0,16);
                    $order_detail['download']      = $product['download'];
                    $order_detail['product_cd']    = $product['product_cd'];
                    $order_detail['product_name']  = $product['name'];
                    $order_detail['variation_name']= $variation_name;
                    $order_detail['price']         = $price;
                    $order_detail['off']           = 0;//常に0にすることにした。上が値引きを考慮された金額だから。。。
                    $order_detail['su']            = $product['su'];
                    $order_detail['tax']           = $tax;
                    $wpdb->insert($order_detail_table, $order_detail);

                    //登録情報の移送（メール送信用）
                    $order_info_detail[$i]['product_name'] = $product['name'];
                    $order_info_detail[$i]['product_cd']   = $product['product_cd'];
                    $order_info_detail[$i]['quantity']     = $order_detail['su'];
                    $order_info_detail[$i]['total']        = ($order_detail['price'] - $order_detail['off'] + $order_detail['tax']) * $order_detail['su'];
                    $i++;
                }
                $order_info[$order_id]['items'] = $order_info_detail;
            }
        }

        //住所情報の登録／更新
        $query = "select count(*) as count from `{$user_table}` where `user_id`={$this->user_ID}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($results[0]['count']==0) {
                $user = array();
                $user['user_id']         = $this->user_ID;
                $user['send_first_name'] = $params['send_first_name'];
                $user['send_last_name']  = $params['send_last_name'];
                $user['send_first_furi'] = $params['send_first_furi'];
                $user['send_last_furi']  = $params['send_last_furi'];
                $user['send_address']    = $params['send_address'];
                $user['send_street']     = $params['send_street'];
                $user['send_state']      = $params['send_state'];
                $user['send_zip']        = $params['send_zip'];
                $user['send_tel']        = $params['send_tel'];
                $user['send_fax']        = $params['send_fax'];
                $user['send_mobile']     = $params['send_mobile'];
                $user['bill_first_name'] = $params['bill_first_name'];
                $user['bill_last_name']  = $params['bill_last_name'];
                $user['bill_first_furi'] = $params['bill_first_furi'];
                $user['bill_last_furi']  = $params['bill_last_furi'];
                $user['bill_address']    = $params['bill_address'];
                $user['bill_street']     = $params['bill_street'];
                $user['bill_state']      = $params['bill_state'];
                $user['bill_zip']        = $params['bill_zip'];
                $user['bill_tel']        = $params['bill_tel'];
                $user['bill_fax']        = $params['bill_fax'];
                $user['bill_mobile']     = $params['bill_mobile'];
                $wpdb->insert($user_table, $user);
            }
            else {
                $user = array();
                $user['send_first_name'] = $params['send_first_name'];
                $user['send_last_name']  = $params['send_last_name'];
                $user['send_first_furi'] = $params['send_first_furi'];
                $user['send_last_furi']  = $params['send_last_furi'];
                $user['send_address']    = $params['send_address'];
                $user['send_street']     = $params['send_street'];
                $user['send_state']      = $params['send_state'];
                $user['send_zip']        = $params['send_zip'];
                $user['send_tel']        = $params['send_tel'];
                $user['send_fax']        = $params['send_fax'];
                $user['send_mobile']     = $params['send_mobile'];
                $user['bill_first_name'] = $params['bill_first_name'];
                $user['bill_last_name']  = $params['bill_last_name'];
                $user['bill_first_furi'] = $params['bill_first_furi'];
                $user['bill_last_furi']  = $params['bill_last_furi'];
                $user['bill_address']    = $params['bill_address'];
                $user['bill_street']     = $params['bill_street'];
                $user['bill_state']      = $params['bill_state'];
                $user['bill_zip']        = $params['bill_zip'];
                $user['bill_tel']        = $params['bill_tel'];
                $user['bill_fax']        = $params['bill_fax'];
                $user['bill_mobile']     = $params['bill_mobile'];
                $wpdb->update($user_table, $user, array('user_id'=>$this->user_ID));
            }
        }

        //カートを空にする
        $query = "delete from `{$cart_table}` where `user_id`={$this->user_ID}";
        $wpdb->query($wpdb->prepare($query));

        foreach ($order_info as $info) {
            //店舗管理者にメール送信
            $mail_data = array();
            $mail_data['from']    = $info['user_id'];
            $mail_data['to']      = ($this->mode==SC_MODE_BP)?$info['store_id']:$info['store_email'];
            $mail_data['subject'] = __(sprintf(SCLNG_MAIL_ORDER_ADMIN_SUBJECT, SimpleCartFunctions::LPAD($info['order_id'], 8)), SC_DOMAIN);
            $mail_data['body']    = SimpleCartFunctions::TemplateConvert('mail/order_admin.txt', $info);
            $mres = sc_mail($mail_data);
            if (is_object($mres)) {
                //メッセージテーブルに登録
                $message = array();
                $message['order_id']   = $info['order_id'];
                $message['message_id'] = $mres->thread_id;
                $wpdb->insert($message_table, $message);
            }

            //注文者にオーダー控え情報をメール送信
            $mail_data = array();
            $mail_data['from']    = $info['store_id'];
            $mail_data['to']      = ($this->mode==SC_MODE_BP)?$info['user_id']:$info['user_email'];
            $mail_data['subject'] = __(sprintf(SCLNG_MAIL_ORDER_SUBJECT, $info['store_name']), SC_DOMAIN);
            $mail_data['body']    = SimpleCartFunctions::TemplateConvert('mail/order.txt', $info);
            $mres = sc_mail($mail_data);
            if (is_object($mres)) {
                //メッセージテーブルに登録
                $message = array();
                $message['order_id']   = $info['order_id'];
                $message['message_id'] = $mres->thread_id;
                $wpdb->insert($message_table, $message);
            }
        }

        return $status;
    }
}
