<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Paid Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: paid_method.php 140016 2009-07-28 06:57:23Z tajima $
 */
$paid = $this->model['paid'];
$results = $this->model['results'];
if (isset($results)) {
    $err_paid_name = $results->errors['paid_name'];
    $err_commission = $results->errors['commission'];
    $err_sort = $results->errors['sort'];
}
?>
<!-- 支払方法登録／更新 -->
<form name="paid" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'paid_action', 'name'=>'sc_action', 'value'=>'save_paid')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'paid_id', 'value'=>$paid['id'])); ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('paid','');return false;")); ?></p>

<div class="sc_tbl_updt">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PAID_METHOD_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'paid_name', 'value'=>$paid['name'], 'class'=>'input_200')); ?><?php $this->error($err_paid_name); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PAID_METHOD_COMMISSION, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'commission', 'value'=>$paid['commission'], 'class'=>'input_120')); ?><?php $this->error($err_commission); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PAID_METHOD_ORDER, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'sort', 'value'=>$paid['sort'], 'class'=>'input_40')); ?><?php $this->error($err_sort); ?></td>
  </tr>
</table>
</div>

<p class="submit">
<?php if(is_null($paid['id'])||$paid['id']==''): ?>
<?php echo $this->submit(array('value'=>__(SCLNG_CREATE, SC_DOMAIN) . " &raquo;")); ?>
<?php else: ?>
<?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?>
<?php endif; ?>
</p>
</form>
