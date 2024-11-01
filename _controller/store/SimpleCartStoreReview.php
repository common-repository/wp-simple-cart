<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Review Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreReview.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreReview extends SimpleCart {

    /**
     * 店舗会員：レビュー管理
     *
     */
    function execute() {

        //roleの確認
        $u = new WP_User($this->user_ID);
        if (!$u->has_cap(SC_CAP)) {
            if ($this->mode!=SC_MODE_BP) {
                $this->roleError();
            }
            return;
        }

        global $bp;

        $params = array();
        $params['s_review_recommend'] = $this->request->getParam('s_review_recommend');
        $params['s_review_comment']   = $this->request->getParam('s_review_comment');
        $params['s_review_user']      = $this->request->getParam('s_review_user');
        $params['s_product_cd']       = $this->request->getParam('s_product_cd');
        $params['s_categorys']        = $this->request->getParam('s_categorys');

        $pager_id = $this->request->getParam('pager_id');
        $params['pager_id'] = 1;
        if (!is_null($pager_id) && $pager_id!='') {
            $params['pager_id'] = $pager_id;
        }

        $this->model['categorys'] = SimpleCartStoreReviewModel::getCategorys();
        $this->model['review_search'] = $params;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //
            default:
            //ユーザーレビュー検索
            case 'search_review':
                break;
            //ユーザーレビュー削除
            case 'delete_review':
                $review = array();
                $review['review_id'] = $this->request->getParam('review_id');
                SimpleCartStoreReviewModel::deleteReview($review);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_store->slug,
                    'bp-slug-sub'  => SC_BPID_REVIEW,
                    'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_STORE_REVIEW
                );
                $this->redirect($r);
                break;
            //
            default:
                break;
        }

        $this->model['recommend'] = SimpleCartStoreReviewModel::getRecommend();
        $this->model['review_count'] = SimpleCartStoreReviewModel::getReviewList($params, true);
        $this->model['review_list'] = SimpleCartStoreReviewModel::getReviewList($params);
        $this->exec('store/review');
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_REVIEW_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_REVIEW);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_REVIEW_INFO_LIST, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
