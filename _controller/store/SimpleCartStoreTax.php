<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Tax Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartStoreTax.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartStoreTax extends SimpleCart {

    /**
     * 店舗会員：税率管理
     *
     */
    function execute() {
        global $sc_tax_method;

        //roleの確認
        $u = new WP_User($this->user_ID);
        if (!$u->has_cap(SC_CAP)) {
            if ($this->mode!=SC_MODE_BP) {
                $this->roleError();
            }
            return;
        }

        global $bp;

        //処理の振分け
        switch ($this->request->getParam('sc_action')) {
            //税率変更
            case 'save_tax':
                $params = array();
                $params['id']       = $this->request->getParam('tax_id');
                $params['tax']      = $this->request->getParam('tax');
                $params['method']   = $this->request->getParam('method');
                $this->model['results'] = SimpleCartStoreTaxModel::checkTax($params);
                //エラー無し
                if ($this->model['results']===true) {
                    SimpleCartStoreTaxModel::saveTax($params);
                    $r = array(
                        'bp-slug-main' => $bp->simplecart_store->slug,
                        'bp-slug-sub'  => SC_BPID_TAX,
                        'mu-slug-main' => SCLNG_PAGE_STORE_INFO,
                        'mu-slug-sub'  => SCLNG_PAGE_STORE_TAX
                    );
                    $this->redirect($r);
                }
                //エラー有り
                else {
                    $this->model['tax'] = $params;
                    $this->model['tax_method'] = $sc_tax_method;
                    $this->exec('store/tax');
                }
                break;
            //
            default:
                $this->model['tax_method'] = $sc_tax_method;
                $this->model['tax'] = SimpleCartStoreTaxModel::getTax();
                $this->exec('store/tax');
                break;
        }
    }

    /**
     * HTML Output Header Area
     *
     */
    function header() {
        if ($this->mode==SC_MODE_BP) {
            _e(SCLNG_STORE_TAX_INFO, SC_DOMAIN);
        }
        else {
            SimpleCartFunctions::storeTabMenu(SC_PGID_STORE_TAX);
        }
    }

    /**
     * HTML Output Title Area
     *
     */
    function title() {
        ?>
        <h2><?php _e(SCLNG_STORE_TAX_INFO_UPDATE, SC_DOMAIN); ?></h2>
        <?php
    }

    /**
     * HTML Output Footer Area
     *
     */
    function footer() {
    }
}
