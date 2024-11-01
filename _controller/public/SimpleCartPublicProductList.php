<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Product List Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartPublicProductList.php 147731 2009-08-21 05:28:05Z tajima $
 */

class SimpleCartPublicProductList extends SimpleCart {

    /**
     * 全体公開：商品一覧
     *
     */
    function execute() {
        $params = array();
        $params['s_product_search']   = $this->request->getParam('s_product_search');
        $params['s_categorys']        = $this->request->getParam('s_categorys');
        $params['s_store']            = $this->request->getParam('s_store');
        $params['s_text']             = $this->request->getParam('s_text');
        $params['s_text_description'] = $this->request->getParam('s_text_description');
        $params['s_sort']             = $this->request->getParam('s_sort');
        if (is_null($params['s_sort'])||$params['s_sort']=='') {
            $params['s_sort'] = 1;
        }

        $params['pager_id'] = 1;
        $pager_id = $this->request->getParam('pager_id');
        if (!is_null($pager_id) && $pager_id!='') {
            $params['pager_id'] = $pager_id;
        }

        if (isset($params['s_product_search']) && !is_null($params['s_product_search']) && $params['s_product_search']!='') {
            if ($params['s_product_search']==1) {
                $this->model['product_list'] = sc_get_new_item(10);
                $this->model['product_count'] = count($this->model['product_list']);
            }
            else if ($params['s_product_search']==2) {
                $this->model['product_list'] = sc_get_best_item(10, 0, 'kin');
                $this->model['product_count'] = count($this->model['product_list']);
            }
            else if ($params['s_product_search']==3) {
                $this->model['product_list'] = sc_get_best_item(10, 0, 'su');
                $this->model['product_count'] = count($this->model['product_list']);
            }
        }
        else {
            $params['s_product_search'] = null;
            $this->model['product_count'] = SimpleCartPublicModel::getProductList($params, true);
            $this->model['product_list']  = SimpleCartPublicModel::getProductList($params);
        }

        $this->model['product_cond'] = $params;
        $this->hidden_vars = $params;
        $this->exec('public/product_list');
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_PUBLIC_PRODUCT_LIST, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
