<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Product Register Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreProductModel.php 145068 2009-08-12 07:49:38Z tajima $
 */

class SimpleCartStoreProductModel extends SimpleCartCommonModel {

    /**
     * 製品一覧取得
     *
     */
    function getProductList($params=null, $count=false) {
        global $wpdb;

        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $tmp_product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';

        $cond = array();
        $cond[] = "a.`store_id` = " . $this->user_ID;
        if (isset($params['s_product_cd']) && $params['s_product_cd']!='') {
            $cond[] = "a.`product_cd` like '%".$params['s_product_cd']."%'";
        }
        if (isset($params['s_product_name']) && $params['s_product_name']!='') {
            $cond[] = "a.`name` like '%".$params['s_product_name']."%'";
        }
        if (isset($params['s_publish']) && $params['s_publish']!='') {
            $cond[] = "a.`publish` = ".$params['s_publish'];
        }
        if (isset($params['s_stock']) && $params['s_stock']!='') {
            $cond[] = "a.`stock_manage` = ".$params['s_stock'];
        }
        if (isset($params['s_categorys'])) {
            $cates = array();
            if (is_array($params['s_categorys'])) {
                foreach ($params['s_categorys'] as $category) {
                    $cates[] = "a.`categorys` like '%i:" . $category . ";%'";
                }
            }
            if (count($cates)>0) {
                $cond[] = '(' . @implode(" or ", $cates) . ')';
            }
        }

        $where = '';
        if (count($cond) > 0) {
            $where = @implode(' and ', $cond);
        }

        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $page .= " limit " . $offset . ", " . $limit;
            }
            $query = "
                select 
                    a.*, 
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
                from (select * from `{$product_table}` as a where {$where}) as a 
                     left outer join `{$tmp_product_table}` as b on (a.`id`=b.`product_id` and b.`download_publish`='0') 
                     left outer join `{$tmp_product_table}` as c on (a.`id`=c.`product_id` and c.`download_publish`='1') 
                " . $page;
        }
        else {
            $query = "select count(a.`id`) as count from `{$product_table}` as a " . $where;
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($count==false) {
                $res = array();
                foreach ($results as $r) {
                    $r['categorys'] = unserialize($r['categorys']);
                    SimpleCartFunctions::CalcPrices($r);
                    $res[$r['id']] = $r;
                }
                return $res;
            }
            else {
                return $results[0]['count'];
            }
        }
        else {
            if ($count==false) {
                return false;
            }
            else {
                return 0;
            }
        }
    }

    /**
     * 製品情報取得
     *
     */
    function getProduct($id) {
        global $wpdb;

        if (is_null($id) || $id=='') {
            return false;
        }
        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $query = "select * from {$product_table} where `id`={$id}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            $results[0]['categorys'] = unserialize($results[0]['categorys']);
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 製品情報更新前チェック
     *
     */
    function checkProduct($params) {
        global $wpdb;

        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['product_cd']) || $params['product_cd']=='') {
            $error->errors['product_cd'] = __(SCLNG_CHECK_REQUIRED_PRODUCT_CODE, SC_DOMAIN);
        }
        else {
            //製品コードは店舗で一意になるようにチェック。
            $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
            $query = "select * from `{$product_table}` where store_id={$this->user_ID} and id<>{$params['id']} and product_cd='{$params['product_cd']}'";
            $results = $wpdb->get_results($query, ARRAY_A);
            if (is_array($results)) {
                $error->errors['product_cd'] = __(SCLNG_CHECK_EXISTS_PRODUCT_CODE, SC_DOMAIN);
            }
        }
        if (is_null($params['name']) || $params['name']=='') {
            $error->errors['product_name'] = __(SCLNG_CHECK_REQUIRED_PRODUCT_NAME, SC_DOMAIN);
        }
        if (is_null($params['description']) || $params['description']=='') {
            $error->errors['description'] = __(SCLNG_CHECK_REQUIRED_DESCRIPTION, SC_DOMAIN);
        }
        if (is_null($params['add_description']) || $params['add_description']=='') {
            $error->errors['add_description'] = __(SCLNG_CHECK_REQUIRED_ADDD_ESCRIPTION, SC_DOMAIN);
        }
        if (is_null($params['price']) || $params['price']=='') {
            $error->errors['price'] = __(SCLNG_CHECK_REQUIRED_PRICE, SC_DOMAIN);
        }
        else if (!is_numeric($params['price'])) {
            $error->errors['price'] = __(SCLNG_CHECK_NUMERIC_PRICE, SC_DOMAIN);
        }
        if (($params['off']!='') && !is_numeric($params['off'])) {
            $error->errors['off'] = __(SCLNG_CHECK_NUMERIC_OFF, SC_DOMAIN);
        }
        if (($params['quantity_limit']!='') && !is_numeric($params['quantity_limit'])) {
            $error->errors['quantity_limit'] = __(SCLNG_CHECK_NUMERIC_QUANTITY_LIMIT, SC_DOMAIN);
        }
        if (count($params['categorys'])==0) {
            $error->errors['categorys'] = __(SCLNG_CHECK_REQUIRED_CATEGORY, SC_DOMAIN);
        }
        if ($params['stock_manage']=='1' && ($params['stock']==''||is_null($params['stock']))) {
            $error->errors['stock'] = __(SCLNG_CHECK_REQUIRED_STOCK, SC_DOMAIN);
        }
        else if ($params['stock_manage']=='1' && !is_numeric($params['stock'])) {
            $error->errors['stock'] = __(SCLNG_CHECK_NUMERIC_STOCK, SC_DOMAIN);
        }
        if ($params['download_publish']!='0' && ($params['product_url']==''||$params['product_url']=='##deleted##')) {
            $error->errors['download'] = __(SCLNG_CHECK_REQUIRED_DOWNLOAD_PRODUCT, SC_DOMAIN);
        }
        if ($params['download_publish']=='2' && ($params['download_price']==''||is_null($params['download_price']))) {
            $error->errors['download_price'] = __(SCLNG_CHECK_REQUIRED_DOWNLOAD_PRICE, SC_DOMAIN);
        }
        else if ($params['download_publish']=='2' && !is_numeric($params['download_price'])) {
            $error->errors['download_price'] = __(SCLNG_CHECK_NUMERIC_DOWNLOAD_PRICE, SC_DOMAIN);
        }
        if ($params['download_publish']=='2' && (($params['download_off']!='') && !is_numeric($params['download_off']))) {
            $error->errors['download_off'] = __(SCLNG_CHECK_NUMERIC_DOWNLOAD_OFF, SC_DOMAIN);
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 製品登録／更新
     *
     */
    function saveProduct($params) {
        global $wpdb, $current_user;

        require_once(WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/_component/class.Thumbnail.php');

        //sc_product
        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';

        if ($params['off']==='') {
            $params['off'] = 0;
        }

        //ID取り
        $id = null;
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($product_table,
                    array(
                    'store_id'         => $this->user_ID,
                    'product_cd'       => $params['product_cd'],
                    'name'             => $params['name'],
                    'description'      => $params['description'],
                    'add_description'  => $params['add_description'],
                    'price'            => $params['price'],
                    'off'              => $params['off'],
                    'categorys'        => serialize($params['categorys']),
                    'notax'            => $params['notax'],
                    'stock_manage'     => $params['stock_manage'],
                    'stock'            => $params['stock'],
                    'quantity_limit'   => $params['quantity_limit'],
                    'publish'          => $params['publish'],
                    'download_publish' => $params['download_publish'],
                    'download_price'   => $params['download_price'],
                    'download_off'     => $params['download_off'],
                    'regist_date'      => date('YmdHis', time()),
                    'update_date'      => date('YmdHis', time())
                    ));
            $id = $wpdb->insert_id;
        }
        else {
            $wpdb->update($product_table,
                    array(
                    'store_id'         => $this->user_ID,
                    'product_cd'       => $params['product_cd'],
                    'name'             => $params['name'],
                    'description'      => $params['description'],
                    'add_description'  => $params['add_description'],
                    'price'            => $params['price'],
                    'off'              => $params['off'],
                    'categorys'        => serialize($params['categorys']),
                    'notax'            => $params['notax'],
                    'stock_manage'     => $params['stock_manage'],
                    'stock'            => $params['stock'],
                    'quantity_limit'   => $params['quantity_limit'],
                    'publish'          => $params['publish'],
                    'download_publish' => $params['download_publish'],
                    'download_price'   => $params['download_price'],
                    'download_off'     => $params['download_off'],
                    'update_date'      => date('YmdHis', time())
                    ),
                    array('id'=>$params['id']));
            $id = $params['id'];
        }

        if (is_null($id)) {
            _e(SCLNG_FATAL_ERROR, SC_DOMAIN);
            return false;
        }

        //ダウンロードファイルをテンポラリから移動
        if (!is_null($params['product_url'])) {
            if ($params['product_url']=='##deleted##') {
                //画像情報削除
                $wpdb->update($product_table, array('product_url'=>null), array('id'=>$id));
            }
            else {
                $file = $params['product_url'];
                $file = explode('?', $file);
                $file = $file[0];
                //ファイル移動
                $temp_file = SimpleCartFunctions::TemporaryDir($params['download_prefix']) . '/' . $file;
                $down_file = SimpleCartFunctions::DownloadPoductDir($id) . '/' . $file;
                SimpleCartFunctions::Move($temp_file, $down_file);
                //画像情報更新
                $wpdb->update($product_table, array('product_url'=>$file), array('id'=>$id));
            }
        }
        //イメージファイルをテンポラリから移動
        if (!is_null($params['image_file_url1'])) {
            if ($params['image_file_url1']=='##deleted##') {
                //画像情報削除
                $wpdb->update($product_table, array('image_file_url1'=>null), array('id'=>$id));
            }
            else {
                $file = $params['image_file_url1'];
                $file = explode('?', $file);
                $file = $file[0];
                //ファイル移動
                $temp_file = SimpleCartFunctions::TemporaryDir($params['image_prefix1']) . '/' . $file;
                $image_file = SimpleCartFunctions::ProductImageDir($id, 1) . '/' . $file;
                SimpleCartFunctions::Move($temp_file, $image_file);
                $thum_obj = new Thumbnail($image_file, 96, 96);
                $thum_obj->save($image_file);
                //画像情報更新
                $wpdb->update($product_table, array('image_file_url1'=>$file), array('id'=>$id));
            }
        }
        if (!is_null($params['image_file_url2'])) {
            if ($params['image_file_url2']=='##deleted##') {
                //画像情報削除
                $wpdb->update($product_table, array('image_file_url2'=>null), array('id'=>$id));
            }
            else {
                $file = $params['image_file_url2'];
                $file = explode('?', $file);
                $file = $file[0];
                //ファイル移動
                $temp_file = SimpleCartFunctions::TemporaryDir($params['image_prefix2']) . '/' . $file;
                $image_file = SimpleCartFunctions::ProductImageDir($id, 2) . '/' . $file;
                SimpleCartFunctions::Move($temp_file, $image_file);
                $thum_obj = new Thumbnail($image_file, 192, 192);
                $thum_obj->save($image_file);
                //画像情報更新
                $wpdb->update($product_table, array('image_file_url2'=>$file), array('id'=>$id));
            }
        }
        if (!is_null($params['image_file_url3'])) {
            if ($params['image_file_url3']=='##deleted##') {
                //画像情報削除
                $wpdb->update($product_table, array('image_file_url3'=>null), array('id'=>$id));
            }
            else {
                $file = $params['image_file_url3'];
                $file = explode('?', $file);
                $file = $file[0];
                //ファイル移動
                $temp_file = SimpleCartFunctions::TemporaryDir($params['image_prefix3']) . '/' . $file;
                $image_file = SimpleCartFunctions::ProductImageDir($id, 3) . '/' . $file;
                SimpleCartFunctions::Move($temp_file, $image_file);
                $thum_obj = new Thumbnail($image_file, 384, 384);
                $thum_obj->save($image_file);
                //画像情報更新
                $wpdb->update($product_table, array('image_file_url3'=>$file), array('id'=>$id));
            }
        }

        $user_dir = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/' . $current_user->ID . '/temporary';
        SimpleCartFunctions::Rm($user_dir);

        return $id;
    }

    /**
     * 製品削除
     *
     */
    function deleteProducts($params) {
        global $wpdb;

        $product_table                = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $product_review_table         = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';
        $variation_associations_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_associations';
        $values_associations_table    = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values_associations';
        $priceandstock_table          = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
        $combinations_table           = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';
        $tmp_product_table            = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';

        if (is_array($params)) {
            foreach ($params as $id) {
                $query = "delete from `{$product_table}` where `id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$product_review_table}` where `product_id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$variation_associations_table}` where `product_id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$values_associations_table}` where `product_id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$priceandstock_table}` where `product_id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$combinations_table}` where `product_id`={$id}";
                $wpdb->query($wpdb->prepare($query));

                $query = "delete from `{$tmp_product_table}` where `product_id`={$id}";
                $wpdb->query($wpdb->prepare($query));
            }
        }
    }
}
