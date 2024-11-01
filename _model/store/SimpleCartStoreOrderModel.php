<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Order Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreOrderModel.php 143626 2009-08-07 12:22:47Z tajima $
 */

class SimpleCartStoreOrderModel extends SimpleCartCommonModel {

    /**
     * 受注一覧情報取得
     *
     */
    function getOrderList($params, $count=false) {
        global $wpdb;

        $order_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $store_table      = $wpdb->users;
        $user_table       = $wpdb->users;
        $delivery_table   = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        $delivery_v_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        $paid_table       = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';

        $cond = array();
        $cond[] = "a.`store_id` = " . $this->user_ID;
        if (isset($params['s_order_status']) && $params['s_order_status']!='') {
            $cond[] = "a.`status` = '" . $params['s_order_status'] . "'";
        }
        if (isset($params['s_order_no']) && $params['s_order_no']!='') {
            $cond[] = "a.`id` like '%" . ltrim($params['s_order_no'], '0') . "%'";
        }
        $fy = 0;
        $fm = 1;
        $fd = 1;
        $ty = 0;
        $tm = 12;
        $td = 31;
        $fy = $params['s_order_fyy'];
        if (isset($params['s_order_fmm']) && $params['s_order_fmm']!='0') {
            $fm = $params['s_order_fmm'];
        }
        if (isset($params['s_order_fdd']) && $params['s_order_fdd']!='') {
            $fd = $params['s_order_fdd'];
        }
        if ($fy!=0) {
            $fymd = date('Y-m-d H:i:s', mktime(0, 0, 0, $fm, $fd, $fy));
            $cond[] = "a.`regist_date` >= '" . $fymd . "'";
        }
        $ty = $params['s_order_tyy'];
        if (isset($params['s_order_tmm']) && $params['s_order_tmm']!='0') {
            $tm = $params['s_order_tmm'];
        }
        if (isset($params['s_order_tdd']) && $params['s_order_tdd']!='') {
            $td = $params['s_order_tdd'];
        }
        if ($ty!=0) {
            $tymd = date('Y-m-d H:i:s', mktime(23, 59, 59, $tm, $td, $ty));
            $cond[] = "a.`regist_date` <= '" . $tymd . "'";
        }
        $where = '';
        if (count($cond) > 0) {
            $where = 'where ' . @implode(' and ', $cond);
        }

        $query = '';
        $page_cond = '';
        //受注一覧情報取得の場合
        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $page_cond = " limit " . $offset . ", " . $limit;
            }
            $query = "
                    select 
                        a.*, 
                        b.`display_name` as store_name, 
                        c.`display_name` as user_name, 
                        d.`name` as deliver_name, 
                        e.`name` as deliver_value_name, 
                        f.`name` as paid_method_name
                    from 
                        `{$order_table}` as a 
                        left outer join `{$store_table}` as b on (a.`store_id`=b.`ID`) 
                        left outer join `{$user_table}` as c on (a.`user_id`=c.`ID`) 
                        left outer join `{$delivery_table}` as d on (a.`deliver_id`=d.`id`) 
                        left outer join `{$delivery_v_table}` as e on (a.`deliver_value_id`=e.`id`) 
                        left outer join `{$paid_table}` as f on (a.`paid_method`=f.`id`) 
                         " . $where . " order by a.`regist_date` desc, a.`id` desc " . $page_cond;
        }
        //件数情報取得の場合
        else {
            $query = "select count(a.`id`) as count from `{$order_table}` as a " . $where;
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($count==false) {
                global $sc_delivery_times;
                $order = array();
                foreach ($results as $r) {
                    $r['deliver_time_name'] = $sc_delivery_times[$r['delivery_time']];
                    $order[] = $r;
                }
                return $order;
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
     * 受注情報取得
     *
     */
    function getOrder($params) {
        global $wpdb;

        $order_table        = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
        $product_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $store_table        = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';

        //オーダーの頭情報取得
        $query = "
                select a.*, b.`display_name` as store_name, c.`display_name` as user_name 
                       , b.`user_url` as store_url, d.`pobox` as store_pobox, d.`address` as store_address, d.`street` as store_street, d.`city` as store_city, d.`state` as store_state, d.`zip` as store_zip, d.`country` as store_country, d.`tel` as store_tel, d.`fax` as store_fax, d.`mobile` as store_mobile
                from `{$order_table}` as a
                     left outer join `{$wpdb->users}` as b on (a.`store_id`=b.`ID`)
                     left outer join `{$wpdb->users}` as c on (a.`user_id`=c.`ID`)
                     left outer join `{$store_table}` as d on (a.`store_id`=d.`user_id`)
                where a.`id`={$params['order_id']} 
                order by `regist_date` desc, `id` desc ";

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            //オーダーの詳細情報取得
            $query = "
                    select 
                        a.*, 
                        b.`store_id`, 
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

    /**
     * 受注削除
     *
     */
    function deleteOrder($params) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';

        //受注データの削除
        $query = "delete from `{$order_table}` where `id`={$params['order_id']}";
        $wpdb->query($wpdb->prepare($query));

        //受注詳細データの削除
        $query = "delete from `{$order_detail_table}` where `order_id`={$params['order_id']}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 受注ステータス変更
     *
     */
    function saveOrderStatus($params) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';

        //受注ステータスの変更
        $wpdb->update($order_table, array('status'=>$params['status']), array('id'=>$params['order_id']));

        //ダウンロード製品の入金があった場合
        //　入金済みの場合、ダウンロード用ディレクトリへ移動
        //　その他の場合、ダウンロード用ディレクトリの削除
        if ($params['download']==1) {
            if ($params['status']==SC_MONEY_RECEIVED) {
                SimpleCartStoreOrderModel::publicDownlodFile($params['order_id']);
            }
            else {
                SimpleCartStoreOrderModel::closeDownlodFile($params['order_id']);
            }
        }
    }

    /**
     * 入金済みステータスの場合、製品ファイルを公開する
     *
     */
    function publicDownlodFile($order_id) {
        global $wpdb;

        $order_table        = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
        $product_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $message_table      = $wpdb->base_prefix . $wpdb->blogid . '_sc_message';
        $user_table         = $wpdb->base_prefix . $wpdb->blogid . '_sc_user';


        //受注取得
        $query = "
            select 
                b.*, 
                c.`ID` as user_id, 
                c.`display_name` as user_name, 
                c.`user_email`, 
                d.`bill_last_name`,
                d.`bill_first_name`,
                e.`ID` as store_id, 
                e.`display_name` as store_name 
            from 
                `{$order_table}` as a, 
                `{$order_detail_table}` as b, 
                `{$wpdb->users}` as c, 
                `{$user_table}` as d, 
                `{$wpdb->users}` as e 
            where a.`id`={$order_id} and a.`id`=b.`order_id` and a.`user_id`=c.`ID` and a.`user_id`=d.`user_id` and a.`store_id`=e.`ID`";
        $order_detail_list = $wpdb->get_results($query, ARRAY_A);

        if (is_array($order_detail_list)) {
            foreach ($order_detail_list as $order_detail) {
                if ($order_detail['download']=='1') {
                    //製品ファイル取得
                    $query = "select * from `{$product_table}` where `id`={$order_detail['product_id']}";
                    $product = $wpdb->get_results($query, ARRAY_A);
                    if (is_array($product)) {
                        //ファイル移動
                        $down_dir = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/download';
                        $temp_dir = $down_dir . '/' . $order_detail['download_hash'];
                        $to_product_file = $temp_dir . '/' . $product[0]['product_url'];
                        $from_product_file = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/' . $order_detail['store_id'] . '/P_' . SimpleCartFunctions::LPAD($product[0]['id'], 6) . '/download_product/' . $product[0]['product_url'];
                        SimpleCartFunctions::Mkdir($down_dir);
                        SimpleCartFunctions::Mkdir($temp_dir);
                        copy($from_product_file, $to_product_file);
                        //製品ダウンロードメール送信
                        $info = array();
                        $info['store_id']    = $order_detail['store_id'];
                        $info['store_name']  = $order_detail['store_name'];
                        $info['user_id']     = $order_detail['user_id'];
                        $info['user_email']  = $order_detail['user_email'];
                        $info['bill_name']   = $order_detail['bill_last_name'] . ' ' . $order_detail['bill_first_name'];
                        $info['download_url'] = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/files/download/' . $order_detail['download_hash'] . '/' . $product[0]['product_url'];
                        $mail_data = array();
                        $mail_data['from']    = $info['store_id'];
                        $mail_data['to']      = ($this->mode==SC_MODE_BP)?$info['user_id']:$info['user_email'];
                        $mail_data['subject'] = __(sprintf(SCLNG_MAIL_DOWNLOAD_SUBJECT, $info['store_name']), SC_DOMAIN);
                        $mail_data['body']    = SimpleCartFunctions::TemplateConvert('mail/product_download.txt', $info);
                        $mres = sc_mail($mail_data);
                        if (is_object($mres)) {
                            //メッセージテーブルに登録
                            $message = array();
                            $message['order_id']   = $order_id;
                            $message['message_id'] = $mres->thread_id;
                            $wpdb->insert($message_table, $message);
                        }
                    }
                }
            }
        }
    }

    /**
     * 入金済みステータスの以外の場合、製品ファイルをクローズする
     *
     */
    function closeDownlodFile($order_id) {
        global $wpdb;

        $order_detail_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';

        //受注取得
        $query = "select * from `{$order_detail_table}` where `order_id`={$order_id}";
        $order_detail_list = $wpdb->get_results($query, ARRAY_A);

        if (is_array($order_detail_list)) {
            foreach ($order_detail_list as $order_detail) {
                if ($order_detail['download']=='1') {
                    //製品ファイル取得
                    $query = "select * from `{$product_table}` where `id`={$order_detail['product_id']}";
                    $product = $wpdb->get_results($query, ARRAY_A);
                    if (is_array($product)) {
                        //ファイル削除
                        $down_dir = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/download/' . $order_detail['download_hash'];
                        SimpleCartFunctions::Rm($down_dir);
                    }
                }
            }
        }
    }
}
