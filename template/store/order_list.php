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
 * @version     svn:$Id: order_list.php 143340 2009-08-06 11:47:12Z tajima $
 */
$order_search = $this->model['order_search'];
$order_count = $this->model['order_count'];
$order_list = $this->model['order_list'];
$status = $this->model['status'];

$csv_download_url     = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-order-csv.php';
$csv_all_download_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-order-csv-all.php';
?>
<form name="order_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'pager_id', 'value'=>'1')); ?>

<!-- 検索条件 -->
<div class="sc_order_list wd500">
<table class="wd500">
<tr>
  <th class="wd80"><?php _e(SCLNG_STORE_ORDER_DATE, SC_DOMAIN) ?></th>
  <td colspan="3">
  <?php
    $params_from = array();
    $params_from['year_id']  = 's_order_fyy';
    $params_from['month_id'] = 's_order_fmm';
    $params_from['day_id']   = 's_order_fdd';
    $params_from['year']     = $order_search['s_order_fyy'];
    $params_from['month']    = $order_search['s_order_fmm'];
    $params_from['day']      = $order_search['s_order_fdd'];
    $params_to = array();
    $params_to['year_id']  = 's_order_tyy';
    $params_to['month_id'] = 's_order_tmm';
    $params_to['day_id']   = 's_order_tdd';
    $params_to['year']     = $order_search['s_order_tyy'];
    $params_to['month']    = $order_search['s_order_tmm'];
    $params_to['day']      = $order_search['s_order_tdd'];
  ?>
  From <?php $this->ymd($params_from);?> To <?php $this->ymd($params_to);?>
  </td>
</tr>
<tr>
  <th class="wd80"><?php _e(SCLNG_STORE_ORDER_STATUS, SC_DOMAIN) ?></th>
  <td><?php echo $this->select(array('id'=>'s_order_status'), array('list'=>array(''=>'')+$status, 'default'=>$order_search['s_order_status'])); ?></td>
  <th class="wd80"><?php _e(SCLNG_STORE_ORDER_NO, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('id'=>'s_order_no', 'value'=>$order_search['s_order_no'], 'class'=>'input_120')); ?> (like)</td>
</tr>
<tr>
  <td colspan="4" class="center"><?php echo $this->submit(array('value'=>__(SCLNG_SEARCH, SC_DOMAIN), 'onclick'=>"Javascript:actionForm('order_list', 'search_order');")); ?></td>
</tr>
</table>
</div>

<!-- 受注一覧 -->
<div class="sc_list_header wd500">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$order_count)); ?></div>
  <span><?php echo $this->submit(array('id'=>'order_download', 'value'=>__(SCLNG_STORE_ORDER_CSV_DOWNLOAD, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('order_list', '', '" . $csv_download_url . "');return false;")); ?></span>
  <span><?php echo $this->submit(array('id'=>'order_download_all', 'value'=>__(SCLNG_STORE_ORDER_CSV_DOWNLOAD_ALL, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('order_list', '', '" . $csv_all_download_url . "');return false;")); ?></span>
</div>

<div class="sc_order_list wd500">
<table class="wd500">
<tr>
  <th class="wd40"><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_DATE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_NO, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_STATUS, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_ORDER_TOTAL, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($order_list)): ?>
<?php foreach ($order_list as $order): ?>
<tr>
  <td class="wd40 center"><?php echo $this->link(array('href'=>'#', 'value'=>SCLNG_DELETE, 'onclick'=>"Javascript:if(confirm('".__(SCLNG_CONFIRM_DELETE_ORDER, SC_DOMAIN)."')){document.getElementById('order_id').value='".$order['id']."';submitForm('order_list', 'delete_order');}return false;")); ?></td>
  <td class="wd80 center"><?php echo $order['regist_date']; ?></td>
  <td class="wd60 center"><?php echo $this->link(array('href'=>'#', 'value'=>SimpleCartFunctions::LPAD($order['id'], 8), 'onclick'=>"Javascript:document.getElementById('order_id').value='".$order['id']."';submitForm('order_list', 'order');return false;")); ?></td>
  <td class="wd60 center"><?php echo $status[$order['status']]; ?></td>
  <td><?php echo $order['user_name']; ?></td>
  <td class="wd80 right"><?php echo $this->money($order['total']); ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</form>
