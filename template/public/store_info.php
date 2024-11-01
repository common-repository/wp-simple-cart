<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Info Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_info.php 140016 2009-07-28 06:57:23Z tajima $
 */
$store = $this->model['store'];
?>

<p><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:window.history.back();return false;")); ?></p>

<div class="sc_tbl">
<table>
  <?php //店舗名称 ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $store['display_name'] ?></td>
  </tr>

  <?php //メールアドレス ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $store['user_email'] ?></td>
  </tr>

  <?php //URL ?>
  <?php if($store['url']!='' && $store['url']=='http://'): ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_URL, SC_DOMAIN) ?></th>
    <td><?php echo $store['url'] ?></td>
  </tr>
  <?php endif; ?>

  <?php //郵便番号 ?>
  <?php if($store['zip']!=''): ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_ZIP, SC_DOMAIN) ?></th>
    <td><?php echo $store['zip'] ?></td>
  </tr>
  <?php endif; ?>

  <?php //住所 ?>
  <?php if($store['street']!='' || $store['address']!=''): ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $states[$store['state']] ?> <?php echo $states[$store['street']] ?> <?php echo $states[$store['address']] ?></td>
  </tr>
  <?php endif; ?>

  <?php //電話番号 ?>
  <?php if($store['tel']!=''): ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $store['tel'] ?></td>
  </tr>
  <?php endif; ?>

  <?php //FAX番号 ?>
  <?php if($store['fax']!=''): ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_STORE_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $store['fax'] ?></td>
  </tr>
  <?php endif; ?>
</table>
</div>
