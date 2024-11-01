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
 * @version     svn:$Id: delivery_values_list.php 140016 2009-07-28 06:57:23Z tajima $
 */
$delivery = $this->model['delivery'];
$delivery_values_list = $this->model['delivery_values_list'];
$results = $this->model['results'];
?>
<h4><?php _e(SCLNG_STORE_DELIVERY_NAME, SC_DOMAIN); ?> : <?php echo $delivery['name'] ?></h4>

<form name="new_delivery_values" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'new_delivery_values_action', 'name'=>'sc_action', 'value'=>'update_delivery_values')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_id', 'value'=>$delivery['id'])); ?>
<p class="submit">
<?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('new_delivery_values','');return false;")); ?>
<?php //echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_NEW_DELIVERY_VALUES, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('new_delivery_values','update_delivery_values');")); ?>
</p>
</form>

<form name="delivery_values_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_values_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_id', 'value'=>$delivery['id'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'delivery_values_id', 'value'=>'')); ?>

<script language="Javascript">
function input_delivery_fees(obj) {
    var elements = document.getElementsByName('delivery_fees');
    var fee = document.getElementById('all_fees').value;
    for (var i=0; i<elements.length; i++) {
        document.getElementById('delivery_fee_' + elements[i].value).value = fee;
    }
}
</script>

<!-- 配送料金表 -->
<!--<span><?php echo $this->submit(array('value'=>__(SCLNG_DELETE, SC_DOMAIN), 'onclick'=>"Javascript:if(!confirm('" . __('Really Delivery Values Deleted?', SC_DOMAIN) . "')){return false};actionForm(SCLNG_CONFIRM_DELETE_DELIVERY_VALUES, 'delete_delivery_values');")); ?></span>-->
<span><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:actionForm('delivery_values_list', 'delivery_values_fee');")); ?></span>
<span><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE_ORDER, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:actionForm('delivery_values_list', 'delivery_values_order');")); ?></span>
<br/>
<span>
<?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_FEE_BATCH_ENTRY, SC_DOMAIN), 'onclick'=>'Javascript:input_delivery_fees(this);return false;')); ?>
<?php echo $this->input(array('id'=>'all_fees', 'value'=>'', 'class'=>'input_120')); ?>
</span>

<div class="sc_tbl_lstfr wd400">
<table>
<tr>
  <!--<th><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>-->
  <th><?php _e(SCLNG_STORE_DELIVERY_VALUES_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_DELIVERY_FEE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_DELIVERY_VALUES_ORDER, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($delivery_values_list)): ?>
<?php foreach ($delivery_values_list as $values): ?>
<?php
    $err_delivery_fee = '';
    $err_sort = '';
    if (isset($results)) {
        if (isset($results->errors['delivery_fee'][$values['id']])) {
            $err_delivery_fee = $results->errors['delivery_fee'][$values['id']];
        }
        if (isset($results->errors['sort'][$values['id']])) {
            $err_sort = $results->errors['sort'][$values['id']];
        }
    }
?>
<tr>
  <!--<td class="center"><?php echo $this->input(array('type'=>'checkbox', 'id'=>'delivery_values_'.$values['id'], 'name'=>'delivery_values['.$values['id'].']', 'value'=>$values['id'])); ?></td>-->
  <td>
      <?php //echo $this->link(array('href'=>$_SERVER['REQUEST_URI'], 'value'=>$values['name'], 'onclick'=>"Javascript:document.getElementById('delivery_values_id').value='".$values['id']."';submitForm('delivery_values_list', 'update_delivery_values');return false;")); ?>
      <?php echo $values['name']; ?>
      <?php echo $this->input(array('type'=>'hidden', 'name'=>'delivery_fees', 'value'=>$values['id'])); ?>
  </td>
  <td class="wd120"><?php echo $this->input(array('id'=>'delivery_fee_'.$values['id'], 'name'=>'delivery_fee['.$values['id'].']', 'value'=>$values['delivery_fee'], 'class'=>'input_120')); ?><?php $this->error($err_delivery_fee); ?></td>
  <td class="wd40"><?php echo $this->input(array('id'=>'sort_'.$values['id'], 'name'=>'sort['.$values['id'].']', 'value'=>$values['sort'], 'class'=>'input_40')); ?><?php $this->error($err_sort); ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</form>
