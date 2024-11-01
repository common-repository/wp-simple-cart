<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile User Favorite Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserFavoriteModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartUserFavoriteModel extends SimpleCartCommonModel {

    /**
     * ‚¨‹C‚É“ü‚èˆê——Žæ“¾
     *
     */
    function getFavoriteList($params, $count=false) {
        global $wpdb;

        $favorite_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user_favorite';
        $product_table  = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';

        $query = '';
        $page_cond = '';
        //w“ü—š—ðî•ñŽæ“¾‚Ìê‡
        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $page_cond = " limit " . $offset . ", " . $limit;
            }
            $query = "
                select 
                    a.`id` as favorite_id,
                    b.* 
                from `{$favorite_table}` as a 
                     left outer join `{$product_table}` as b on (a.`product_id`=b.`id`)
                where a.`user_id`={$this->user_ID}
                order by `regist_date` desc " . $page_cond;
        }
        //Œ”î•ñŽæ“¾‚Ìê‡
        else {
            $query = "select count(id) as count from `{$favorite_table}` where `user_id`={$this->user_ID}";
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
            return false;
        }
    }

    /**
     * ‚¨‹C‚É“ü‚èî•ñíœ
     *
     */
    function deleteFavorite($params) {
        global $wpdb;

        $favorite_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_user_favorite';

        //‚¨‹C‚É“ü‚èíœ
        $query = "delete from `{$favorite_table}` where `id`={$params['favorite_id']}";
        $wpdb->query($wpdb->prepare($query));
    }
}
