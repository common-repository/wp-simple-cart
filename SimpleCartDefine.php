<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Define
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartDefine.php 162120 2009-10-10 13:28:30Z tajima $
 */

define('SC_DOMAIN',                         'simple-cart');
define('SC_VERSION',                        '1.0.15');
define('SC_PLUGIN_NAME',                    'wp-simple-cart');
define('SC_PLUGIN_FILE',                    'wp-simple-cart.php');
define('SC_ROLE',                           'simple_cart_store_user');  //独自ロール
define('SC_CAP',                            'simple_cart');             //独自役割
define('SC_DEFAULT_STATE',                  '23');                      //店舗会員登録時の都道府県デフォルト
define('SC_DEFAULT_TAX',                    '5');                       //店舗会員登録時の税率デフォルト
define('SC_DEFAULT_TAX_METHOD',             '1');                       //店舗会員登録時の税金計算方法デフォルト（1:四捨五入 2:切上げ 3:切捨て）
define('SC_MODE_BP',                        '1');                       //BuddyPress Mypageを使用する
define('SC_PAGE_COUNT',                     '20');                      //1ページ表示件数
define('SC_PRICE_FORMAT',                   "%s円");                    //価格表示形式
define('SC_USER_ENABLE',                    '90');                      //
define('SC_USER_DISABLE',                   '91');                      //

//自動生成ページ用識別名
define('SC_PGID_STORE_INFO',                'store_member_page_id');
define('SC_PGID_STORE_ORDER',               'store_order_manage_id');
define('SC_PGID_STORE_PRODUCT_LIST',        'store_product_list_id');
define('SC_PGID_STORE_PRODUCT',             'store_product_register_id');
define('SC_PGID_STORE_VARIATION',           'store_variation_manage_id');
define('SC_PGID_STORE_VARIATION_RELATION',  'store_variation_relation_manage_id');
define('SC_PGID_STORE_DELIVERY',            'store_delivery_manage_id');
define('SC_PGID_STORE_TAX',                 'store_tax_manage_id');
define('SC_PGID_STORE_PAID',                'store_paid_manage_id');
define('SC_PGID_STORE_USER',                'store_user_manage_id');
define('SC_PGID_STORE_REVIEW',              'store_review_manage_id');
define('SC_PGID_USER_INFO',                 'user_information_page_id');
define('SC_PGID_USER_HISTORY',              'user_purchase_history_list_id');
define('SC_PGID_USER_CART',                 'user_cart_id');
define('SC_PGID_USER_FAVORITE',             'user_favorite_id');
define('SC_PGID_PUBLIC_TOP',                'public_top_id');
define('SC_PGID_PUBLIC_PRODUCT_LIST',       'public_product_list_id');
define('SC_PGID_PUBLIC_PRODUCT',            'public_product_id');
define('SC_PGID_PUBLIC_MEMEBER_ENTRY',      'public_member_entry_id');
define('SC_PGID_PUBLIC_STORE',              'public_store_id');

//BP-SLUG用
define('SC_BPID_STORE_INFO',                'store-info');
define('SC_BPID_ORDER',                     'order');
define('SC_BPID_PRODUCT_LIST',              'product-list');
define('SC_BPID_PRODUCT',                   'product');
define('SC_BPID_VARIATION',                 'variation');
define('SC_BPID_VARIATION_RELATION',        'variation-relation');
define('SC_BPID_DELIVERY',                  'delivery');
define('SC_BPID_TAX',                       'tax');
define('SC_BPID_PAID',                      'paid');
define('SC_BPID_STORE_USER',                'store-user');
define('SC_BPID_REVIEW',                    'review');
define('SC_BPID_USER_INFO',                 'user-info');
define('SC_BPID_CART',                      'cart');
define('SC_BPID_HISTORY',                   'history');
define('SC_BPID_FAVORITE',                  'favorite');

//画像ファイル名
define('SC_IMAGE_001',                      '001');//画像１ファイル名
define('SC_IMAGE_002',                      '002');//画像２ファイル名
define('SC_IMAGE_003',                      '003');//画像３ファイル名

//受注ステータス
define('SC_NEW_RECEIPT',                    '1');//新規受付
define('SC_PAYMENT_WAITING',                '2');//入金待ち
define('SC_MONEY_RECEIVED',                 '3');//入金済み
define('SC_SENT_OUT',                       '4');//発送済み
define('SC_CANCEL',                         '5');//キャンセル
define('SC_BACK_ORDER',                     '6');//取寄せ中
define('SC_RETURNED',                       '7');//返品

//メール関連
define('SC_ADMIN_FROM', 'tajima@exbridge.jp');

