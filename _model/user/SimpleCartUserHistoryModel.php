<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile User History Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserHistoryModel.php 140359 2009-07-29 09:13:26Z tajima $
 */

class SimpleCartUserHistoryModel extends SimpleCartCommonModel {

    /**
     * 購入履歴情報取得
     *
     */
    function getOrderList($params, $count=false) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $store_table = $wpdb->users;

        $query = '';
        $page_cond = '';
        //購入履歴情報取得の場合
        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $page_cond = " limit " . $offset . ", " . $limit;
            }
            $query = "select a.*, b.`display_name` as store_name from `{$order_table}` as a, `{$store_table}` as b where a.`user_id`={$this->user_ID} and a.`store_id`=b.`ID` order by `regist_date` desc, `id` desc " . $page_cond;
        }
        //件数情報取得の場合
        else {
            $query = "select count(id) as count from `{$order_table}` where `user_id`={$this->user_ID}";
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
     * 購入履歴詳細情報取得
     *
     */
    function getOrder($params) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $store_table = $wpdb->users;

        //オーダーの頭情報取得
        $query = "
                select a.*, b.`display_name` as store_name 
                from `{$order_table}` as a
                     left outer join `{$store_table}` as b on (a.`store_id`=b.`ID`)
                where a.`id`={$params['order_id']} 
                order by `regist_date` desc, `id` desc ";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            //オーダーの詳細情報取得
            $query = "
                    select 
                        a.*, 
                        b.`store_id`,
                        b.`image_file_url1`, 
                        b.`image_file_url2`, 
                        b.`image_file_url3`, 
                        b.`product_url`, 
                        b.`categorys` 
                    from `{$order_detail_table}` as a
                         left outer join `{$product_table}` as b on (a.`product_id`=b.`id`)
                    where a.`order_id`={$params['order_id']} 
                    order by a.`id` desc ";
            $detail = $wpdb->get_results($query, ARRAY_A);
            $res = array();
            foreach ($detail as $d) {
                $d['categorys'] = unserialize($d['categorys']);
                $res[$d['id']] = $d;
            }
            $results[0]['data'] = $res;
            return $results[0];
        }
        else {
            return false;
        }
    }
}
