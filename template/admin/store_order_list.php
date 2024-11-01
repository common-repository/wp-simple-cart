<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Store Order List Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_order_list.php 140016 2009-07-28 06:57:23Z tajima $
 */
$status = $this->model['status'];
$store_user = $this->model['store_user'];
$order_count = $this->model['order_count'];
$order_list = $this->model['order_list'];
$order_search = $this->model['order_search'];

$yy = $order_search['s_order_fyy'];
$mm = $order_search['s_order_fmm'];
$dd = $order_search['s_order_fdd'];

$top_link = site_url('wp-admin/admin.php?page=SimpleCartAdminStoreManage.php');
$base_link = site_url('wp-admin/admin.php?page=SimpleCartAdminStoreManage.php&sc_action=search_store_order');
$order_link = site_url('wp-admin/admin.php?page=SimpleCartAdminStoreManage.php&sc_action=store_order');
?>
<p>&raquo;<?php echo $this->link(array('href'=>$top_link, 'value'=>__('Back'))) ?></p>

<h3><?php _e(SCLNG_ADMIN_ORDER_SEARCH, SC_DOMAIN) ?></h3>
<form action="<?php echo $base_link ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'s_store_id', 'value'=>$order_search['s_store_id'])); ?>
<table class="widefat" cellspacing="0" style="width:90%;">
  <thead>
  <tr>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_NO, SC_DOMAIN) ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_STATUS, SC_DOMAIN) ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_LOGIN, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_USER, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_DATE, SC_DOMAIN) ?></th>
  </tr>
  </thead>
  <tbody id="orders" class="list:user user-list">
  <tr>
    <td><?php echo $this->input(array('id'=>'s_order_no',     'value'=>$order_search['s_order_no'])); ?></td>
    <td><?php echo $this->select(array('id'=>'s_order_status'), array('list'=>array(''=>'')+$status, 'default'=>$order_search['s_order_status'])); ?></td>
    <td><?php echo $this->input(array('id'=>'s_order_login',  'value'=>$order_search['s_order_login'])); ?></td>
    <td><?php echo $this->input(array('id'=>'s_order_user',   'value'=>$order_search['s_order_user'])); ?></td>
    <td>
    <?php
        $params_from = array();
        $params_from['year_id']  = 's_order_fyy';
        $params_from['month_id'] = 's_order_fmm';
        $params_from['day_id']   = 's_order_fdd';
        $params_from['year']     = $yy;
        $params_from['month']    = $mm;
        $params_from['day']      = $dd;
    ?>
    <?php $this->ymd($params_from);?>
    </td>
  </tr>
  <tr>
    <td cols="5">
      <p class="submit" style="margin-top:0px;margin-bottom:0px;padding-top:0px;padding-bottom:0px;"><?php echo $this->input(array('type'=>'submit', 'value'=>__(SCLNG_SEARCH, SC_DOMAIN)." &raquo;")); ?></p>
    </td>
  </tr>
</table>
</form>

<form name="frm_order" action="<?php echo $order_link ?>" method="post">
<?php $this->hvars(); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'order_id', 'value'=>'')); ?>
</form>

<h3><?php _e(SCLNG_ADMIN_ORDER_LIST, SC_DOMAIN) ?></h3>
<?php
    if (!is_null($yy) && $yy!='') {
        $yy = sprintf(SCLNG_ADMIN_MANAGE_SALES_YEAR, $yy);
    }
    if (!is_null($mm) && $mm!='') {
        $mm = sprintf(SCLNG_ADMIN_MANAGE_SALES_MONTH, $mm);
    }
?>
<h4><?php echo $store_user['display_name'] ?> <?php echo $yy ?><?php echo $mm ?></h4>

<?php //受注一覧 ?>
<div class="sc_list_header" style="width:90%;">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$order_count)); ?></div>
</div>

<table class="widefat" cellspacing="0" style="width:90%;">
  <thead>
  <tr>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_NO, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_STATUS, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_LOGIN, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_USER, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_DATE, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_ORDER_TOTAL, SC_DOMAIN); ?></th>
  </tr>
  </thead>
  <tbody id="orders" class="list:user user-list">
  <?php if (is_array($order_list)): ?>
  <?php foreach ($order_list as $order): ?>
  <?php
    $class = ('alternate'==$class)?'':'alternate';
    $link = $this->link(array('href'=>'#', 'value'=>SimpleCartFunctions::LPAD($order['id'], 8), 'onclick'=>"Javascript:document.getElementById('order_id').value=".$order['id'].";submitForm('frm_order');return false;"));
  ?>
  <tr class="<?php echo $class; ?>">
    <td class="center"><?php echo $link; ?></td>
    <td class="center"><?php echo $status[$order['status']]; ?></td>
    <td><?php echo $order['user_login']; ?></td>
    <td><?php echo $order['user_name']; ?></td>
    <td class="center"><?php echo $order['regist_date']; ?></td>
    <td class="right"><?php echo $this->money($order['total']); ?></td>
  </tr>
  <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>
<?
