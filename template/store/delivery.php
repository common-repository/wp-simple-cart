<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Delivery Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: delivery.php 140016 2009-07-28 06:57:23Z tajima $
 */
$delivery = $this->model['delivery'];
$results = $this->model['results'];
if (isset($results)) {
    $err_delivery_name = $results->errors['delivery_name'];
    $err_sort = $results->errors['sort'];
}
?>
<!-- 配信先情報登録／更新 -->
<form name="delivery" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_action', 'name'=>'sc_action', 'value'=>'save_delivery')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_id', 'value'=>$delivery['id'])); ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('delivery','');return false;")); ?></p>

<div class="sc_tbl_updt">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_DELIVERY_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'delivery_name', 'value'=>$delivery['name'], 'class'=>'input_200')); ?><?php $this->error($err_delivery_name); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_DELIVERY_ORDER, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'sort', 'value'=>$delivery['sort'], 'class'=>'input_40')); ?><?php $this->error($err_sort); ?></td>
  </tr>
</table>
</div>

<p class="submit">
<?php if(is_null($delivery['id'])||$delivery['id']==''): ?>
<?php echo $this->submit(array('value'=>__(SCLNG_CREATE, SC_DOMAIN) . " &raquo;")); ?>
<?php else: ?>
<?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?>
<?php endif; ?>
</p>
</form>
