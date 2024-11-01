<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart テーブル作成配列
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: createtable.php 155290 2009-09-16 11:08:12Z tajima $
 */

global $wpdb;

//----------------------------------------------------------------
//デフォルトキャラセットの取得
//----------------------------------------------------------------
if (!empty($wpdb->charset)) {
    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
}

//----------------------------------------------------------------
//商品
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_product';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    product_cd          varchar(20) NOT NULL,
    name                text        NOT NULL,
    description         longtext    NOT NULL,
    add_description     longtext    NULL,
    price               varchar(20) NOT NULL DEFAULT 0,
    off                 varchar(20) NOT NULL DEFAULT 0,
    download_price      varchar(20) NOT NULL DEFAULT 0,
    download_off        varchar(20) NOT NULL DEFAULT 0,
    image_file_url1     text        NULL,
    image_file_url2     text        NULL,
    image_file_url3     text        NULL,
    categorys           text        NOT NULL,
    notax               varchar(1)  NOT NULL DEFAULT 0,
    stock_manage        varchar(1)  NOT NULL DEFAULT 0,
    stock               varchar(20) NOT NULL DEFAULT 0,
    quantity_limit      varchar(20) NOT NULL DEFAULT 0,
    publish             varchar(1)  NOT NULL DEFAULT 0,
    download_publish    varchar(1)  NOT NULL DEFAULT 0,
    product_url         text        NULL,
    regist_date         datetime    NOT NULL,
    update_date         datetime    NOT NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_product (store_id, id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//商品レビュー
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_review';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    user_id             bigint(20)  NOT NULL,
    comment             longtext    NOT NULL,
    recommend           varchar(20) NULL,
    regist_date         datetime    NOT NULL,
    KEY key_{$wpdb->blogid}_sc_product_review (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//規格
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_variations';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    name                text        NOT NULL,
    KEY key_{$wpdb->blogid}_sc_product_variations (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//規格詳細
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    name                text        NOT NULL,
    variation_id        bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_variation_values (store_id, variation_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//商品規格関連付け
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_associations';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    associated_id       bigint(20)  NOT NULL,
    variation_id        bigint(20)  NOT NULL,
    download_publish    varchar(1)  NOT NULL DEFAULT 0,
    KEY key_{$wpdb->blogid}_sc_variation_associations1 (store_id, associated_id),
    KEY key_{$wpdb->blogid}_sc_variation_associations2 (store_id, variation_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//商品規格詳細関連付け
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_values_associations';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    value_id            bigint(20)  NOT NULL,
    variation_id        bigint(20)  NOT NULL,
    download_publish    varchar(1)  NOT NULL DEFAULT 0,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_variation_values_associations (store_id, product_id, value_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//商品規格別価格在庫
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_priceandstock';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    stock               varchar(20) NOT NULL,
    price               varchar(32) NOT NULL,
    visible             varchar(1)  NOT NULL,
    download_publish    varchar(1)  NOT NULL DEFAULT 0,
    KEY key_{$wpdb->blogid}_sc_variation_priceandstock (store_id, product_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//価格在庫関連付け
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_variation_combinations';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    pricestock_id       bigint(20)  NOT NULL,
    value_id            bigint(20)  NOT NULL,
    variation_id        bigint(20)  NOT NULL,
    all_variation_id    varchar(64) NOT NULL,
    download_publish    varchar(1)  NOT NULL DEFAULT 0,
    KEY key_{$wpdb->blogid}_sc_variation_combinations1 (store_id, product_id),
    KEY key_{$wpdb->blogid}_sc_variation_combinations2 (store_id, pricestock_id),
    KEY key_{$wpdb->blogid}_sc_variation_combinations3 (store_id, value_id),
    KEY key_{$wpdb->blogid}_sc_variation_combinations4 (store_id, variation_id),
    KEY key_{$wpdb->blogid}_sc_variation_combinations5 (store_id, all_variation_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//消費税
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_tax';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    tax                 varchar(32) NOT NULL,
    method              varchar(1)  NOT NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_tax (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//外部連携
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_product_relation';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    pricestock_id       bigint(20)  NOT NULL,
    value_id            bigint(20)  NOT NULL,
    variation_id        bigint(20)  NOT NULL,
    relation_id         bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_product_relation1 (store_id, product_id),
    KEY key_{$wpdb->blogid}_sc_product_relation2 (store_id, pricestock_id),
    KEY key_{$wpdb->blogid}_sc_product_relation3 (store_id, value_id),
    KEY key_{$wpdb->blogid}_sc_product_relation4 (store_id, variation_id),
    KEY key_{$wpdb->blogid}_sc_product_relation5 (store_id, relation_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//カート
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_cart';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id             bigint(20)  NOT NULL,
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    pricestock_id       bigint(20)  NOT NULL,
    su                  bigint(20)  NOT NULL,
    download            varchar(1)  NOT NULL,
    regist_date         datetime    NOT NULL,
    KEY key_{$wpdb->blogid}_sc_cart (user_id, store_id, product_id, pricestock_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//注文
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_order';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_no            varchar(50) NULL,
    status              varchar(1)  NOT NULL,
    user_id             bigint(20)  NOT NULL,
    store_id            bigint(20)  NOT NULL,
    deliver_id          bigint(20)  NULL,
    deliver_name        text        NULL,
    deliver_value_id    bigint(20)  NULL,
    deliver_value_name  text        NULL,
    message             longtext    NULL,
    deliver_fee         varchar(32) NOT NULL DEFAULT 0,
    commission          varchar(32) NOT NULL DEFAULT 0,
    total               varchar(32) NOT NULL,
    download            varchar(1)  NOT NULL,
    paid_method         bigint(20)  NOT NULL,
    paid_method_name    text        NOT NULL,
    delivery_date       text        NOT NULL,
    delivery_time       bigint(20)  NOT NULL,
    delivery_time_name  text        NOT NULL,
    send_first_name     text        NOT NULL,
    send_last_name      text        NOT NULL,
    send_first_furi     text        NOT NULL,
    send_last_furi      text        NOT NULL,
    send_pobox          text        NOT NULL,
    send_address        text        NOT NULL,
    send_street         text        NOT NULL,
    send_city           text        NOT NULL,
    send_state          text        NOT NULL,
    send_zip            text        NOT NULL,
    send_country        text        NOT NULL,
    send_tel            text        NOT NULL,
    send_fax            text        NOT NULL,
    send_mobile         text        NOT NULL,
    send_mail           text        NOT NULL,
    bill_first_name     text        NOT NULL,
    bill_last_name      text        NOT NULL,
    bill_first_furi     text        NOT NULL,
    bill_last_furi      text        NOT NULL,
    bill_pobox          text        NOT NULL,
    bill_address        text        NOT NULL,
    bill_street         text        NOT NULL,
    bill_city           text        NOT NULL,
    bill_state          text        NOT NULL,
    bill_zip            text        NOT NULL,
    bill_country        text        NOT NULL,
    bill_tel            text        NOT NULL,
    bill_fax            text        NOT NULL,
    bill_mobile         text        NOT NULL,
    bill_mail           text        NOT NULL,
    regist_date         datetime    NOT NULL,
    update_date         datetime    NOT NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_order (user_id, store_id, id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//注文詳細
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_detail';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    pricestock_id       bigint(20)  NULL,
    download_hash       varchar(32) NOT NULL,
    download            varchar(1)  NOT NULL DEFAULT 0,
    product_cd          varchar(20) NOT NULL,
    product_name        varchar(200) NOT NULL,
    variation_name      varchar(200) NULL,
    price               varchar(32) NOT NULL DEFAULT 0,
    off                 varchar(32) NOT NULL DEFAULT 0,
    su                  varchar(32) NOT NULL DEFAULT 0,
    tax                 varchar(32) NOT NULL DEFAULT 0,
    KEY key_{$wpdb->blogid}_sc_order_detail (order_id, product_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//注文関連メッセージ（BP連携）
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_message';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id            bigint(20)  NOT NULL,
    message_id          bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_message (order_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//採番
//----------------------------------------------------------------
//$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_order_no';
//$wp_sc_tbls[$table_name] = $table_name;
//$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
//    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
//    prefix              varchar(32) NOT NULL,
//    digit               bigint(20)  NOT NULL,
//    no                  varchar(50) NOT NULL
//    ) {$charset_collate};";

//----------------------------------------------------------------
//配送業者
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    name                text        NOT NULL,
    sort                bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_delivery (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//配送詳細
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_values';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    delivery_id         bigint(20)  NOT NULL,
    name                text        NOT NULL,
    delivery_fee        varchar(32) NOT NULL,
    sort                bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_delivery_values (store_id, delivery_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//配送料無料
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_free';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    price_limit         varchar(32) NOT NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_delivery_free (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//希望配送時間帯
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_delivery_time';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    name                text        NOT NULL
    sort                bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_delivery_time (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//希望支払方法
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_paid_method';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    name                text        NOT NULL,
    commission          varchar(32) NOT NULL DEFAULT 0,
    sort                bigint(20)  NOT NULL,
    KEY key_{$wpdb->blogid}_sc_bill_method (store_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//郵便番号
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_zipcode';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    zip                 text        NOT NULL,
    address             text        NULL,
    street              text        NULL,
    city                text        NULL,
    state               text        NULL,
    country             text        NULL,
    KEY key_{$wpdb->blogid}_sc_zipcode (zip(256))
    ) {$charset_collate};";

//----------------------------------------------------------------
//店舗会員属性
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_store';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id             bigint(20)  NOT NULL,
    pobox               text        NULL,
    address             text        NULL,
    street              text        NULL,
    city                text        NULL,
    state               text        NULL,
    zip                 text        NULL,
    country             text        NULL,
    tel                 text        NULL,
    fax                 text        NULL,
    mobile              text        NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_store (user_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//一般会員属性
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_user';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id             bigint(20)  NOT NULL,
    send_last_name      text        NULL,
    send_first_name     text        NULL,
    send_last_furi      text        NULL,
    send_first_furi     text        NULL,
    send_pobox          text        NULL,
    send_address        text        NULL,
    send_street         text        NULL,
    send_city           text        NULL,
    send_state          text        NULL,
    send_zip            text        NULL,
    send_country        text        NULL,
    send_tel            text        NULL,
    send_fax            text        NULL,
    send_mobile         text        NULL,
    bill_last_name      text        NULL,
    bill_first_name     text        NULL,
    bill_last_furi      text        NULL,
    bill_first_furi     text        NULL,
    bill_pobox          text        NULL,
    bill_address        text        NULL,
    bill_street         text        NULL,
    bill_city           text        NULL,
    bill_state          text        NULL,
    bill_zip            text        NULL,
    bill_country        text        NULL,
    bill_tel            text        NULL,
    bill_fax            text        NULL,
    bill_mobile         text        NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_suer (user_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//お気に入り
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_user_favorite';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id             bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    regist_date         datetime    NOT NULL,
    UNIQUE KEY ukey_{$wpdb->blogid}_sc_user_favorite (user_id, product_id)
    ) {$charset_collate};";

//----------------------------------------------------------------
//商品規格情報中間テーブル
//----------------------------------------------------------------
$table_name = $wpdb->base_prefix . $wpdb->blogid . '_sc_tmp_product_variation';
$wp_sc_tbls[$table_name] = $table_name;
$wp_sc_sqls[$table_name] = "CREATE TABLE {$table_name} (
    id                  bigint(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    store_id            bigint(20)  NOT NULL,
    product_id          bigint(20)  NOT NULL,
    min_stock           varchar(20) NOT NULL,
    max_stock           varchar(20) NOT NULL,
    min_price           varchar(32) NOT NULL,
    max_price           varchar(32) NOT NULL,
    download_publish    varchar(1)  NOT NULL DEFAULT 0,
    KEY key_{$wpdb->blogid}_sc_tmp_product_variation (store_id, product_id)
    ) {$charset_collate};";

