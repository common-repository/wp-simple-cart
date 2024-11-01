<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Tax Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreTaxModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreTaxModel extends SimpleCartCommonModel {

    /**
     * 消費税情報取得
     *
     */
    function getTax() {
        global $wpdb;

        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $query = "select * from {$tax_table} where `store_id`={$this->user_ID}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 消費税情報更新前チェック
     *
     */
    function checkTax($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['tax']) || $params['tax']=='') {
            $error->errors['tax'] = __(SCLNG_CHECK_REQUIRED_TAX, SC_DOMAIN);
        }
        else if (!is_numeric($params['tax'])) {
            $error->errors['tax'] = __(SCLNG_CHECK_NUMERIC_TAX, SC_DOMAIN);
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 消費税情報更新
     *
     */
    function saveTax($params) {
        global $wpdb;

        //sc_tax作成
        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        if (is_null($params['id'])||$params['id']=='') {
        }
        else {
            $wpdb->update($tax_table, array('tax'=>$params['tax'],'method'=>$params['method']), array('id'=>$params['id']));
        }
    }
}
