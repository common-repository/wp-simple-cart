<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Order Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_order.php 143340 2009-08-06 11:47:12Z tajima $
 */
$order  = $this->model['order'];
$status = $this->model['status'];
$states = $this->model['states'];
$store_user   = $this->model['store_user'];
$message_list = $this->model['message_list'];
$order_list_link = site_url('wp-admin/admin.php?page=SimpleCartAdminStoreManage.php&sc_action=search_store_order');
?>
<form name="frm_order" action="<?php echo $order_list_link ?>" method="post">
<?php $this->hvars(); ?>
</form>
<p>&raquo;<?php echo $this->link(array('href'=>'#', 'value'=>__('Back'), 'onclick'=>"Javascript:submitForm('frm_order');return false;")) ?></p>

<h3><?php _e(SCLNG_ADMIN_ORDER_INFO, SC_DOMAIN) ?></h3>

<?php //受注情報 ?>
<table class="widefat wd120" cellspacing="0">
  <thead>
  <tr>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_STATUS, SC_DOMAIN); ?></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td class="center"><?php echo $status[$order['status']]; ?></td>
  </tr>
  </tbody>
</table>

<p></p>

<?php //請求情報 ?>
<table class="widefat wd500" cellspacing="0">
  <thead>
  <tr>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_DATE, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_NO, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_USER, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_TOTAL, SC_DOMAIN); ?></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td class="wd120 center"><?php echo $order['regist_date']; ?></td>
    <td class="wd100 center"><?php echo SimpleCartFunctions::LPAD($order['id'], 8); ?></td>
    <td><?php echo $order['user_name']; ?></td>
    <td class="wd120 right"><?php echo $this->money($order['total']); ?></td>
  </tr>
  <?php //小計 ?>
  <?php if($order['deliver_fee']>0 || $order['commission']>0): ?>
  <tr>
    <td class="right" colspan="3"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_SUB_TOTAL, SC_DOMAIN); ?></td>
    <td class="right"><?php echo $this->money($order['total']-$order['deliver_fee']-$order['commission']) ?></td>
  </tr>
  <?php endif; ?>
  <?php //配送料 ?>
  <?php if($order['deliver_fee']>0): ?>
  <tr>
    <td class="right" colspan="3"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_DELIVERY_COST, SC_DOMAIN); ?></td>
    <td class="right"><?php echo $this->money($order['deliver_fee']) ?></td>
  </tr>
  <?php endif; ?>
  <?php //手数料 ?>
  <?php if($order['commission']>0): ?>
  <tr>
    <td class="right" colspan="3"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_COMMISSION, SC_DOMAIN); ?></td>
    <td class="right"><?php echo $this->money($order['commission']) ?></td>
  </tr>
  <?php endif; ?>
  <?php //配送業者 ?>
  <?php if($order['deliver_id']>0): ?>
  <tr>
    <td class="right" colspan="3"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_DELIVERY, SC_DOMAIN); ?></td>
    <td class="right"><?php echo $order['deliver_name']; ?></td>
  </tr>
  <?php endif; ?>
  <?php //配送時間帯 ?>
  <?php if($order['delivery_time']>0): ?>
  <tr>
    <td class="right" colspan="3"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_DELIVERY_TIME, SC_DOMAIN); ?></td>
    <td class="right"><?php echo $order['delivery_time_name']; ?></td>
  </tr>
  <?php endif; ?>
  <?php //請求方法 ?>
  <tr>
    <td class="right" colspan="3"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_PAID_METHOD, SC_DOMAIN); ?></td>
    <td class="right"><?php echo $order['paid_method_name']; ?></td>
  </tr>
  <?php //備考 ?>
  <?php if($order['message']!=''): ?>
  <tr>
    <th class="center" colspan="4"><?php echo SCLNG_ADMIN_MANAGE_ORDER_MESSAGE; ?></td>
  </tr>
  <tr>
    <td class="right" colspan="4"><?php echo nl2br($order['message']); ?></td>
  </tr>
  <?php endif; ?>
  </tbody>
</table>

<p></p>

<?php //受注商品情報 ?>
<table class="widefat" style="width:80%;" cellspacing="0">
  <thead>
  <tr>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_PRODUCT_NAME, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_VARIATION, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_CATEGORY, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_QUANTITY, SC_DOMAIN); ?></th>
    <th class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_PRICE, SC_DOMAIN); ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if (is_array($order['data'])): ?>
  <?php foreach ($order['data'] as $product): ?>
  <?php
    //カテゴリー情報
    $categorys = '<br/>';
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
    <td class="wd120"><?php echo nl2br(trim($product['variation_name'])) ?></td>
    <td class="wd120"><?php echo $categorys ?></td>
    <td class="wd60 center"><?php echo $product['su'] ?></td>
    <td class="wd80 right"><?php echo $this->money(($product['price']-$product['off'])*$product['su']) ?></td>
  </tr>
  <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>

<p></p>

<?php //送付先情報 ?>
<table class="widefat" style="width:85%;" cellspacing="0">
  <thead>
  <tr>
    <th class="center"><br/></th>
    <th class="center"><?php echo SCLNG_ADMIN_MANAGE_ORDER_ADDR_NAME; ?></th>
    <th class="center"><?php echo SCLNG_ADMIN_MANAGE_ORDER_ADDR_FURI; ?></th>
    <th class="center"><?php echo SCLNG_ADMIN_MANAGE_ORDER_ADDR_ZIP; ?></th>
    <th class="center"><?php echo SCLNG_ADMIN_MANAGE_ORDER_ADDR_ADDRESS; ?></th>
    <th class="center"><?php echo SCLNG_ADMIN_MANAGE_ORDER_ADDR_TEL; ?></th>
    <th class="center"><?php echo SCLNG_ADMIN_MANAGE_ORDER_ADDR_EMAIL; ?></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td class="wd40 center"><strong><?php _e(SCLNG_ADMIN_MANAGE_ORDER_SEND, SC_DOMAIN) ?></strong></td>
    <td><?php echo $order['send_last_name']; ?> <?php echo $order['send_first_name']; ?></td>
    <td><?php echo $order['send_last_furi']; ?> <?php echo $order['send_first_furi']; ?></td>
    <td class="wd80"><?php echo $order['send_zip']; ?></td>
    <td><?php echo $states[$order['send_state']]; ?> <?php echo $order['send_street']; ?><br/><?php echo $order['send_address']; ?></td>
    <td class="wd100"><?php echo $order['send_tel']; ?><br/><?php echo $order['send_mobile']; ?></td>
    <td><?php echo $order['send_mail']; ?></td>
  </tr>
  <tr>
    <td class="center"><strong><?php _e(SCLNG_ADMIN_MANAGE_ORDER_BILL, SC_DOMAIN) ?></strong></td>
    <td><?php echo $order['bill_last_name']; ?> <?php echo $order['bill_first_name']; ?></td>
    <td><?php echo $order['bill_last_furi']; ?> <?php echo $order['bill_first_furi']; ?></td>
    <td><?php echo $order['bill_zip']; ?></td>
    <td><?php echo $states[$order['bill_state']]; ?> <?php echo $order['bill_street']; ?><br/><?php echo $order['bill_address']; ?></td>
    <td><?php echo $order['bill_tel']; ?><br/><?php echo $order['bill_mobile']; ?></td>
    <td><?php echo $order['bill_mail']; ?></td>
  </tr>
  </tbody>
</table>

<?php 
//メッセージのやり取り
if (is_array($message_list) && count($message_list) > 0): ?>
<h4><?php _e(SCLNG_ADMIN_MESSAGE_STORE_CUSTOMER, SC_DOMAIN) ?></h4>

<table class="widefat thread_detail" cellspacing="0">
  <thead>
  <tr>
    <th scope="col"><?php _e(SCLNG_ADMIN_STORE_NAME, SC_DOMAIN) ?></th>
    <th class='message' scope="col"><?php _e(SCLNG_MESSAGE, SC_DOMAIN) ?></th>
    <th scope="col"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_USER, SC_DOMAIN) ?></th>
  </tr>
  </thead>
  <tbody>
  <?php 
    $date_format = get_option('date_format') . ' ' . get_option('time_format');
    $thread_sender_nm   = $message_list[0]['sender_nm'];
    $thread_receiver_nm = $message_list[0]['receiver_nm'];
    foreach ($message_list as $message_info) {
        $message_info['sender_id'];
        $message_info['receiver_id'];
        $message_class = '';
        if ($message_info['sender_id'] == $order['store_id']) {
            $message_class = 'message_sender';
        }
        else {
            $message_class = 'message_receiver';
        }
        $message = $message_info['message'];
        if (function_exists('convert_smilies')) {
            $message = convert_smilies($message);
        }
  ?>
    <tr id='id_bp_message_<?php echo $message_info['id']?>' class='<?php echo $message_class;?>'>
      <td><?php echo $store_user['display_name'];?></td>
      <td class="message">
        <div class='message-header'><?php echo mysql2date($date_format, $message_info['date_sent'])?></div>
        <div class='message-body'><?php echo nl2br($message);?></div>
        <div class='message-footer'></div>
      </td>
      <td><?php echo $thread_receiver_nm;?></td>
    </tr>
  <?php 
    }
  ?>
  </tbody>
</table>
<?php endif; ?>
