<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartAdminModel.php 151036 2009-09-01 06:50:07Z tajima $
 */

/******************************************************************************
 * SimpleCartModel
 * 
 * @author		Exbridge,inc.
 * @version		0.1
 * 
 *****************************************************************************/
class SimpleCartAdminModel {

    /**
     * 設定情報のチェック
     *
     */
    function checkOptions($params) {
        global $wpdb;

        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if ($params['sc_timeout']!='') {
            if (!is_numeric($params['sc_timeout'])) {
                $error->errors['sc_timeout'] = __(SCLNG_CHECK_NUMERIC_TIMEOUT, SC_DOMAIN);
            }
        }
        if ($params['sc_new']!='') {
            if (!is_numeric($params['sc_new'])) {
                $error->errors['sc_new'] = __(SCLNG_CHECK_NUMERIC_NEW, SC_DOMAIN);
            }
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 店舗会員登録／更新
     *
     */
    function saveStoreUser($params) {
        global $wpdb;

        //-----------------------------------------------------
        //入力内容のチェック処理
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        //-----------------------------------------------------
        $error = new WP_Error();
        if (is_null($params['id'])||$params['id']=='') {
            if (is_null($params['user_login']) || $params['user_login']=='') {
                $error->errors['user_login'] = __(SCLNG_CHECK_REQUIRED_STORE_LOGIN_ID, SC_DOMAIN);
            }
            else if (!validate_username($params['user_login'])) {
                $error->errors['user_login'] = __(SCLNG_CHECK_INVALID_STORE_LOGIN_ID, SC_DOMAIN);
            }
            else if (username_exists($params['user_login'])) {
                $error->errors['user_login'] = __(SCLNG_CHECK_EXISTS_STORE_LOGIN_ID, SC_DOMAIN);
            }
            if (is_null($params['pass1']) || $params['pass1']=='') {
                $error->errors['pass'] = __(SCLNG_CHECK_REQUIRED_STORE_LOGIN_PASSWORD, SC_DOMAIN);
            }
            //強制投入
            $_POST['pass2'] = $params['pass1'];
        }
        if (is_null($params['email']) || $params['email']=='') {
            $error->errors['user_email'] = __(SCLNG_CHECK_REQUIRED_STORE_EMAIL, SC_DOMAIN);
        }
        else if (!is_email($params['email'])) {
            $error->errors['user_email'] = __(SCLNG_CHECK_INVALID_STORE_EMAIL, SC_DOMAIN);
        }
        else if (empty($params['id']) && email_exists($params['email'])) {
            $error->errors['user_email'] = __(SCLNG_CHECK_EXISTS_STORE_EMAIL, SC_DOMAIN);
        }
        //エラーがあれば帰る
        if (count($error->errors)>0) {
            return $error;
        }

        $results = edit_user($params['id']);
        if (is_object($results)) {
            $results->errors['fatal'] = __(SCLNG_FATAL_ERROR, SC_DOMAIN);
        }
        else {
            //店舗会員の属性登録
            $id = $results;
            $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';
            $address = $params['address'];
            $street  = $params['street'];
            $state   = $params['state'];
            $zip     = $params['zip'];
            $tel     = $params['tel'];
            $fax     = $params['fax'];
            if (is_null($params['id'])||$params['id']=='') {
                $wpdb->insert($store_table, array(
                                'user_id' => $id,
                                'address' => $address,
                                'street'  => $street,
                                'state'   => $state,
                                'zip'     => $zip,
                                'tel'     => $tel,
                                'fax'     => $fax
                       ));
            }
            else {
                $wpdb->update($store_table,
                            array(
                                'address'  => $address,
                                'street'   => $street,
                                'state'    => $state,
                                'zip'      => $zip,
                                'tel'      => $tel,
                                'fax'      => $fax),
                            array('user_id' => $id));
            }

            if (is_null($params['id'])||$params['id']=='') {
                //関連テーブルへのデータ生成
                SimpleCartAdminModel::createTax($id);
                SimpleCartAdminModel::createDeliverys($id);
                //SimpleCartAdminModel::createDeliveryTimes($id);
                SimpleCartAdminModel::createPaidMethods($id);

                //ちと別件で訳あって。。。
                $user = new WP_User($id);
                $user->set_role($params['role']);
                $user->add_cap(SC_CAP);//独自役割
            }

            $wpdb->update($wpdb->users, array('user_status'  => SC_USER_ENABLE), array('ID' => $id));
        }
        return $results;
    }

    /**
     * ユーザーを店舗会員へ
     *
     */
    function addExistsUserCap($user_login=0) {
        global $wpdb;
        if ($user_login===0||$user_login=='') {
           return 1;
        }
        $user_data = get_userdatabylogin($user_login);
        $user = new WP_User($user_data->ID);
        if ($user->ID!==0) {
            $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';
            $wpdb->insert($store_table, array('user_id'=>$user_data->ID));
            $wpdb->update($wpdb->users, array('user_status'=>SC_USER_ENABLE), array('ID'=>$user_data->ID));
            //関連テーブルへのデータ生成
            SimpleCartAdminModel::createTax($user_data->ID);
            SimpleCartAdminModel::createDeliverys($user_data->ID);
            //SimpleCartAdminModel::createDeliveryTimes($user_data->ID);
            SimpleCartAdminModel::createPaidMethods($user_data->ID);
            $user->add_cap(SC_CAP);
            return 0;
        }
        return 2;
    }

    /**
     * 店舗会員削除
     *
     */
    function deleteStoreUser($user_id=0) {
        global $wpdb;
        $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';
        $query = "delete from {$store_table} where user_id={$user_id}";
        $wpdb->query($wpdb->prepare($query));
        SimpleCartAdminModel::deleteTax($user_id);
        SimpleCartAdminModel::deleteDeliverys($user_id);
        SimpleCartAdminModel::deleteDeliveryTimes($user_id);
        SimpleCartAdminModel::deletePaidMethods($user_id);
        //独自役割を削除する
        //wpmu_delete_user($user_id);
         $user = new WP_User($user_id);
         $user->remove_cap(SC_CAP);
    }

    /**
     * 店舗会員停止
     *
     */
    function stopStoreUser($user_id=0) {
        global $wpdb;
        $wpdb->update($wpdb->users, array('user_status'=>SC_USER_DISABLE), array('ID'=>$user_id));
    }

    /**
     * 店舗会員復活
     *
     */
    function revivalStoreUser($user_id=0) {
        global $wpdb;
        $wpdb->update($wpdb->users, array('user_status'=>SC_USER_ENABLE), array('ID'=>$user_id));
    }

    /**
     * 郵便番号情報一覧取得
     *
     */
    function getZipList($zipcode) {
        global $wpdb;

        $zip_list = array();
        $zip_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_zipcode';

        if (preg_match('/^(\d{3})-?(\d{4})$/', $zipcode, $match_list)) {
            $zipcode = $match_list[1] . '' . $match_list[2];

            $query = "select * from `{$zip_table}` where zip={$zipcode}";
            $zip_list = $wpdb->get_results($query, ARRAY_A);
        }

        return $zip_list;
    }

    /**
     * 郵便番号情報登録
     *
     */
    function saveZip($params) {
        global $wpdb;

        //郵便番号情報の登録
        $zip_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_zipcode';

        $wpdb->insert($zip_table, array(
                        'address' => $params['address'],
                        'street'  => $params['street'],
                        'city'    => $params['city'],
                        'state'   => $params['state'],
                        'country' => $params['country'],
                        'zip'     => $params['zip']
               ));
    }

    /**
     * 郵便番号情報全削除
     *
     */
    function truncateZip() {
        global $wpdb;

        //郵便番号情報の削除
        $zip_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_zipcode';

        $query = "truncate table `{$zip_table}`";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 会員登録時の消費税デフォルトデータ生成
     *
     */
    function createTax($id) {
        global $wpdb;

        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $tax    = SC_DEFAULT_TAX;
        $method = SC_DEFAULT_TAX_METHOD;
        $wpdb->insert($tax_table, array('store_id'=>$id,'tax'=>$tax,'method'=>$method));
    }

    /**
     * 会員削除時の消費税データ削除
     *
     */
    function deleteTax($id) {
        global $wpdb;

        $tax_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
        $query = "delete from `{$tax_table}` where `store_id`={$id}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 会員登録時の配送先情報デフォルトデータ生成
     *
     */
    function createDeliverys($id) {
        global $wpdb, $sc_delivery_companys;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        $i = 0;
        foreach ($sc_delivery_companys as $val) {
            $wpdb->insert($delivery_table, array('store_id'=>$id,'name'=>$val,'sort'=>$i));
            SimpleCartAdminModel::createDeliveryValues($id, $wpdb->insert_id);
            $i++;
        }
    }

    /**
     * 会員削除時の配送先情報データ削除
     *
     */
    function deleteDeliverys($id) {
        global $wpdb;

        $delivery_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
        $query = "delete from `{$delivery_table}` where `store_id`={$id}";
        $wpdb->query($wpdb->prepare($query));

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        $query = "delete from `{$delivery_values_table}` where `store_id`={$id}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 会員登録時の配送地域情報デフォルトデータ生成
     *
     */
    function createDeliveryValues($id, $delivery_id) {
        global $wpdb, $sc_states;

        $delivery_values_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
        $i = 0;
        foreach ($sc_states as $val) {
            $wpdb->insert($delivery_values_table, array(
                            'store_id'     => $id,
                            'delivery_id'  => $delivery_id,
                            'name'         => $val,
                            'delivery_fee' => 0,
                            'sort'         => $i
                   ));
            $i++;
        }
    }

    /**
     * 会員登録時の配送時間情報デフォルトデータ生成（不要となった）
     *
     */
    function createDeliveryTimes($id) {
        global $wpdb, $sc_delivery_times;

        $delivery_time_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_time';
        $i = 0;
        foreach ($sc_delivery_times as $val) {
            $wpdb->insert($delivery_time_table, array('store_id'=>$id,'name'=>$val,'sort'=>$i));
            $i++;
        }
    }

    /**
     * 会員削除時の配送時間情報データ削除（不要となった）
     *
     */
    function deleteDeliveryTimes($id) {
        global $wpdb;

        $delivery_time_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_time';
        $query = "delete from `{$delivery_time_table}` where `store_id`={$id}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 会員登録時の支払方法デフォルトデータ生成
     *
     */
    function createPaidMethods($id) {
        global $wpdb, $sc_paid_methods;

        $paid_method_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        $i = 0;
        foreach ($sc_paid_methods as $val) {
            $wpdb->insert($paid_method_table, array('store_id'=>$id,'name'=>$val,'sort'=>$i));
            $i++;
        }
    }

    /**
     * 会員削除時の支払方法情報データ削除
     *
     */
    function deletePaidMethods($id) {
        global $wpdb;

        $paid_method_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
        $query = "delete from `{$paid_method_table}` where `store_id`={$id}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 店舗情報取得
     *
     */
    function getStoreUser($user_id=0) {
        global $wpdb;

        $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';

        $user_clm  = 'a.ID, a.user_login, a.user_email as email, a.user_url as url, a.user_registered, a.display_name ';
        $store_clm = 'b.pobox, b.address, b.street, b.city, b.state, b.zip, b.country, b.tel, b.fax, b.mobile ';

        $query = "select {$user_clm}, {$store_clm} from `{$wpdb->users}` as a, `{$store_table}` as b where a.ID={$user_id} and a.ID=b.user_id ";
        $results = $wpdb->get_results($query, ARRAY_A);

        //ロール取得
        $u = new WP_User($results[0]['ID']);
        $results[0]['role'] = $u->roles[0];
        return $results[0];
    }

    /**
     * 店舗一覧取得
     *
     */
    function getStoreList() {
        global $wpdb, $wp_roles;

        $query = "select * from `{$wpdb->users}` order by user_status";
        $results = $wpdb->get_results($query, ARRAY_A);
        $user_list = array();
        foreach ($results as $user) {
            $u = new WP_User($user['ID']);
            if ($u->has_cap(SC_CAP)) {
                $user['role'] = $u->roles[0];
                $user_list[] = $user;
            }
        }
        return $user_list;
    }

    /**
     * 権限情報取得
     *
     */
    function getRole() {
        global $wpdb;
        return get_blog_option($wpdb->blogid, "{$wpdb->base_prefix}{$wpdb->blogid}_user_roles");
    }

    /**
     * 店舗別売上（受注）情報取得
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

        $query = "
            select 
                a.`store_id`,
                b.`display_name` as store_name,
                sum(case when a.`regist_date`>='{$params['yymm00']}' then (a.`total`-a.`commission`-a.`deliver_fee`) else 0 end) as yymm00, 
                sum(case when a.`regist_date`>='{$params['yymm01']}' and a.`regist_date`<'{$params['yymm00']}' then (a.`total`-a.`commission`-a.`deliver_fee`) else 0 end) as yymm01, 
                sum(case when a.`regist_date`>='{$params['yymm02']}' and a.`regist_date`<'{$params['yymm01']}' then (a.`total`-a.`commission`-a.`deliver_fee`) else 0 end) as yymm02, 
                sum(case when a.`regist_date`>='{$params['yymm03']}' and a.`regist_date`<'{$params['yymm02']}' then (a.`total`-a.`commission`-a.`deliver_fee`) else 0 end) as yymm03, 
                sum(a.`total`-a.`commission`-a.`deliver_fee`) as total 
            from `{$order_table}` as a
                 left outer join {$wpdb->users} as b on (a.`store_id`=b.`ID`)
            where a.`regist_date`>='{$params['ymd']}'
            and   a.`status` in ({$status0}, {$status1}, {$status2}, {$status3}, {$status4})
            group by
                a.`store_id`,
                b.`display_name`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results;
        }
        else {
            return false;
        }
    }

    /**
     * 店舗受注一覧取得
     *
     */
    function getOrderList($params, $count=false) {
        global $wpdb;

        $order_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';

        $cond = array();
        $cond[] = "store_id = " . $params['s_store_id'];
        if (isset($params['s_order_no']) && $params['s_order_no']!='') {
            $cond[] = "a.`id` like '%" . $params['s_order_no'] . "%'";
        }
        if (isset($params['s_order_status']) && $params['s_order_status']!='') {
            $cond[] = "a.`status` like '%" . $params['s_order_status'] . "%'";
        }
        if (isset($params['s_order_login']) && $params['s_order_login']!='') {
            $cond[] = "b.`user_login` like '%" . $params['s_order_login'] . "%'";
        }
        if (isset($params['s_order_user']) && $params['s_order_user']!='') {
            $cond[] = "b.`display_name` like '%" . $params['s_order_user'] . "%'";
        }
        if ((isset($params['s_order_fyy']) && $params['s_order_fyy']!='')
            && (isset($params['s_order_fmm']) && $params['s_order_fmm']!='')
            && (isset($params['s_order_fdd']) && $params['s_order_fdd']!='')) {
            $cond[] = "a.`regist_date` like '%" . $params['s_order_fyy'] . '-' . SimpleCartFunctions::LPAD($params['s_order_fmm'], 2) . '-' . SimpleCartFunctions::LPAD($params['s_order_fdd'], 2) . "%'";
        }
        if ((isset($params['s_order_fyy']) && $params['s_order_fyy']!='')
            && (isset($params['s_order_fmm']) && $params['s_order_fmm']!='')
            && (!isset($params['s_order_fdd']) || $params['s_order_fdd']=='')) {
            $cond[] = "a.`regist_date` like '%" . $params['s_order_fyy'] . '-' . SimpleCartFunctions::LPAD($params['s_order_fmm'], 2) . "%'";
        }
        $where = '';
        if (count($cond) > 0) {
            $where = 'where ' . @implode(' and ', $cond);
        }

        if ($count==false) {
            $page = '';
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $page = " limit " . $offset . ", " . $limit;
            }
            $query = "
                select 
                    a.*,
                    b.user_login,
                    b.display_name as user_name
                from `{$order_table}` as a
                     left outer join `{$wpdb->users}` as b on (a.`user_id`=b.`ID`)
                " . $where . "
                order by
                    a.`regist_date` desc " . $page;
        }
        else {
            $query = "
                select count(a.`id`) as count
                from `{$order_table}` as a
                     left outer join `{$wpdb->users}` as b on (a.`user_id`=b.`ID`)
                " . $where;
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
            if ($count==false) {
                return false;
            }
            else {
                return 0;
            }
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

        //オーダーの頭情報取得
        $query = "
                select a.*, b.`display_name` as store_name, c.`display_name` as user_name 
                from `{$order_table}` as a
                     left outer join `{$wpdb->users}` as b on (a.`store_id`=b.`ID`)
                     left outer join `{$wpdb->users}` as c on (a.`user_id`=c.`ID`)
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
     * メッセージ一覧取得
     *
     */
    function getMessageThreadList($params) {
        global $wpdb;

        $message_thread_list = array();
        $message_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_message';

        $query = "select * from `{$message_table}` where order_id={$params['order_id']}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            foreach ($results as $r) {
                $message_params = array();
                $message_params['thread_id'] = $r['message_id'];
                $message_thread_list[]  = sc_get_message_thread($message_params);
            }
        }
        return $message_thread_list;
    }
}
