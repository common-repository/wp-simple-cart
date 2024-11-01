<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Info Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: top.php 140016 2009-07-28 06:57:23Z tajima $
 */
$status = $this->model['status'];
$ymd_info = $this->model['ymd_info'];
$order_list = $this->model['order_list'];
$sales_list = $this->model['sales_list'];
$stock_list = $this->model['stock_list'];
?>
<?php //受注状況サマリー ?>
<div class="sc_subtitle_top"><?php _e(sprintf(SCLNG_STORE_INFO_ORDER_SUMARRY, $ymd_info['ymd']), SC_DOMAIN); ?></div>
<div class="sc_tbl_lstfr">
<table>
  <tr>
    <?php foreach ($status as $key=>$val): ?>
    <th class="wd60"><?php echo $val; ?></th>
    <?php endforeach; ?>
  </tr>
  <tr>
    <?php foreach ($status as $key=>$val): ?>
    <?php
        $count = 0;
        if (isset($order_list[$key])) {
            $count = $order_list[$key];
        }

        //商品一覧更新へのリンクを作成
        $yymm = str_replace('-', '',$ymd_info['ymd']);
        $yy = substr($yymm, 0, 4);
        $mm = substr($yymm, 4, 2);
        if ($this->mode==SC_MODE_BP) {
            global $bp;
            $product_list_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_store->slug . '/' . SC_BPID_ORDER . '?action=search_order&s_order_fyy='. $yy .'&s_order_fmm=' . $mm . '&s_order_status=' . $key);
        }
        else {
            $product_list_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_ORDER . '?action=search_order&s_order_fyy=' . $yy . '&s_order_fmm=' . $mm . '&s_order_status=' . $key);
        }
        if ($count > 0) {
            $link_count = $this->link(array('href'=>$product_list_url, 'value'=>$count));
        }
        else {
            $link_count = $count;
        }
    ?>
    <td class="wd60 center"><?php echo $link_count; ?></td>
    <?php endforeach; ?>
  </tr>
</table>
</div>

<?php //月別売上サマリー ?>
<div class="sc_subtitle"><?php _e(SCLNG_STORE_INFO_SALES_SUMARRY, SC_DOMAIN); ?></div>
<div class="sc_tbl_lstfr">
<table>
  <tr>
    <?php foreach ($sales_list as $key=>$val): ?>
    <?php
        $sales_lbl = __(SCLNG_STORE_INFO_SALES_TOTAL, SC_DOMAIN);
        if (isset($ymd_info[$key])) {
            $yymm  = str_replace('-', '',$ymd_info[$key]);
            $year  = __(sprintf(SCLNG_STORE_INFO_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
            $month = __(sprintf(SCLNG_STORE_INFO_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
            $sales_lbl = __(sprintf(SCLNG_STORE_INFO_SALES_YYMM, $year.$month), SC_DOMAIN);
        }
    ?>
    <th class="wd60"><?php echo $sales_lbl; ?></th>
    <?php endforeach; ?>
  </tr>
  <tr>
    <?php foreach ($sales_list as $key=>$val): ?>
    <td class="wd60 center"><?php echo $this->money($val); ?></td>
    <?php endforeach; ?>
  </tr>
</table>
</div>

<?php //在庫切れ商品情報 ?>
<?php if (is_array($stock_list)): ?>
<div class="sc_subtitle"><?php _e(SCLNG_STORE_INFO_STOCK_EMPTY, SC_DOMAIN); ?></div>
<div class="sc_tbl_lstfr">
<table>
  <tr>
    <?php
        $yymm  = str_replace('-', '',$ymd_info['yymm00']);
        $year  = __(sprintf(SCLNG_STORE_INFO_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_STORE_INFO_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm00_lbl = __(sprintf(SCLNG_STORE_INFO_SALES_YYMM_QUANTITY, $year.$month), SC_DOMAIN);

        $yymm  = str_replace('-', '',$ymd_info['yymm01']);
        $year  = __(sprintf(SCLNG_STORE_INFO_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_STORE_INFO_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm01_lbl = __(sprintf(SCLNG_STORE_INFO_SALES_YYMM_QUANTITY, $year.$month), SC_DOMAIN);

        $yymm  = str_replace('-', '',$ymd_info['yymm02']);
        $year  = __(sprintf(SCLNG_STORE_INFO_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_STORE_INFO_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm02_lbl = __(sprintf(SCLNG_STORE_INFO_SALES_YYMM_QUANTITY, $year.$month), SC_DOMAIN);

        $yymm  = str_replace('-', '',$ymd_info['yymm03']);
        $year  = __(sprintf(SCLNG_STORE_INFO_SALES_YEAR, substr($yymm, 0, 4)), SC_DOMAIN);
        $month = __(sprintf(SCLNG_STORE_INFO_SALES_MONTH, substr($yymm, 4, 2)), SC_DOMAIN);
        $stock_yymm03_lbl = __(sprintf(SCLNG_STORE_INFO_SALES_YYMM_QUANTITY, $year.$month), SC_DOMAIN);
    ?>
    <th class="wd200"><?php _e(SCLNG_STORE_ORDER_PRODUCT_NAME, SC_DOMAIN); ?></th>
    <th class="wd100"><?php echo $stock_yymm00_lbl; ?></th>
    <th class="wd100"><?php echo $stock_yymm01_lbl; ?></th>
    <th class="wd100"><?php echo $stock_yymm02_lbl; ?></th>
    <th class="wd100"><?php echo $stock_yymm03_lbl; ?></th>
  </tr>
  <tr>
    <?php foreach ($stock_list as $key=>$product): ?>
    <?php
        //商品情報更新へのリンクを作成
        if ($this->mode==SC_MODE_BP) {
            global $bp;
            $product_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_store->slug . '/' . SC_BPID_PRODUCT . '?top=1&product_id=' . $product['id']);
        }
        else {
            $product_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_PRODUCT . '?top=1&product_id=' . $product['id']);
        }
        $product_name = $this->link(array('href'=>$product_url, 'value'=>$product['product_cd'])) . '<br/>' . $product['name'];
    ?>
    <td class="left"><?php echo $product_name; ?></td>
    <td class="center"><?php echo number_format($product['yymm00']); ?></td>
    <td class="center"><?php echo number_format($product['yymm01']); ?></td>
    <td class="center"><?php echo number_format($product['yymm02']); ?></td>
    <td class="center"><?php echo number_format($product['yymm03']); ?></td>
    <?php endforeach; ?>
  </tr>
</table>
</div>
<?php endif; ?>
