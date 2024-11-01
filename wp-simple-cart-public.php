<?php
/*
 * This file is part of wp-smple-cart widget.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 公開APIライブラリ
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     wp-simple-cart
 * @version     svn:$Id: wp-simple-cart-public.php 151036 2009-09-01 06:50:07Z tajima $
 */

/**
 * カート情報取得
 *
 */
function _sc_get_cart_info() {
    global $wpdb, $current_user;

    $cart_table    = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
    $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
    $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';

    $query = "
        select 
            a.*, 
            b.`notax`, 
            b.`price`, 
            b.`off`, 
            b.`download_publish`, 
            b.`download_price`, 
            b.`download_off`, 
            0 as variation_price,
            9 as download_publish
        from `{$cart_table}` as a, 
             `{$product_table}` as b 
        where a.`user_id`=" . $current_user->ID . " 
        and   a.`pricestock_id`=0
        and   a.`product_id`=b.`id` 
        and   b.`stock_manage`=0
        union all
        select 
            a.*, 
            b.`notax`, 
            b.`price`, 
            b.`off`, 
            b.`download_publish`, 
            b.`download_price`, 
            b.`download_off`, 
            0 as variation_price,
            9 as download_publish
        from `{$cart_table}` as a, 
             `{$product_table}` as b 
        where a.`user_id`=" . $current_user->ID . " 
        and   a.`pricestock_id`=0
        and   a.`product_id`=b.`id` 
        and   b.`stock_manage`=1 
        and   b.`stock`>0
        union all
        select 
            a.*, 
            b.`notax`, 
            b.`price`, 
            b.`off`, 
            b.`download_publish`, 
            b.`download_price`, 
            b.`download_off`, 
            c.`price` as variation_price,
            c.`download_publish`
        from `{$cart_table}` as a, 
             `{$product_table}` as b, 
             `{$priceandstock_table}` as c 
        where a.`user_id`=" . $current_user->ID . " 
        and   a.`product_id`=b.`id` 
        and   a.`pricestock_id`=c.`id` 
        and   b.`stock_manage`=0
        union all
        select 
            a.*, 
            b.`notax`, 
            b.`price`, 
            b.`off`, 
            b.`download_publish`, 
            b.`download_price`, 
            b.`download_off`, 
            c.`price` as variation_price,
            c.`download_publish`
        from `{$cart_table}` as a, 
             `{$product_table}` as b, 
             `{$priceandstock_table}` as c 
        where a.`user_id`=" . $current_user->ID . " 
        and   a.`product_id`=b.`id` 
        and   a.`pricestock_id`=c.`id` 
        and   b.`stock_manage`=1 
        and   b.`stock`>0";
    $results = $wpdb->get_results($query);
    if (is_array($results)) {
        return $results;
    }
    else {
        return false;
    }
}

/**
 * カート金額取得
 *
 */
function sc_get_total_cost() {
    $cart_info = _sc_get_cart_info();
    $cost = 0;
    foreach ($cart_info as $cart) {
        $params = array();
        $params['id']               = $cart->product_id;
        $params['store_id']         = $cart->store_id;
        $params['notax']            = $cart->notax;
        $params['price']            = $cart->price;
        $params['off']              = $cart->off;
        $params['download_publish'] = $cart->download_publish;
        $params['download_price']   = $cart->download_price;
        $params['download_off']     = $cart->download_off;
        //規格連携無し
        if ($cart->pricestock_id==0) {
            $price = SimpleCartFunctions::Price($params);
            $off   = SimpleCartFunctions::OffPrice($params);
            if ($cart->download=='1' && $cart->download_publish=='2') {
                $price = SimpleCartFunctions::DownloadPrice($params);
                $off = SimpleCartFunctions::DownloadOffPrice($params);
            }
        }
        //規格連携有り
        else {
            if ($params['download_publish']==0) {
                $params['price'] = $cart->variation_price;
                $price = SimpleCartFunctions::Price($params);
                $off   = SimpleCartFunctions::OffPrice($params);
            }
            else {
                $params['download_price'] = $cart->variation_price;
                $price = SimpleCartFunctions::DownloadPrice($params);
                $off   = SimpleCartFunctions::DownloadOffPrice($params);
            }
        }
        if ($price <> $off) {
            $price = $off;
        }
        $cost += $cart->su * $price;
    }
    return SimpleCartFunctions::MoneyFormat($cost);
}

/**
 * カートSKU数取得
 *
 */
function sc_get_sku() {
    $cart_info = _sc_get_cart_info();
    $sku = array();
    foreach ($cart_info as $cart) {
        $sku[$cart->product_id . '_' . $cart->pricestock_id] = $cart;
    }
    return number_format(count($sku));
}

/**
 * 商品をランダムで取得する
 * 
 */
function sc_get_random_item(&$i=0) {
    global $wpdb;

    $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
    $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';

    //まず店舗をランダムで１店舗に絞る
    $query = "select * from `{$store_table}` where `user_status`=" . SC_USER_ENABLE;
    $stores = $wpdb->get_results($query, ARRAY_A);
    if (is_array($stores)) {
        $store_key = array_rand($stores);
    }
    else {
        return false;
    }
    $query = "select * from `{$product_table}` where `publish`=1 and `store_id`={$stores[$store_key]['user_id']}";
    $products = $wpdb->get_results($query, ARRAY_A);
    if (is_array($products)) {
        $product_key = array_rand($products);
    }
    $product = $products[$product_key];
    if (!isset($product)||is_null($product)||$product===false) {
        if ($i < 10) {
            $i++;
            $product = sc_get_random_item($i);
        }
    }

    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    $product = SimpleCartPublicModel::getProduct(array('product_id'=>$product['id']));
    return $product;
}

/**
 * 新着商品をN件取得する
 * 
 */
function sc_get_new_item($i=3) {
    global $wpdb;

    $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';

    $query = "select a.* from `{$product_table}` as a left outer join `{$wpdb->users}` as b on (a.`store_id`=b.`ID`) where b.`user_status`=" . SC_USER_ENABLE . " and a.`publish`=1 order by a.`regist_date` desc limit {$i}";
    $products = $wpdb->get_results($query, ARRAY_A);

    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    if (is_array($products)) {
        $p = array();
        foreach ($products as $product) {
            $p[] = SimpleCartPublicModel::getProduct(array('product_id'=>$product['id']));
        }
        return $p;
    }
    else {
        return false;
    }
}

/**
 * 売筋商品をN件取得する
 * 
 * @param mon  : 0=当月, 1=先月, ...
 * @param type : kin=売上金額, su=売上数量
 * 
 */
function sc_get_best_item($i=5, $mon, $type='kin') {
    global $wpdb;

    $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
    $order_detal_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
    $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';

    $rand = SimpleCartFunctions::Rand8();

    $tm = time();

    $yymm = date('Y-m', mktime(0, 0, 0, (date('m', $tm) - $mon), 1, date('Y', $tm)));

    $query = "
        select a.* 
        from (
                select
                    a.`store_id`, 
                    b.`product_id`, 
                    sum(b.`price`-b.`off`) as kin,
                    sum(b.su) as su
                from {$order_table} as a, 
                     {$order_detal_table} as b, 
                     {$product_table} as c, 
                     {$wpdb->users} as d
                where a.`id` = b.`order_id`
                and   b.`product_id` = c.`id`
                and   c.`store_id` = d.`ID`
                and   d.`user_status`=" . SC_USER_ENABLE . "
                and   a.`regist_date` like '%".$yymm."%'
                and   c.`publish`=1
                group by 
                    a.`store_id`, 
                    b.`product_id`
            ) as a
        order by a.`{$type}` desc  limit {$i}";
    $results = $wpdb->get_results($query, ARRAY_A);

    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    if (is_array($results)) {
        $p = array();
        foreach ($results as $product) {
            $p[] = $product + SimpleCartPublicModel::getProduct(array('product_id'=>$product['product_id']));
        }
        return $p;
    }
    else {
        return false;
    }
}
