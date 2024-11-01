<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User History Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_history_detail.php 143626 2009-08-07 12:22:47Z tajima $
 */
$order = $this->model['order'];
$status = $this->model['status'];
?>
<form name="order" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_action', 'name'=>'sc_action', 'value'=>'')); ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('order');return false;")); ?></p>
</form>

<div class="sc_subtitle"><?php echo SCLNG_USER_HISTORY_BILL_INFO ?></div>
<div class="sc_cart_list wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_USER_HISTORY_ORDER_DATE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_ORDER_NO, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_STATUS, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_STORE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_TOTAL, SC_DOMAIN); ?></th>
</tr>
<tr>
  <td class="wd80 center"><?php echo substr($order['regist_date'], 0, 10); ?></td>
  <td class="wd60 center"><?php echo SimpleCartFunctions::LPAD($order['id'], 8); ?></td>
  <td class="wd80 center"><?php echo $status[$order['status']]; ?></td>
  <td><?php echo $order['store_name']; ?></td>
  <td class="wd80 right"><?php echo $this->money($order['total']); ?></td>
</tr>
<?php if($order['deliver_fee']>0 || $order['commission']>0): ?>
<tr>
  <td class="right" colspan="4"><?php _e(SCLNG_USER_HISTORY_SUB_TOTAL, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $this->money($order['total']-$order['deliver_fee']-$order['commission']) ?></td>
</tr>
<?php endif; ?>
<?php if($order['deliver_fee']>0): ?>
<tr>
  <td class="right" colspan="4"><?php _e(SCLNG_USER_HISTORY_DELIVERY, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $this->money($order['deliver_fee']) ?></td>
</tr>
<?php endif; ?>
<?php if($order['commission']>0): ?>
<tr>
  <td class="right" colspan="4"><?php _e(SCLNG_USER_HISTORY_COMMISSION, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $this->money($order['commission']) ?></td>
</tr>
<?php endif; ?>
</table>
</div>

<div class="sc_subtitle"><?php echo SCLNG_USER_HISTORY_PRODUCT_INFO ?></div>
<div class="sc_cart_list wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_USER_HISTORY_IMAGE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_VARIATION, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_CATEGORY, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_QUANTITY, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_PRICE, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($order['data'])): ?>
<?php foreach ($order['data'] as $product): ?>
<?php
    $image_url1 = SimpleCartFunctions::ProductImageUrl($product['product_id'], 1, $product['image_file_url1'], $order['store_id']);
    $image_url3 = SimpleCartFunctions::ProductImageUrl($product['product_id'], 3, $product['image_file_url3'], $order['store_id']);
    $open_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-product.php?image_url=' . $image_url3;

    //カテゴリー情報
    $categorys = '';
    if (is_array($product['categorys'])) {
        foreach ($product['categorys'] as $category) {
            $cate = get_category($category);
            $categorys .= $cate->name . '<br/>';
        }
    }

    $name = $product['product_name'];
    if ($product['download']=='1' && $order['status']==SC_MONEY_RECEIVED) {
        //ダウンロード用URL生成
        $href = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/files/download/' . $product['download_hash'] . '/' . $product['product_url'];
        $val  = __(SCLNG_USER_HISTORY_DOWNLOAD, SC_DOMAIN);
        $name .= '<br/>' . $this->link(array('href'=>$href, 'value'=>$val));
    }
?>
<tr>
  <td class="image"><img class="sc_list_image" src="<?php echo $image_url1 ?>" onclick="javascript:window.open('<?php echo $open_url ?>', '_blank', 'width=384px,height=384px,resizable=no');" /></td>
  <td><?php echo $name ?></td>
  <td class="wd120"><?php echo nl2br($product['variation_name']); ?></td>
  <td class="wd120"><?php echo $categorys ?></td>
  <td class="wd60 center"><?php echo $product['su'] ?></td>
  <td class="wd80 right"><?php echo $this->money(($product['price']-$product['off']+$product['tax'])*$product['su']) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
