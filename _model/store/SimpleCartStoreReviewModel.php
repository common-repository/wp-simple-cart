<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Myprofile Store Review Model Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreReviewModel.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreReviewModel extends SimpleCartCommonModel {

    /**
     * ユーザーレビュー一覧取得
     *
     */
    function getReviewList($params, $count=false) {
        global $wpdb;

        $product_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
        $review_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';

        $cond = array();
        $cond[] = "b.`store_id` = " . $this->user_ID;
        $cond[] = "b.`product_id` = a.`id`";
        $cond[] = "b.`user_id` = c.`ID`";
        if (isset($params['s_product_cd']) && $params['s_product_cd']!='') {
            $cond[] = "a.`product_cd` like '%".$params['s_product_cd']."%'";
        }
        if (isset($params['s_review_recommend']) && $params['s_review_recommend']!='') {
            $cond[] = "b.`recommend` = " . $params['s_review_recommend'];
        }
        if (isset($params['s_review_comment']) && $params['s_review_comment']!='') {
            $cond[] = "b.`comment` like '%".$params['s_review_comment']."%'";
        }
        if (isset($params['s_review_user']) && $params['s_review_user']!='') {
            $user = array();
            $user[] = "c.`user_login` like '%".$params['s_review_user']."%'";
            $user[] = "c.`display_name` like '%".$params['s_review_user']."%'";
            $cond[] = "(" . @implode(" or ", $user) . ")";
        }
        if (isset($params['s_categorys'])) {
            $cates = array();
            foreach ($params['s_categorys'] as $category) {
                $cates[] = "a.`categorys` like '%i:" . $category . ";%'";
            }
            $cond[] = '(' . @implode(" or ", $cates) . ')';
        }

        $where = '';
        if (count($cond) > 0) {
            $where = 'where ' . @implode(' and ', $cond);
        }

        if ($count==false) {
            if (isset($params['pager_id'])) {
                $offset = SC_PAGE_COUNT * ($params['pager_id']-1);
                $limit  = SC_PAGE_COUNT;
                $where .= " order by b.`regist_date` desc, a.`product_cd`";
                $where .= " limit " . $offset . ", " . $limit;
            }
            $query = "select b.*, a.id as product_id, a.product_cd, a.name as product_name, c.display_name, c.user_login from `{$product_table}` as a, `{$review_table}` as b, `{$wpdb->users}` as c " . $where;
        }
        else {
            $query = "select count(b.`id`) as count from `{$product_table}` as a, `{$review_table}` as b, `{$wpdb->users}` as c " . $where;
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        if (is_array($results)) {
            if ($count==false) {
                $res = array();
                foreach ($results as $r) {
                    $r['categorys'] = unserialize($r['categorys']);
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
     * ユーザーレビュー情報削除
     *
     */
    function deleteReview($params) {
        global $wpdb;

        $review_table = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';

        //ユーザーレビュー削除
        $query = "delete from `{$review_table}` where `id`={$params['review_id']}";
        $wpdb->query($wpdb->prepare($query));
    }

    /**
     * 評価一覧取得
     *
     */
    function getRecommend() {
        global $sc_recommend;
        return $sc_recommend;
    }
}
