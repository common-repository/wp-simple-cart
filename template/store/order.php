<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Order Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: order.php 147731 2009-08-21 05:28:05Z tajima $
 */
$order = $this->model['order'];
$status = $this->model['status'];
$states = $this->model['states'];

$invoice_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-invoice.php?order_id=' . $order['id'];
?>
<form name="order" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_action', 'name'=>'sc_action', 'value'=>'change_status')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_id', 'value'=>$order['id'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'download', 'value'=>$order['download'])); ?>
<?php echo $this->hvars(); ?>

<p class="submit">
<?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('order', '');return false;")); ?>
<span class="ml10"><?php echo $this->submit(array('type'=>'button', 'value'=>__(SCLNG_STORE_ORDER_PRINT, SC_DOMAIN), 'onclick'=>"Javascript:window.open('" . $invoice_url . "', '_blank', 'width=800px,height=800px');return false;")); ?></span>
</p>

<?php //受注情報 ?>
<div class="sc_subtitle"><?php echo SCLNG_STORE_ORDER_STATUS_INFO ?></div>
<div class="sc_order_list wd300">
<table class="wd300">
<tr>
  <th class="wd100"><?php _e(SCLNG_STORE_ORDER_STATUS, SC_DOMAIN); ?></th>
  <td>
    <?php echo $this->select(array('id'=>'status'), array('list'=>$status, 'default'=>$order['status'])); ?>
    <?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?>
  </td>
</tr>
</table>
</div>
</form>

<?php //ダウンロード製品についての説明 ?>
<?php if($order['download']=='1'): ?>
<div class="sc_order_download_info">
<div class="sc_order_download_title"><?php _e(SCLNG_STORE_ORDER_DOWNLOAD_PRODUCT_TITLE, SC_DOMAIN); ?></div>
<?php _e(SCLNG_STORE_ORDER_DOWNLOAD_PRODUCT_MESSAGE, SC_DOMAIN); ?>
</div>
<?php endif; ?>

<?php //請求情報 ?>
<div class="sc_subtitle"><?php echo SCLNG_STORE_ORDER_BILL_INFO ?></div>
<div class="sc_order_list wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_STORE_ORDER_DATE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_NO, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_TOTAL, SC_DOMAIN); ?></th>
</tr>
<tr>
  <td class="wd80 center"><?php echo $order['regist_date']; ?></td>
  <td class="wd60 center"><?php echo SimpleCartFunctions::LPAD($order['id'], 8); ?></td>
  <td><?php echo $order['user_name']; ?></td>
  <td class="wd80 right"><?php echo $this->money($order['total']); ?></td>
</tr>
<?php //小計 ?>
<?php if($order['deliver_fee']>0 || $order['commission']>0): ?>
<tr>
  <td class="right" colspan="3"><?php _e(SCLNG_STORE_ORDER_SUB_TOTAL, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $this->money($order['total']-$order['deliver_fee']-$order['commission']) ?></td>
</tr>
<?php endif; ?>
<?php //配送料 ?>
<?php if($order['deliver_fee']>0): ?>
<tr>
  <td class="right" colspan="3"><?php _e(SCLNG_STORE_ORDER_DELIVERY_COST, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $this->money($order['deliver_fee']) ?></td>
</tr>
<?php endif; ?>
<?php //手数料 ?>
<?php if($order['commission']>0): ?>
<tr>
  <td class="right" colspan="3"><?php _e(SCLNG_STORE_ORDER_COMMISSION, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $this->money($order['commission']) ?></td>
</tr>
<?php endif; ?>
<?php //配送業者 ?>
<?php if($order['deliver_id']>0): ?>
<tr>
  <td class="right" colspan="3"><?php _e(SCLNG_STORE_ORDER_DELIVERY, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $order['deliver_name']; ?></td>
</tr>
<?php endif; ?>
<?php //配送時間帯 ?>
<?php if($order['delivery_time']>0): ?>
<tr>
  <td class="right" colspan="3"><?php _e(SCLNG_STORE_ORDER_DELIVERY_TIME, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $order['delivery_time_name']; ?></td>
</tr>
<?php endif; ?>
<?php //請求方法 ?>
<tr>
  <td class="right" colspan="3"><?php _e(SCLNG_STORE_ORDER_PAID_METHOD, SC_DOMAIN); ?></td>
  <td class="right"><?php echo $order['paid_method_name']; ?></td>
</tr>
<?php //備考 ?>
<tr>
  <th class="center" colspan="4"><?php echo SCLNG_STORE_ORDER_MESSAGE; ?></td>
</tr>
<tr>
  <td class="right" colspan="4"><?php echo nl2br($order['message']); ?></td>
</tr>
</table>
</div>

<?php //受注商品情報 ?>
<div class="sc_subtitle"><?php echo SCLNG_STORE_ORDER_PRODUCT_INFO ?></div>
<div class="sc_order_list wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_STORE_ORDER_PRODUCT_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_VARIATION, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_CATEGORY, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_QUANTITY, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_PRICE, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($order['data'])): ?>
<?php foreach ($order['data'] as $product): ?>
<?php
    //カテゴリー情報
    $categorys = '';
    if (is_array($product['categorys'])) {
        foreach ($product['categorys'] as $category) {
            $cate = get_category($category);
            $categorys .= $cate->name . '<br/>';
        }
    }

    //製品名
    $product_name = $product['product_cd'] . '<br/>' . $product['product_name'];
    if ($product['download']=='1') {
        $product_name .= '<div class="dl_product">' . __(SCLNG_DOWNLOAD, SC_DOMAIN) . '</div>';
    }
?>
<tr>
  <td><?php echo $product_name ?></td>
  <td class="wd120"><?php echo nl2br($product['variation_name']) ?></td>
  <td class="wd120"><?php echo $categorys ?></td>
  <td class="wd60 center"><?php echo $product['su'] ?></td>
  <td class="wd80 right"><?php echo $this->money(($product['price']-$product['off'])*$product['su']) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>

<?php //送付先情報 ?>
<div class="sc_subtitle"><?php echo SCLNG_STORE_ORDER_SEND_ADDRESS ?></div>
<div class="sc_order_list wd500">
<table class="wd500">
<tr>
  <th class="wd100"><?php echo SCLNG_STORE_ORDER_SEND_NAME; ?></th>
  <td><?php echo $order['send_last_name']; ?> <?php echo $order['send_first_name']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_FURI; ?></th>
  <td><?php echo $order['send_last_furi']; ?> <?php echo $order['send_first_furi']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_ZIP; ?></th>
  <td><?php echo $order['send_zip']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_STATE; ?></th>
  <td><?php echo $states[$order['send_state']]; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_STREET; ?></th>
  <td><?php echo $order['send_street']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_ADDRESS; ?></th>
  <td><?php echo $order['send_address']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_TEL; ?></th>
  <td><?php echo $order['send_tel']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_MOBILE; ?></th>
  <td><?php echo $order['send_mobile']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_EMAIL; ?></th>
  <td><?php echo $order['send_mail']; ?></td>
</tr>
</table>
</div>

<?php //請求先情報 ?>
<div class="sc_subtitle"><?php echo SCLNG_STORE_ORDER_BILL_ADDRESS ?></div>
<div class="sc_order_list wd500">
<table class="wd500">
<tr>
  <th class="wd100"><?php echo SCLNG_STORE_ORDER_BILL_NAME; ?></th>
  <td><?php echo $order['bill_last_name']; ?> <?php echo $order['bill_first_name']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_FURI; ?></th>
  <td><?php echo $order['bill_last_furi']; ?> <?php echo $order['bill_first_furi']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_ZIP; ?></th>
  <td><?php echo $order['bill_zip']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_STATE; ?></th>
  <td><?php echo $states[$order['bill_state']]; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_STREET; ?></th>
  <td><?php echo $order['bill_street']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_ADDRESS; ?></th>
  <td><?php echo $order['bill_address']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_TEL; ?></th>
  <td><?php echo $order['bill_tel']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_MOBILE; ?></th>
  <td><?php echo $order['bill_mobile']; ?></td>
</tr>
<tr>
  <th><?php echo SCLNG_STORE_ORDER_EMAIL; ?></th>
  <td><?php echo $order['bill_mail']; ?></td>
</tr>
</table>
</div>

