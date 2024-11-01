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
 * @version     svn:$Id: user_history.php 140016 2009-07-28 06:57:23Z tajima $
 */
$order_count = $this->model['order_count'];
$order_list = $this->model['order_list'];
$status = $this->model['status'];
?>
<form name="order_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_list_action', 'name'=>'sc_action', 'value'=>'order')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_id', 'value'=>$order['id'])); ?>

<div class="sc_list_header wd500">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$order_count)); ?></div>
</div>

<div class="sc_cart_list wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_USER_HISTORY_ORDER_DATE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_ORDER_NO, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_STATUS, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_STORE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_USER_HISTORY_TOTAL, SC_DOMAIN); ?></th>
</tr>
<?php if(is_array($order_list)): ?>
<?php foreach($order_list as $order): ?>
<tr>
  <td class="wd80 center"><?php echo substr($order['regist_date'], 0, 10); ?></td>
  <td class="wd60 center"><?php echo $this->link(array('href'=>$_SERVER['REQUEST_URI'], 'value'=>SimpleCartFunctions::LPAD($order['id'], 8), 'onclick'=>"Javascript:document.getElementById('order_id').value='".$order['id']."';submitForm('order_list');return false;")); ?></td>
  <td class="wd80 center"><?php echo $status[$order['status']]; ?></td>
  <td><?php echo $order['store_name']; ?></td>
  <td class="wd80 right"><?php echo $this->money($order['total']); ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</form>
