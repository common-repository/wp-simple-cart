<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Variation Relation Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreVariationRelationModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreVariationRelationModel extends SimpleCartCommonModel {

    /**
     * 製品情報取得
     *
     */
    function getProduct($params) {
        global $wpdb;

        if (is_null($params['product_id']) || $params['product_id']=='') {
            return false;
        }

        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $tmp_product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';

        $query = "
            select 
                a.* ,
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
            where a.`id`={$params['product_id']} ";

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $results[0]['categorys'] = unserialize($results[0]['categorys']);
            SimpleCartFunctions::CalcPrices($results[0]);
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 規格一覧取得
     *
     */
    function getVariations() {
        global $wpdb;

        $variation_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';

        $query = "select * from {$variation_table} where `store_id`={$this->user_ID} order by `id`";
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
     * 規格詳細取得
     *
     */
    function getVariationValues($params) {
        global $wpdb;

        $values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';

        $query = "select * from {$values_table} where `store_id`={$this->user_ID} and `variation_id`={$params['variation_id']} order by `id`";
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
            where `store_id`={$this->user_ID} 
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
            where a.`store_id`={$this->user_ID} 
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
     * 規格詳細入力チェック
     *
     */
    function checkVariationValues($params) {
        $values_stock = $params['values_stock'];
        $values_price = $params['values_price'];

        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_array($values_stock)) {
            foreach ($values_stock as $key=>$stock) {
                if (is_null($stock) || $stock=='' || $stock < 0) {
                    $error->errors['stock'][$key] = __(SCLNG_CHECK_REQUIRED_STOCK, SC_DOMAIN);
                }
                else if (!is_numeric($stock)) {
                    $error->errors['stock'][$key] = __(SCLNG_CHECK_NUMERIC_STOCK, SC_DOMAIN);
                }
            }
        }
        if (is_array($values_price)) {
            foreach ($values_price as $key=>$price) {
                if (is_null($price) || $price=='' || $price < 0) {
                    $error->errors['price'][$key] = __(SCLNG_CHECK_REQUIRED_PRICE, SC_DOMAIN);
                }
                else if (!is_numeric($price)) {
                    $error->errors['price'][$key] = __(SCLNG_CHECK_NUMERIC_PRICE, SC_DOMAIN);
                }
            }
        }

        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 規格詳細登録
     *
     */
    function saveVariationValues($params) {
        global $wpdb;

        $associations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_associations';
        $values_associations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values_associations';
        $priceandstock_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
        $combinations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';
        $tmp_product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';

        //sc_variation_associations削除
        $query = "delete from `{$associations_table}` where `store_id`={$this->user_ID} and `associated_id`={$params['product_id']} and `download_publish`='{$params['download_publish']}'";
        $wpdb->query($wpdb->prepare($query));

        //sc_variation_values_associations削除
        $query = "delete from `{$values_associations_table}` where `store_id`={$this->user_ID} and `product_id`={$params['product_id']} and `download_publish`='{$params['download_publish']}'";
        $wpdb->query($wpdb->prepare($query));

        //sc_variation_priceandstock削除
        $query = "delete from `{$priceandstock_table}` where `store_id`={$this->user_ID} and `product_id`={$params['product_id']} and `download_publish`='{$params['download_publish']}'";
        $wpdb->query($wpdb->prepare($query));

        //sc_variation_combinations削除
        $query = "delete from `{$combinations_table}` where `store_id`={$this->user_ID} and `product_id`={$params['product_id']} and `download_publish`='{$params['download_publish']}'";
        $wpdb->query($wpdb->prepare($query));

        //sc_tmp_product_variation削除
        $query = "delete from `{$tmp_product_table}` where `store_id`={$this->user_ID} and `product_id`={$params['product_id']} and `download_publish`='{$params['download_publish']}'";
        $wpdb->query($wpdb->prepare($query));

        if (!is_array($params['product_variations'])) {
            return;
        }

        $values_ids = $params['values_ids'];
        $values_visible = $params['values_visible'];
        $values_stock = $params['values_stock'];
        $values_price = $params['values_price'];
        $i = 0;
        foreach ($params['product_variations'] as $variation) {
            //sc_variation_associations作成
            $data = array();
            $data['store_id'] = $this->user_ID;
            $data['associated_id'] = $params['product_id'];
            $data['variation_id'] = $variation;
            $data['download_publish'] = $params['download_publish'];
            $wpdb->insert($associations_table, $data);

            //規格詳細の集約
            $values = array();
            foreach ($values_ids as $value) {
                //規格詳細IDを「_」で区切る。
                $value = explode('_', $value);
                $values[$value[$i]] = $value[$i];
            }
            $i++;

            //sc_variation_values_associations作成
            foreach ($values as $key=>$value) {
                $data = array();
                $data['store_id'] = $this->user_ID;
                $data['product_id'] = $params['product_id'];
                $data['value_id'] = $value;
                $data['variation_id'] = $variation;
                $data['download_publish'] = $params['download_publish'];
                $wpdb->insert($values_associations_table, $data);
            }
        }

        //在庫と価格を集約
        $priceandstock = array();
        foreach ($values_price as $key=>$price) {
            $priceandstock[$key]['store_id'] = $this->user_ID;
            $priceandstock[$key]['product_id'] = $params['product_id'];
            $priceandstock[$key]['price'] = $price;
            $priceandstock[$key]['visible'] = isset($values_visible[$key])?'1':'0';
            $priceandstock[$key]['download_publish'] = $params['download_publish'];
        }
        if (isset($values_stock)) {
            foreach ($values_stock as $key=>$stock) {
                $priceandstock[$key]['stock'] = $stock;
            }
        }

        //sc_variation_priceandstock作成
        $pricestock_ids = array();
        foreach ($priceandstock as $key=>$ps_data) {
            $wpdb->insert($priceandstock_table, $ps_data);
            $pricestock_ids[$key] = $wpdb->insert_id;
        }

        $i = 0;
        foreach ($params['product_variations'] as $variation) {
            //在庫価格情報の再構成
            $priceandstocks = array();
            foreach ($pricestock_ids as $key=>$ids) {
                //「_」で区切る。
                $keys = explode('_', $key);
                $priceandstocks[$key]['value_id'] = $keys[$i];
                $priceandstocks[$key]['id'] = $ids;
            }
            $i++;

            //sc_variation_combinations作成
            foreach ($priceandstocks as $key=>$priceandstock) {
                $data = array();
                $data['store_id']      = $this->user_ID;
                $data['product_id']    = $params['product_id'];
                $data['pricestock_id'] = $priceandstock['id'];
                $data['value_id']      = $priceandstock['value_id'];
                $data['variation_id']  = $variation;
                $data['all_variation_id'] = $key;
                $data['download_publish'] = $params['download_publish'];
                $wpdb->insert($combinations_table, $data);
            }
        }

        $query = "select min(stock) as min_stock, max(stock) as max_stock, min(price) as min_price, max(price) as max_price from {$priceandstock_table} where `product_id`={$params['product_id']}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $data = array();
            $data['store_id']   = $this->user_ID;
            $data['product_id'] = $params['product_id'];
            $data['min_stock']  = $results[0]['min_stock'];
            $data['max_stock']  = $results[0]['max_stock'];
            $data['min_price']  = $results[0]['min_price'];
            $data['max_price']  = $results[0]['max_price'];
            $data['download_publish'] = $params['download_publish'];
            $wpdb->insert($tmp_product_table, $data);
        }
    }
}
