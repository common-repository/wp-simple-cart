<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Favorite Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartUserFavorite.php 141681 2009-08-01 08:33:00Z tajima $
 */

class SimpleCartUserFavorite extends SimpleCart {

    /**
     * 一般会員：お気に入り
     *
     */
    function execute() {
        if (is_null($this->user_ID) || $this->user_ID==0) {
            $this->roleError();
            return;
        }

        $pager_id = $this->request->getParam('pager_id');
        $params['pager_id'] = 1;
        if (!is_null($pager_id) && $pager_id!='') {
            $params['pager_id'] = $pager_id;
        }

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //お気に入りから削除
            case 'delete_favorite':
                $favorite = array();
                $favorite['favorite_id'] = $this->request->getParam('favorite_id');
                SimpleCartUserFavoriteModel::deleteFavorite($favorite);
                $r = array(
                    'bp-slug-main' => $bp->simplecart_user->slug,
                    'bp-slug-sub'  => SC_BPID_FAVORITE,
                    'mu-slug-main' => SCLNG_PAGE_USER_INFO,
                    'mu-slug-sub'  => SCLNG_PAGE_USER_FAVORITE
                );
                $this->redirect($r);
                break;
            //
            default:
                $this->model['favorite_count'] = SimpleCartUserFavoriteModel::getFavoriteList($params, true);
                $this->model['favorite_list']  = SimpleCartUserFavoriteModel::getFavoriteList($params);
                $this->exec('user/user_favorite');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_USER_FAVORITE, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::userTabMenu(SC_PGID_USER_FAVORITE);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_USER_FAVORITE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
