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
 * @version     svn:$Id: SimpleCartGlobal.php 140016 2009-07-28 06:57:23Z tajima $
 */

//このファイルは必要に応じて適宜メンテする。

//日本固有(stateで使用する)国際化対応しない
//ISO3166-2
$sc_states = array(
    '01' => '北海道',
    '02' => '青森県',
    '03' => '岩手県',
    '04' => '宮城県',
    '05' => '秋田県',
    '06' => '山形県',
    '07' => '福島県',
    '08' => '茨城県',
    '09' => '栃木県',
    '10' => '群馬県',
    '11' => '埼玉県',
    '12' => '千葉県',
    '13' => '東京都',
    '14' => '神奈川県',
    '15' => '新潟県',
    '16' => '富山県',
    '17' => '石川県',
    '18' => '福井県',
    '19' => '山梨県',
    '20' => '長野県',
    '21' => '岐阜県',
    '22' => '静岡県',
    '23' => '愛知県',
    '24' => '三重県',
    '25' => '滋賀県',
    '26' => '京都府',
    '27' => '大阪府',
    '28' => '兵庫県',
    '29' => '奈良県',
    '30' => '和歌山県',
    '31' => '鳥取県',
    '32' => '島根県',
    '33' => '岡山県',
    '34' => '広島県',
    '35' => '山口県',
    '36' => '徳島県',
    '37' => '香川県',
    '38' => '愛媛県',
    '39' => '高知県',
    '40' => '福岡県',
    '41' => '佐賀県',
    '42' => '長崎県',
    '43' => '熊本県',
    '44' => '大分県',
    '45' => '宮崎県',
    '46' => '鹿児島県',
    '47' => '沖縄県'
);

$sc_delivery_companys = array(
    '1' => '郵便局（ゆうパック）',
    '2' => 'ヤマト運輸（クロネコヤマト）',
    '3' => '日本通運（ペリカン便）',
    '4' => '佐川急便（飛脚）',
    '5' => 'その他'
);

$sc_delivery_times = array(
    '1' => 'いつでも',
    '2' => '午前',
    '3' => '午後',
    '4' => '夕方以降'
);

$sc_paid_methods = array(
    '1' => '代金引換',
    '2' => '銀行振込',
    '3' => '郵便振替',
    '4' => '現金書留'
);

$sc_tax_method = array(
    '1'  => '四捨五入',
    '2'  => '切上げ',
    '3'  => '切捨て'
);

$sc_recommend = array(
    '1'  => '★',
    '2'  => '★★',
    '3'  => '★★★',
    '4'  => '★★★★',
    '5'  => '★★★★★'
);

$sc_status = array(
    SC_NEW_RECEIPT      => __(SCLNG_STATUS_NEW_RECEIPT, SC_DOMAIN),
    SC_PAYMENT_WAITING  => __(SCLNG_STATUS_PAYMENT_WAITING, SC_DOMAIN),
    SC_MONEY_RECEIVED   => __(SCLNG_STATUS_MONEY_RECEIVED, SC_DOMAIN),
    SC_SENT_OUT         => __(SCLNG_STATUS_SENT_OUT, SC_DOMAIN),
    SC_CANCEL           => __(SCLNG_STATUS_CANCEL, SC_DOMAIN),
    SC_BACK_ORDER       => __(SCLNG_STATUS_BACK_ORDER, SC_DOMAIN),
    SC_RETURNED         => __(SCLNG_STATUS_RETURNED, SC_DOMAIN)
);

$sc_csv_order = array(
    'id'                 => __(SCLNG_CSV_ORDER_ID, SC_DOMAIN),
    'user_id'            => __(SCLNG_CSV_ORDER_USER_ID, SC_DOMAIN),
    'deliver_fee'        => __(SCLNG_CSV_ORDER_DELIVER_FEE, SC_DOMAIN),
    'commission'         => __(SCLNG_CSV_ORDER_COMMISSION, SC_DOMAIN),
    'total'              => __(SCLNG_CSV_ORDER_TOTAL, SC_DOMAIN),
    'deliver_name'       => __(SCLNG_CSV_ORDER_DELIVER_NAME, SC_DOMAIN),
    'deliver_value_name' => __(SCLNG_CSV_ORDER_DELIVER_VALUE_NAME, SC_DOMAIN),
    'deliver_time_name'  => __(SCLNG_CSV_ORDER_DELIVERY_TIME_NAME, SC_DOMAIN),
    'paid_method_name'   => __(SCLNG_CSV_ORDER_PAID_METHOD_NAME, SC_DOMAIN),
    'send_first_name'    => __(SCLNG_CSV_ORDER_SEND_FIRST_NAME, SC_DOMAIN),
    'send_last_name'     => __(SCLNG_CSV_ORDER_SEND_LAST_NAME, SC_DOMAIN),
    'send_first_furi'    => __(SCLNG_CSV_ORDER_SEND_FIRST_FURI, SC_DOMAIN),
    'send_last_furi'     => __(SCLNG_CSV_ORDER_SEND_LAST_FURI, SC_DOMAIN),
    'send_address'       => __(SCLNG_CSV_ORDER_SEND_ADDRESS, SC_DOMAIN),
    'send_street'        => __(SCLNG_CSV_ORDER_SEND_STREET, SC_DOMAIN),
    'send_state'         => __(SCLNG_CSV_ORDER_SEND_STATE, SC_DOMAIN),
    'send_zip'           => __(SCLNG_CSV_ORDER_SEND_ZIP, SC_DOMAIN),
    'send_tel'           => __(SCLNG_CSV_ORDER_SEND_TEL, SC_DOMAIN),
    'send_fax'           => __(SCLNG_CSV_ORDER_SEND_FAX, SC_DOMAIN),
    'send_mobile'        => __(SCLNG_CSV_ORDER_SEND_MOBILE, SC_DOMAIN),
    'send_mail'          => __(SCLNG_CSV_ORDER_SEND_MAIL, SC_DOMAIN),
    'bill_first_name'    => __(SCLNG_CSV_ORDER_BILL_FIRST_NAME, SC_DOMAIN),
    'bill_last_name'     => __(SCLNG_CSV_ORDER_BILL_LAST_NAME, SC_DOMAIN),
    'bill_first_furi'    => __(SCLNG_CSV_ORDER_BILL_FIRST_FURI, SC_DOMAIN),
    'bill_last_furi'     => __(SCLNG_CSV_ORDER_BILL_LAST_FURI, SC_DOMAIN),
    'bill_address'       => __(SCLNG_CSV_ORDER_BILL_ADDRESS, SC_DOMAIN),
    'bill_street'        => __(SCLNG_CSV_ORDER_BILL_STREET, SC_DOMAIN),
    'bill_state'         => __(SCLNG_CSV_ORDER_BILL_STATE, SC_DOMAIN),
    'bill_zip'           => __(SCLNG_CSV_ORDER_BILL_ZIP, SC_DOMAIN),
    'bill_tel'           => __(SCLNG_CSV_ORDER_BILL_TEL, SC_DOMAIN),
    'bill_fax'           => __(SCLNG_CSV_ORDER_BILL_FAX, SC_DOMAIN),
    'bill_mobile'        => __(SCLNG_CSV_ORDER_BILL_MOBILE, SC_DOMAIN),
    'bill_mail'          => __(SCLNG_CSV_ORDER_BILL_MAIL, SC_DOMAIN),
    'regist_date'        => __(SCLNG_CSV_ORDER_REGIST_DATE, SC_DOMAIN)
);





$sc_page_content_ids = array(
    'sc_store_info'                      => "\[SimpleCart_Store_Manage_Page\]",
    'sc_store_order_manage'              => "\[SimpleCart_Store_Order_Manage_Page\]",
    'sc_store_product_list'              => "\[SimpleCart_Store_Product_List_Page\]",
    'sc_store_product_register'          => "\[SimpleCart_Store_Product_Register_Page\]",
    'sc_store_variation_manage'          => "\[SimpleCart_Store_Variation_Manage_Page\]",
    'sc_store_variation_relation_manage' => "\[SimpleCart_Store_Variation_Relation_Manage_Page\]",
    'sc_store_delivery_manage'           => "\[SimpleCart_Store_Delivery_Manage_Page\]",
    'sc_store_tax_manage'                => "\[SimpleCart_Store_Tax_Manage_Page\]",
    'sc_store_paid_manage'               => "\[SimpleCart_Store_Paid_Manage_Page\]",
    'sc_store_user_manage'               => "\[SimpleCart_Store_User_Manage_Page\]",
    'sc_store_review_manage'             => "\[SimpleCart_Store_Review_Manage_Page\]",
    'sc_user_info'                       => "\[SimpleCart_User_Information_Page\]",
    'sc_user_history'                    => "\[SimpleCart_User_Purchase_History_List_Page\]",
    'sc_user_cart'                       => "\[SimpleCart_User_Cart_Page\]",
    'sc_user_favorite'                   => "\[SimpleCart_Favorite_Page\]",
    'sc_public_top'                      => "\[SimpleCart_Public_Top_Page\]",
    'sc_public_product_list'             => "\[SimpleCart_Public_Product_List_Page\]",
    'sc_public_product'                  => "\[SimpleCart_Public_Product_Page\]",
    'sc_public_member_entry'             => "\[SimpleCart_Public_Member_Entry_Page\]",
    'sc_public_store'                    => "\[SimpleCart_Public_Store_Page\]"
);

