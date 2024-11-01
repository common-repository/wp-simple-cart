<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreUserModel.php 151036 2009-09-01 06:50:07Z tajima $
 */

class SimpleCartStoreUserModel extends SimpleCartCommonModel {

    /**
     * 店舗情報取得
     *
     */
    function getStore() {
        global $wpdb, $current_user;

        $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';

        $query = "select a.ID, a.user_login, a.user_email as email, a.user_url as url, a.user_registered, a.display_name, b.address, b.street, b.state, b.zip, b.tel, b.fax from {$wpdb->users} as a, {$store_table} as b where a.`user_status`=" . SC_USER_ENABLE . " and a.ID={$current_user->ID} and a.`ID`=b.`user_id`";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * 店舗情報更新前チェック
     *
     */
    function checkStore($params) {
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        $error = new WP_Error();
        if (is_null($params['display_name']) || $params['display_name']=='') {
            $error->errors['user_name'] = __(SCLNG_CHECK_REQUIRED_STORE_NAME, SC_DOMAIN);
        }
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * 店舗情報更新
     *
     */
    function saveStore($params) {
        global $wpdb;

        require_once(ABSPATH . '/wp-admin/includes/user.php');
        require_once(ABSPATH . '/wp-includes/registration.php');
        $results = edit_user($params['ID']);

        if (is_object($results)) {
            $results->errors['fatal'] = __(SCLNG_FATAL_ERROR, SC_DOMAIN);
        }
        else {
            $store_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';
            $id      = $params['ID'];
            $address = $params['address'];
            $street  = $params['street'];
            $state   = $params['state'];
            $zip     = $params['zip'];
            $tel     = $params['tel'];
            $fax     = $params['fax'];
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
        return $results;
    }

    /**
     * パスワード変更前チェック
     *
     */
    function checkPassword($params) {
        global $wpdb;

        //-----------------------------------------------------
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        //-----------------------------------------------------
        $error = new WP_Error();
        if (is_null($params['pass1']) || $params['pass1']=='') {
            $error->errors['pass1'] = __(SCLNG_CHECK_REQUIRED_USER_PASSWORD, SC_DOMAIN);
        }
        if (is_null($params['pass2']) || $params['pass2']=='') {
            $error->errors['pass2'] = __(SCLNG_CHECK_REQUIRED_USER_PASSWORD, SC_DOMAIN);
        }
        else if ($params['pass1'] != $params['pass2']) {
            $error->errors['pass2'] = __(SCLNG_CHECK_INVALID_USER_PASSWORD, SC_DOMAIN);
        }
        //エラーがあれば帰る
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * パスワード変更
     *
     */
    function savePassword($params) {
        global $wpdb;

        require_once(ABSPATH . '/wp-includes/pluggable.php');
        $wpdb->update($wpdb->users, array('user_pass'=>wp_hash_password($params['pass1'])), array('ID'=>$this->user_ID));
    }
}
