<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Manage Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_manage.php 141652 2009-08-01 06:16:59Z tajima $
 */
$ymd_info = $this->model['ymd_info'];
$sales_list = $this->model['sales_list'];

$base_link = site_url('wp-admin/admin.php?page=SimpleCartAdminStoreManage.php');
?>
<h3><?php _e(SCLNG_ADMIN_SALES_LIST, SC_DOMAIN) ?></h3>

<?php //受注状況サマリー ?>
<table class="widefat" cellspacing="0" style="width:90%;">
  <thead>
  <tr>
    <?php
        $yymm  = str_replace('-', '',$ymd_info['yymm00']);
        $year  = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm00_lbl = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YYMM, $year.$month), SC_DOMAIN);

        $yymm  = str_replace('-', '',$ymd_info['yymm01']);
        $year  = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm01_lbl = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YYMM, $year.$month), SC_DOMAIN);

        $yymm  = str_replace('-', '',$ymd_info['yymm02']);
        $year  = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm02_lbl = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YYMM, $year.$month), SC_DOMAIN);

        $yymm  = str_replace('-', '',$ymd_info['yymm03']);
        $year  = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm03_lbl = __(sprintf(SCLNG_ADMIN_MANAGE_SALES_YYMM, $year.$month), SC_DOMAIN);
    ?>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_STORE_NAME, SC_DOMAIN); ?></th>
    <th scope="col" class="center"><?php echo $stock_yymm00_lbl; ?></th>
    <th scope="col" class="center"><?php echo $stock_yymm01_lbl; ?></th>
    <th scope="col" class="center"><?php echo $stock_yymm02_lbl; ?></th>
    <th scope="col" class="center"><?php echo $stock_yymm03_lbl; ?></th>
    <th scope="col" class="center"><?php _e(SCLNG_ADMIN_MANAGE_SALES_TOTAL, SC_DOMAIN); ?></th>
  </tr>
  </thead>
  <tbody id="users" class="list:user user-list">
  <?php if (is_array($sales_list)): ?>
  <?php foreach ($sales_list as $sales): ?>
  <?php
    $class = ('alternate'==$class)?'':'alternate';

    //各リンクを作成する
    $yymm00_link = $base_link . '&sc_action=search_store_order&s_store_id='.$sales['store_id'].'&s_order_fyy='.substr($ymd_info['yymm00'],0,4).'&s_order_fmm='.substr($ymd_info['yymm00'],5,2);
    $yymm01_link = $base_link . '&sc_action=search_store_order&s_store_id='.$sales['store_id'].'&s_order_fyy='.substr($ymd_info['yymm01'],0,4).'&s_order_fmm='.substr($ymd_info['yymm01'],5,2);
    $yymm02_link = $base_link . '&sc_action=search_store_order&s_store_id='.$sales['store_id'].'&s_order_fyy='.substr($ymd_info['yymm02'],0,4).'&s_order_fmm='.substr($ymd_info['yymm02'],5,2);
    $yymm03_link = $base_link . '&sc_action=search_store_order&s_store_id='.$sales['store_id'].'&s_order_fyy='.substr($ymd_info['yymm03'],0,4).'&s_order_fmm='.substr($ymd_info['yymm03'],5,2);
    $total_link  = $base_link . '&sc_action=search_store_order&s_store_id='.$sales['store_id'];
    if ($sales['yymm00']>0) {
        $yymm00_link = $this->link(array('href'=>$yymm00_link, 'value'=>$this->money($sales['yymm00'])));
    }
    else {
        $yymm00_link = $this->money($sales['yymm00']);
    }
    if ($sales['yymm01']>0) {
        $yymm01_link = $this->link(array('href'=>$yymm01_link, 'value'=>$this->money($sales['yymm01'])));
    }
    else {
        $yymm01_link = $this->money($sales['yymm01']);
    }
    if ($sales['yymm02']>0) {
        $yymm02_link = $this->link(array('href'=>$yymm02_link, 'value'=>$this->money($sales['yymm02'])));
    }
    else {
        $yymm02_link = $this->money($sales['yymm02']);
    }
    if ($sales['yymm03']>0) {
        $yymm03_link = $this->link(array('href'=>$yymm03_link, 'value'=>$this->money($sales['yymm03'])));
    }
    else {
        $yymm03_link = $this->money($sales['yymm03']);
    }
    if ($sales['total']>0) {
        $total_link = $this->link(array('href'=>$total_link, 'value'=>$this->money($sales['total'])));
    }
    else {
        $total_link = $this->money($sales['total']);
    }
  ?>
  <tr class="<?php echo $class; ?>">
    <td><?php echo $sales['store_name'] ?></td>
    <td class="right"><?php echo $yymm00_link; ?></td>
    <td class="right"><?php echo $yymm01_link; ?></td>
    <td class="right"><?php echo $yymm02_link; ?></td>
    <td class="right"><?php echo $yymm03_link; ?></td>
    <td class="right"><?php echo $total_link; ?></td>
  </tr>
  <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>
