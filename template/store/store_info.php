<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store User Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_info.php 140016 2009-07-28 06:57:23Z tajima $
 */
$states = $this->model['states'];
$store = $this->model['store_info'];
?>
<!-- 店舗情報閲覧 -->
<form name="wp_simple_cart" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'sc_action', 'value'=>'update')); ?>

<div class="sc_tbl">
<table>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $store['display_name'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_LOGIN, SC_DOMAIN) ?></th>
    <td><?php echo $store['user_login'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $store['email'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_URL, SC_DOMAIN) ?></th>
    <td><?php echo $store['url'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_ZIP, SC_DOMAIN) ?></th>
    <td><?php echo $store['zip'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $states[$store['state']] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $store['street'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $store['address'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $store['tel'] ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $store['fax'] ?></td>
  </tr>
</table>
</div>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>

<form name="user" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_action', 'name'=>'sc_action', 'value'=>'change_password')); ?>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_STORE_USER_INFO_PASSWORD, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
