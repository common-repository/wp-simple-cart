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
 * @version     svn:$Id: store_info_update_confirm.php 140016 2009-07-28 06:57:23Z tajima $
 */
$states = $this->model['states'];
?>
<form name="store" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'store_action', 'name'=>'sc_action', 'value'=>'completed')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'ID', 'value'=>$this->request->getParam('ID'))); ?>
<?php $this->hvars() ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('store','update_back');return false;")); ?></p>

<div class="sc_tbl_updt">
<table>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('display_name'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_LOGIN, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('user_login'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('email'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_URL, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('url'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_ZIP, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('zip'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $states[$this->request->getParam('state')]; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('street'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('address'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('tel'); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->request->getParam('fax'); ?></td>
  </tr>
</table>
</div>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
