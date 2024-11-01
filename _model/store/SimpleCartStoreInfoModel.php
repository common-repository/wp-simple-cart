<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Info Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreInfoModel.php 141652 2009-08-01 06:16:59Z tajima $
 */

class SimpleCartStoreInfoModel extends SimpleCartCommonModel {

    /**
     * 受注状況一覧情報取得
     *
     */
    function getOrderList($params) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';

        //受注一覧情報取得
        $query = "select `status`, count(`id`) as count from `{$order_table}` where `store_id`={$this->user_ID} and `regist_date`>='{$params['ymd']}' group by `status` order by `status`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $order = array();
            foreach ($results as $r) {
                $order[$r['status']] = $r['count'];
            }
            return $order;
        }
        else {
            return false;
        }
    }

    /**
     * 売上一覧情報取得
     *
     */
    function getSalesList($params) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';

        $status0 = SC_NEW_RECEIPT;
        $status1 = SC_PAYMENT_WAITING;
        $status2 = SC_MONEY_RECEIVED;
        $status3 = SC_SENT_OUT;
        $status4 = SC_BACK_ORDER;

        //売上一覧情報取得
        $query = "
            select 
                sum(case when `regist_date`>='{$params['yymm00']}' then (`total`-`commission`-`deliver_fee`) else 0 end) as yymm00, 
                sum(case when `regist_date`>='{$params['yymm01']}' and `regist_date`<'{$params['yymm00']}' then `total`-`commission`-`deliver_fee` else 0 end) as yymm01, 
                sum(case when `regist_date`>='{$params['yymm02']}' and `regist_date`<'{$params['yymm01']}' then `total`-`commission`-`deliver_fee` else 0 end) as yymm02, 
                sum(case when `regist_date`>='{$params['yymm03']}' and `regist_date`<'{$params['yymm02']}' then `total`-`commission`-`deliver_fee` else 0 end) as yymm03, 
                sum(`total`) as total 
            from `{$order_table}` 
            where `store_id`   = {$this->user_ID} 
            and   `status` in ({$status0}, {$status1}, {$status2}, {$status3}, {$status4})
            and   `regist_date`>='{$params['ymd']}'";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 在庫情報取得
     *
     */
    function getStockList($params) {
        global $wpdb;

        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';

        //在庫不足情報取得
        $query = "
            select
                a.`id`,
                a.`product_cd`,
                a.`name`,
                sum(case when c.`regist_date`>='{$params['yymm00']}' then `su` else 0 end) as yymm00, 
                sum(case when c.`regist_date`>='{$params['yymm01']}' and c.`regist_date`<'{$params['yymm00']}' then `su` else 0 end) as yymm01,
                sum(case when c.`regist_date`>='{$params['yymm02']}' and c.`regist_date`<'{$params['yymm01']}' then `su` else 0 end) as yymm02,
                sum(case when c.`regist_date`>='{$params['yymm03']}' and c.`regist_date`<'{$params['yymm02']}' then `su` else 0 end) as yymm03
            from (select * from `{$product_table}` where `store_id`={$this->user_ID} and `stock_manage`=1 and `stock`<1) as a
                 left outer join `{$order_detail_table}` as b on (a.`id`=b.`product_id`)
                 left outer join `{$order_table}` as c on (b.`order_id`=c.`id`)
            group by
                a.`id`,
                a.`product_cd`,
                a.`name`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results;
        }
        else {
            return false;
        }
    }
}
