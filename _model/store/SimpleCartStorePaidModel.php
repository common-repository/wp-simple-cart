<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Paid Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStorePaidModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStorePaidModel extends SimpleCartCommonModel {

    /**
     * 支払方法一覧取得
     *
     */
    function getPaidList() {
        global $wpdb;

        $paid_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        $query = "select * from {$paid_table} where store_id={$this->user_ID} order by `sort`,`name`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $res = array();
            foreach ($results as $r) {
                $res[$r['id']] = $r;
            }
            return $res;
        }
        else {
            return false;
        }
    }

    /**
     * 支払方法情報取得
     *
     */
    function getPaid($id) {
        global $wpdb;

        $paid_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        $query = "select * from {$paid_table} where `id`={$id}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 支払方法更新前チェック
     *
     */
    function checkPaid($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['name']) || $params['name']=='') {
            $error->errors['paid_name'] = __(SCLNG_CHECK_REQUIRED_PAID_METHOD_NAME, SC_DOMAIN);
        }
        if (is_null($params['commission']) || $params['commission']=='') {
            $error->errors['commission'] = __(SCLNG_CHECK_REQUIRED_COMMISSION, SC_DOMAIN);
        }
        else if (!is_numeric($params['commission'])) {
            $error->errors['commission'] = __(SCLNG_CHECK_NUMERIC_COMMISSION, SC_DOMAIN);
        }
        if (is_null($params['sort']) || $params['sort']=='') {
            $error->errors['sort'] = __(SCLNG_CHECK_REQUIRED_SORT_ORDER, SC_DOMAIN);
        }
        else if (!is_numeric($params['sort'])) {
            $error->errors['sort'] = __(SCLNG_CHECK_NUMERIC_SORT_ORDER, SC_DOMAIN);
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 支払方法登録／更新
     *
     */
    function savePaid($params) {
        global $wpdb;

        //sc_paid_method作成
        $paid_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($paid_table, array('store_id'=>$this->user_ID,'name'=>$params['name'],'commission'=>$params['commission'],'sort'=>$params['sort']));
        }
        else {
            $wpdb->update($paid_table, array('store_id'=>$this->user_ID,'name'=>$params['name'],'commission'=>$params['commission'],'sort'=>$params['sort']), array('id'=>$params['id']));
        }
    }

    /**
     * 支払方法削除
     *
     */
    function deletePaids($params) {
        global $wpdb;

        $paid_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        if (is_array($params)) {
            foreach ($params as $id) {
                $query = "delete from `{$paid_table}` where `id`={$id}";
                $wpdb->query($wpdb->prepare($query));
            }
        }
    }

    /**
     * 並び順更新前チェック
     *
     */
    function checkPaidSortOrder($params) {
        if (is_array($params)) {
            //本来の正しい使い方してないけど、まあメンドイからこれで。。。
            $error = new WP_Error();
            foreach ($params as $k=>$v) {
                if (is_null($v) || $v=='') {
                    $error->errors['sort'][$k] = __(SCLNG_CHECK_REQUIRED_SORT_ORDER, SC_DOMAIN);
                }
                else if (!is_numeric($v)) {
                    $error->errors['sort'][$k] = __(SCLNG_CHECK_NUMERIC_SORT_ORDER, SC_DOMAIN);
                }
            }
            if (count($error->errors)>0) {
                return $error;
            }
        }
        return true;
    }

    /**
     * 並び順更新
     *
     */
    function savePaidSortOrder($params) {
        global $wpdb;

        $paid_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                $wpdb->update($paid_table, array('sort'=>$v), array('id'=>$k));
            }
        }
        return true;
    }
}
