<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Delivery Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreDeliveryModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreDeliveryModel extends SimpleCartCommonModel {

    /**
     * 配送先一覧取得
     *
     */
    function getDeliveryList() {
        global $wpdb;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        $query = "select * from {$delivery_table} where store_id={$this->user_ID} order by `sort`, `name`";
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
     * 配送先情報取得
     *
     */
    function getDelivery($id) {
        global $wpdb;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        $query = "select * from {$delivery_table} where `id`={$id}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 配送先情報入力チェック
     *
     */
    function checkDelivery($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['name']) || $params['name']=='') {
            $error->errors['delivery_name'] = __(SCLNG_CHECK_REQUIRED_DELIVERY_NAME, SC_DOMAIN);
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
     * 配送先登録／更新
     *
     */
    function saveDelivery($params) {
        global $wpdb;

        //sc_delivery作成
        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($delivery_table, array('store_id'=>$this->user_ID,'name'=>$params['name'],'sort'=>$params['sort']));
            SimpleCartAdminModel::createDeliveryValues($this->user_ID, $wpdb->insert_id);
        }
        else {
            $wpdb->update($delivery_table, array('store_id'=>$this->user_ID,'name'=>$params['name'],'sort'=>$params['sort']), array('id'=>$params['id']));
        }
    }

    /**
     * 配送先削除
     *
     */
    function deleteDeliverys($params) {
        global $wpdb;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        if (is_array($params)) {
            foreach ($params as $id) {
                $query = "delete from `{$delivery_table}` where `id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$delivery_values_table}` where `delivery_id`={$id}";
                $wpdb->query($wpdb->prepare($query));
            }
        }
    }

    /**
     * 並び順更新前チェック
     *
     */
    function checkDeliverySortOrder($params) {
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
    function saveDeliverySortOrder($params) {
        global $wpdb;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                $wpdb->update($delivery_table, array('sort'=>$v), array('id'=>$k));
            }
        }
        return true;
    }

    /**
     * 配送地域一覧取得
     *
     */
    function getDeliveryValuesList($id) {
        global $wpdb;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        $query = "select * from {$delivery_values_table} where `delivery_id`={$id} order by `sort`, `name`";
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
     * 配送地域取得
     *
     */
    function getDeliveryValues($id) {
        global $wpdb;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        $query = "select * from {$delivery_values_table} where `id`={$id}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 配送地域情報入力チェック
     *
     */
    function checkDeliveryValues($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['name']) || $params['name']=='') {
            $error->errors['delivery_values_name'] = __(SCLNG_CHECK_REQUIRED_DELIVERY_VALUES_NAME, SC_DOMAIN);
        }
        if (is_null($params['delivery_fee']) || $params['delivery_fee']=='') {
            $error->errors['delivery_fee'] = __(SCLNG_CHECK_REQUIRED_DELIVERY_FEE, SC_DOMAIN);
        }
        else if (!is_numeric($params['delivery_fee'])) {
            $error->errors['delivery_fee'] = __(SCLNG_CHECK_NUMERIC_DELIVERY_FEE, SC_DOMAIN);
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 配送地域登録／更新
     *
     */
    function saveDeliveryValues($params) {
        global $wpdb;

        //sc_delivery_values作成
        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($delivery_values_table, array('store_id'=>$this->user_ID,'delivery_id'=>$params['delivery_id'],'name'=>$params['name'],'delivery_fee'=>$params['delivery_fee'],'sort'=>$params['sort']));
        }
        else {
            $wpdb->update($delivery_values_table, array('store_id'=>$this->user_ID,'delivery_id'=>$params['delivery_id'],'name'=>$params['name'],'delivery_fee'=>$params['delivery_fee'],'sort'=>$params['sort']), array('id'=>$params['id']));
        }
    }

    /**
     * 配送地域削除
     *
     */
    function deleteDeliveryValues($params) {
        global $wpdb;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        if (is_array($params)) {
            foreach ($params as $id) {
                $query = "delete from `{$delivery_values_table}` where `id`={$id}";
                $wpdb->query($wpdb->prepare($query));
            }
        }
    }

    /**
     * 配送料金更新前チェック
     *
     */
    function checkDeliveryValuesFee($params) {
        if (is_array($params)) {
            //本来の正しい使い方してないけど、まあメンドイからこれで。。。
            $error = new WP_Error();
            foreach ($params as $k=>$v) {
                if (is_null($v) || $v=='') {
                    $error->errors['delivery_fee'][$k] = __(SCLNG_CHECK_REQUIRED_DELIVERY_FEE, SC_DOMAIN);
                }
                else if (!is_numeric($v)) {
                    $error->errors['delivery_fee'][$k] = __(SCLNG_CHECK_NUMERIC_DELIVERY_FEE, SC_DOMAIN);
                }
            }
            if (count($error->errors)>0) {
                return $error;
            }
        }
        return true;
    }

    /**
     * 配送料金更新
     *
     */
    function saveDeliveryValuesFee($params) {
        global $wpdb;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                $wpdb->update($delivery_values_table, array('delivery_fee'=>$v), array('id'=>$k));
            }
        }
        return true;
    }

    /**
     * 並び順更新前チェック
     *
     */
    function checkDeliveryValuesSortOrder($params) {
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
    function saveDeliveryValuesSortOrder($params) {
        global $wpdb;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                $wpdb->update($delivery_values_table, array('sort'=>$v), array('id'=>$k));
            }
        }
        return true;
    }

    /**
     * 配送料金無料オプション取得
     *
     */
    function getDeliveryLimitOption() {
        global $wpdb;

        $delivery_free_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_free';
        $query = "select * from {$delivery_free_table} where store_id={$this->user_ID}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 配送料金無料オプション入力前チェック
     *
     */
    function checkDeliveryLimitOption($params) {
        if (is_array($params)) {
            //本来の正しい使い方してないけど、まあメンドイからこれで。。。
            $error = new WP_Error();
            if ($params['price_limit']!='' && !is_numeric($params['price_limit'])) {
                $error->errors['price_limit'] = __(SCLNG_CHECK_NUMERIC_PRICE_LIMIT, SC_DOMAIN);
            }
            if (count($error->errors)>0) {
                return $error;
            }
        }
        return true;
    }

    /**
     * 配送料金無料オプション更新
     *
     */
    function saveDeliveryLimitOption($params) {
        global $wpdb;

        //sc_delivery_free作成
        $delivery_free_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_free';
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($delivery_free_table, array('store_id'=>$this->user_ID,'price_limit'=>$params['price_limit']));
        }
        else {
            $wpdb->update($delivery_free_table, array('store_id'=>$this->user_ID,'price_limit'=>$params['price_limit']), array('id'=>$params['id']));
        }
    }
}
