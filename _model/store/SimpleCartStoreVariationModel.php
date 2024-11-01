<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Variation Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreVariationModel.php 140849 2009-07-30 09:07:35Z tajima $
 */

class SimpleCartStoreVariationModel extends SimpleCartCommonModel {

    /**
     * 規格一覧取得
     *
     */
    function getVariationList() {
        global $wpdb;

        $variation_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';
        $query = "select * from `{$variation_table}` where `store_id`={$this->user_ID} order by `id`";
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
     * 規格情報取得
     *
     */
    function getVariation($params) {
        global $wpdb;

        $variation_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';
        $query = "select * from {$variation_table} where `id`={$params['id']}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $values = SimpleCartStoreVariationModel::getVariationValuesList($params);
            $results[0]['values_value'] = $values;
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 規格詳細一覧取得
     *
     */
    function getVariationValuesList($params) {
        global $wpdb;

        $variation_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';
        $query = "select * from `{$variation_values_table}` where `variation_id`={$params['id']} order by `id`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $res = array();
            foreach ($results as $r) {
                $res[$r['id']] = $r['name'];
            }
            return $res;
        }
        else {
            return false;
        }
    }

    /**
     * 規格情報入力チェック
     *
     */
    function checkVariation($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['name']) || $params['name']=='') {
            $error->errors['variation_name'] = __(SCLNG_CHECK_REQUIRED_VARIATION_NAME, SC_DOMAIN);
        }
        foreach ($params['values_value'] as $key=>$value) {
            if (is_null($value) || $value=='') {
                $error->errors['value_name'][$key] = __(SCLNG_CHECK_REQUIRED_VARIATION_VALUE_NAME, SC_DOMAIN);
            }
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 規格登録／更新
     *
     */
    function saveVariation($params) {
        global $wpdb;

        $variation_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';
        $variation_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';

        //sc_product_variations作成
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($variation_table, array('store_id'=>$this->user_ID,'name'=>$params['name']));
            $variation_id = $wpdb->insert_id;
        }
        else {
            $wpdb->update($variation_table, array('store_id'=>$this->user_ID,'name'=>$params['name']), array('id'=>$params['id']));
            $variation_id = $params['id'];
        }

        //sc_variation_values不要レコード削除
        $val_list = array();
        foreach ($params['values_value'] as $key=>$val) {
            $val_list[] = $key;
        }
        $query = "
            delete from `{$variation_values_table}` 
            where `store_id`={$this->user_ID} 
            and   `variation_id`={$variation_id}
            and   `id` not in (".@implode(',', $val_list).")";
        $wpdb->query($wpdb->prepare($query));

        //sc_variation_values作成
        foreach ($params['values_value'] as $key=>$val) {
            if (is_null($key)||$key==''||$key<1) {
                $wpdb->insert($variation_values_table, array('store_id'=>$this->user_ID,'name'=>$val,'variation_id'=>$variation_id));
            }
            else {
                $wpdb->update($variation_values_table, array('store_id'=>$this->user_ID,'name'=>$val,'variation_id'=>$variation_id), array('id'=>$key));
            }
        }
    }

    /**
     * 規格削除
     *
     */
    function deleteVariations($params) {
        global $wpdb;

        $variation_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';
        $variation_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';

        if (is_array($params)) {
            foreach ($params as $id) {
                $query = "delete from `{$variation_table}` where `id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$variation_values_table}` where `variation_id`={$id}";
                $wpdb->query($wpdb->prepare($query));
            }
        }
    }
}
