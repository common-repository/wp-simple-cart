<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Info View Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_info_update_confirm.php 140016 2009-07-28 06:57:23Z tajima $
 */
$states = $this->model['states'];
$user = $this->model['user'];
?>
<form name="user" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_action', 'name'=>'sc_action', 'value'=>'save_user')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'email', 'value'=>$user['user_email'])); ?>
<?php $this->hvars() ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('user','update_back');return false;")); ?></p>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_USER, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $user['display_name']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $user['user_email']; ?></td>
  </tr>
</table>
</div>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_SEND, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th rowspan="2"><?php _e(SCLNG_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_last_name']; ?> <?php echo $user['send_first_name']; ?></td>
  </tr>
  <tr>
    <td><?php echo $user['send_last_furi']; ?> <?php echo $user['send_first_furi']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ZIP, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_zip']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $states[$user['send_state']]; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_street']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_address']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_tel']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_fax']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_MOBILE, SC_DOMAIN) ?></th>
    <td><?php echo $user['send_mobile']; ?></td>
  </tr>
</table>
</div>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_BILL, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th rowspan="2"><?php _e(SCLNG_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_last_name']; ?> <?php echo $user['bill_first_name']; ?></td>
  </tr>
  <tr>
    <td><?php echo $user['bill_last_furi']; ?> <?php echo $user['bill_first_furi']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ZIP, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_zip']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $states[$user['bill_state']]; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_street']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_address']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_tel']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_fax']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_MOBILE, SC_DOMAIN) ?></th>
    <td><?php echo $user['bill_mobile']; ?></td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
