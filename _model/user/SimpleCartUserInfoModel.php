<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile User Info Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserInfoModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartUserInfoModel extends SimpleCartCommonModel {

    /**
     * ユーザー情報取得
     *
     */
    function getUser() {
        global $wpdb;

        $user_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user';
        $query = "select a.user_login, a.user_email, a.display_name, b.* 
                  from {$wpdb->users} as a
                       left outer join {$user_table} as b on (a.`ID`=b.`user_id`)
                  where a.`ID`={$this->user_ID}";
        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            return $results[0];
        }
        else {
            return false;
        }
    }

    /**
     * ユーザー情報更新前チェック
     *
     */
    function checkUser($params) {
        global $wpdb;

        //-----------------------------------------------------
        //本来の正しい使い方してないけど、まあメンドイからこれで。。。
        //-----------------------------------------------------
        $error = new WP_Error();
        if (is_null($params['display_name']) || $params['display_name']=='') {
             $error->errors['display_name'] = __(SCLNG_CHECK_REQUIRED_USER_DISPLAY_NAME, SC_DOMAIN);
        }
        //エラーがあれば帰る
        if (count($error->errors)>0) {
            return $error;
        }
        return true;
    }

    /**
     * ユーザー情報登録／更新
     *
     */
    function saveUser($params) {
        global $wpdb;

        require_once(ABSPATH . '/wp-includes/pluggable.php');
        $wpdb->update($wpdb->users, array('display_name'=>$params['display_name']), array('ID'=>$this->user_ID));

        //sc_user作成
        $user_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user';
        if (is_null($params['id'])||$params['id']=='') {
            $wpdb->insert($user_table,
                    array(
                    'user_id'         => $this->user_ID,
                    'send_first_name' => $params['send_first_name'],
                    'send_last_name'  => $params['send_last_name'],
                    'send_first_furi' => $params['send_first_furi'],
                    'send_last_furi'  => $params['send_last_furi'],
                    'send_address'    => $params['send_address'],
                    'send_street'     => $params['send_street'],
                    'send_state'      => $params['send_state'],
                    'send_zip'        => $params['send_zip'],
                    'send_tel'        => $params['send_tel'],
                    'send_fax'        => $params['send_fax'],
                    'send_mobile'     => $params['send_mobile'],
                    'bill_first_name' => $params['bill_first_name'],
                    'bill_last_name'  => $params['bill_last_name'],
                    'bill_first_furi' => $params['bill_first_furi'],
                    'bill_last_furi'  => $params['bill_last_furi'],
                    'bill_address'    => $params['bill_address'],
                    'bill_street'     => $params['bill_street'],
                    'bill_state'      => $params['bill_state'],
                    'bill_zip'        => $params['bill_zip'],
                    'bill_tel'        => $params['bill_tel'],
                    'bill_fax'        => $params['bill_fax'],
                    'bill_mobile'     => $params['bill_mobile'],
                    ));
        }
        else {
            $wpdb->update($user_table,
                    array(
                    'user_id'         => $this->user_ID,
                    'send_first_name' => $params['send_first_name'],
                    'send_last_name'  => $params['send_last_name'],
                    'send_first_furi' => $params['send_first_furi'],
                    'send_last_furi'  => $params['send_last_furi'],
                    'send_address'    => $params['send_address'],
                    'send_street'     => $params['send_street'],
                    'send_state'      => $params['send_state'],
                    'send_zip'        => $params['send_zip'],
                    'send_tel'        => $params['send_tel'],
                    'send_fax'        => $params['send_fax'],
                    'send_mobile'     => $params['send_mobile'],
                    'bill_first_name' => $params['bill_first_name'],
                    'bill_last_name'  => $params['bill_last_name'],
                    'bill_first_furi' => $params['bill_first_furi'],
                    'bill_last_furi'  => $params['bill_last_furi'],
                    'bill_address'    => $params['bill_address'],
                    'bill_street'     => $params['bill_street'],
                    'bill_state'      => $params['bill_state'],
                    'bill_zip'        => $params['bill_zip'],
                    'bill_tel'        => $params['bill_tel'],
                    'bill_fax'        => $params['bill_fax'],
                    'bill_mobile'     => $params['bill_mobile'],
                    ),
                    array('id'=>$params['id']));
        }
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
     * パスワード更新
     *
     */
    function savePassword($params) {
        global $wpdb;

        require_once(ABSPATH . '/wp-includes/pluggable.php');
        $wpdb->update($wpdb->users, array('user_pass'=>wp_hash_password($params['pass1'])), array('ID'=>$this->user_ID));
    }
}
