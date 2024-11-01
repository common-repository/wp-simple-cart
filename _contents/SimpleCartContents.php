<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Contents Functions
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartContents.php 140016 2009-07-28 06:57:23Z tajima $
 */
function simplecart_title_controller($title = '') {
    global $post, $sc_page_content_ids;

    foreach ($sc_page_content_ids as $function_id=>$content_id) {
        if (preg_match("/" . $content_id . "/", $post->post_content)) {
            return '';
        } 
    }
    return $title;
}
function simplecart_contents_controller($content = '') {
    global $sc_page_content_ids;

    foreach ($sc_page_content_ids as $function_id=>$content_id) {
        if (preg_match("/" . $content_id . "/", $content)) {
            ob_start();
            $function_id();
            $output = ob_get_contents();
            ob_end_clean();
            return preg_replace("/(<p>)*" . $content_id . "(<\/p>)*/", $output, $content);
        } 
    }
    return $content;
}

/**
 * sc_store_info()
 *
 */
function sc_store_info() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreInfoModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreInfo.php');
    $sc_controller = & new SimpleCartStoreInfo();
    $sc_controller->execute();
}

/**
 * sc_store_order_manage()
 *
 */
function sc_store_order_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreOrderModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreOrder.php');
    $sc_controller = & new SimpleCartStoreOrder();
    $sc_controller->execute();
}

/**
 * sc_store_product_list()
 *
 */
function sc_store_product_list() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreProductModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreProductList.php');
    $sc_controller = & new SimpleCartStoreProductList();
    $sc_controller->execute();
}

/**
 * sc_store_product_register()
 *
 */
function sc_store_product_register() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreProductModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreProductRegister.php');
    $sc_controller = & new SimpleCartStoreProductRegister();
    $sc_controller->execute();
}

/**
 * sc_store_variation_manage()
 *
 */
function sc_store_variation_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreVariation.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreVariationModel.php');
    $sc_controller = & new SimpleCartStoreVariation();
    $sc_controller->execute();
}

/**
 * sc_store_variation_relation_manage()
 *
 */
function sc_store_variation_relation_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreVariationRelation.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreVariationRelationModel.php');
    $sc_controller = & new SimpleCartStoreVariationRelation();
    $sc_controller->execute();
}

/**
 * sc_store_delivery_manage()
 *
 */
function sc_store_delivery_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreDelivery.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreDeliveryModel.php');
    $sc_controller = & new SimpleCartStoreDelivery();
    $sc_controller->execute();
}

/**
 * sc_store_tax_manage()
 *
 */
function sc_store_tax_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreTax.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreTaxModel.php');
    $sc_controller = & new SimpleCartStoreTax();
    $sc_controller->execute();
}

/**
 * sc_store_paid_manage()
 *
 */
function sc_store_paid_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStorePaid.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStorePaidModel.php');
    $sc_controller = & new SimpleCartStorePaid();
    $sc_controller->execute();
}

/**
 * sc_store_review_manage()
 *
 */
function sc_store_review_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreReview.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreReviewModel.php');
    $sc_controller = & new SimpleCartStoreReview();
    $sc_controller->execute();
}

/**
 * sc_store_user_manage()
 *
 */
function sc_store_user_manage() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/store/SimpleCartStoreUser.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/store/SimpleCartStoreUserModel.php');
    $sc_controller = & new SimpleCartStoreUser();
    $sc_controller->execute();
}

/**
 * sc_user_info()
 *
 */
function sc_user_info() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/user/SimpleCartUserInfo.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/user/SimpleCartUserInfoModel.php');
    $sc_controller = & new SimpleCartUserInfo();
    $sc_controller->execute();
}

/**
 * sc_user_history()
 *
 */
function sc_user_history() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/user/SimpleCartUserHistory.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/user/SimpleCartUserHistoryModel.php');
    $sc_controller = & new SimpleCartUserHistory();
    $sc_controller->execute();
}

/**
 * sc_user_cart()
 *
 */
function sc_user_cart() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/user/SimpleCartUserCart.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/user/SimpleCartUserCartModel.php');
    $sc_controller = & new SimpleCartUserCart();
    $sc_controller->execute();
}

/**
 * sc_user_favorite()
 *
 */
function sc_user_favorite() {
    require_once(PLUGIN_SIMPLE_CART . '/_controller/user/SimpleCartUserFavorite.php');
    require_once(PLUGIN_SIMPLE_CART . '/_model/user/SimpleCartUserFavoriteModel.php');
    $sc_controller = & new SimpleCartUserFavorite();
    $sc_controller->execute();
}

/**
 * sc_public_top()
 *
 */
function sc_public_top() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/public/SimpleCartPublicInfo.php');
    $sc_controller = & new SimpleCartPublicInfo();
    $sc_controller->execute();
}

/**
 * sc_public_product_list()
 *
 */
function sc_public_product_list() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/public/SimpleCartPublicProductList.php');
    $sc_controller = & new SimpleCartPublicProductList();
    $sc_controller->execute();
}

/**
 * sc_public_product()
 *
 */
function sc_public_product() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/public/SimpleCartPublicProductDetail.php');
    $sc_controller = & new SimpleCartPublicProductDetail();
    $sc_controller->execute();
}

/**
 * sc_public_member_entry()
 *
 */
function sc_public_member_entry() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/public/SimpleCartPublicMemberEntry.php');
    $sc_controller = & new SimpleCartPublicMemberEntry();
    $sc_controller->execute();
}

/**
 * sc_public_store()
 *
 */
function sc_public_store() {
    require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
    require_once(PLUGIN_SIMPLE_CART . '/_controller/public/SimpleCartPublicStore.php');
    $sc_controller = & new SimpleCartPublicStore();
    $sc_controller->execute();
}
