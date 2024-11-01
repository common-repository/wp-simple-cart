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
 * @version     svn:$Id: delivery_list.php 140016 2009-07-28 06:57:23Z tajima $
 */
$delivery_list = $this->model['delivery_list'];
$delivery_free = $this->model['delivery_free'];
$results = $this->model['results'];
if (isset($results)) {
    if (isset($results->errors['price_limit'])) {
        $err_price_limit = $results->errors['price_limit'];
    }
}
?>
<!-- 新規配送先追加 -->
<form name="new_delivery" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'new_delivery_action', 'name'=>'sc_action', 'value'=>'update_delivery')); ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_NEW_DELIVERY, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:submitForm('new_delivery');return false;")); ?></p>
</form>

<!-- 配送先一覧 -->
<?php if (is_array($delivery_list)): ?>
<form name="delivery_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_id', 'value'=>'')); ?>

<span><?php echo $this->submit(array('value'=>__(SCLNG_DELETE, SC_DOMAIN), 'onclick'=>"Javascript:if(!confirm('". __(SCLNG_CONFIRM_DELETE_DELIVERY, SC_DOMAIN) . "')){return false};actionForm('delivery_list', 'delete_delivery');")); ?></span>
<span><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE_ORDER, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:actionForm('delivery_list', 'delivery_order');")); ?></span>
<div class="sc_tbl_lstfr wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_DELIVERY_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_DELIVERY_FEE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_DELIVERY_ORDER, SC_DOMAIN); ?></th>
</tr>
<?php foreach ($delivery_list as $delivery): ?>
<?php
    $err_sort = '';
    if (isset($results)) {
        if (isset($results->errors['sort'][$delivery['id']])) {
            $err_sort = $results->errors['sort'][$delivery['id']];
        }
    }
    $link1 = $this->link(array('href'=>$_SERVER['REQUEST_URI'], 'value'=>$delivery['name'], 'onclick'=>"Javascript:document.getElementById('delivery_id').value='".$delivery['id']."';submitForm('delivery_list', 'update_delivery');return false;"));
    $link2 = $this->link(array('href'=>$_SERVER['REQUEST_URI'], 'value'=>__(SCLNG_STORE_EDIT_FEE, SC_DOMAIN), 'onclick'=>"Javascript:document.getElementById('delivery_id').value='".$delivery['id']."';submitForm('delivery_list', 'delivery_values_list');return false;"));
?>
<tr>
  <td class="center wd40"><?php echo $this->input(array('type'=>'checkbox', 'id'=>'deliverys_'.$delivery['id'], 'name'=>'deliverys['.$delivery['id'].']', 'value'=>$delivery['id'])); ?></td>
  <td><?php echo $link1; ?></td>
  <td><?php echo $link2; ?></td>
  <td><?php echo $this->input(array('id'=>'sort_'.$delivery['id'], 'name'=>'sort['.$delivery['id'].']', 'value'=>$delivery['sort'], 'class'=>'input_40')); ?><?php $this->error($err_sort); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</form>
<?php else: ?>
<?php echo SCLNG_NOT_FOUND ?>
<?php endif; ?>

<div><?php _e(SCLNG_STORE_DELIVERY_OTHER_OPTION, SC_DOMAIN) ?></div>
<form name="delivery_option" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_option_action', 'name'=>'sc_action', 'value'=>'save_delivery_option')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_free_id', 'value'=>$delivery_free['id'], 'class'=>'input_120')); ?>
<?php echo $this->input(array('id'=>'price_limit', 'value'=>$delivery_free['price_limit'], 'class'=>'input_120')); ?><?php _e(SCLNG_STORE_DELIVERY_FEE_LIMIT, SC_DOMAIN); ?><?php $this->error($err_price_limit); ?>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
