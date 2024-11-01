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
 * @version     svn:$Id: paid_method_list.php 140016 2009-07-28 06:57:23Z tajima $
 */
$paid_list = $this->model['paid_list'];
$results = $this->model['results'];
?>
<form name="new_paid" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'new_paid_action', 'name'=>'sc_action', 'value'=>'update_paid')); ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_NEW_PAID_METHOD, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:submitForm('new_paid');return false;")); ?></p>
</form>

<!-- 支払方法一覧 -->
<?php if (is_array($paid_list)): ?>
<form name="paid_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'paid_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'paid_id', 'value'=>'')); ?>

<span><?php echo $this->submit(array('value'=>__(SCLNG_DELETE, SC_DOMAIN), 'onclick'=>"Javascript:if(!confirm('" . __(SCLNG_CONFIRM_DELETE_PAID_METHOD, SC_DOMAIN) . "')){return false};actionForm('paid_list', 'delete_paid');")); ?></span>
<span><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE_ORDER, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:actionForm('paid_list', 'paid_order');")); ?></span>
<div class="sc_tbl_lstfr wd400">
<table>
<tr>
  <th><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PAID_METHOD_NAME, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PAID_METHOD_COMMISSION, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PAID_METHOD_ORDER, SC_DOMAIN); ?></th>
</tr>
<?php foreach ($paid_list as $paid): ?>
<?php
    $err_sort = '';
    if (isset($results)) {
        if (isset($results->errors['sort'][$paid['id']])) {
            $err_sort = $results->errors['sort'][$paid['id']];
        }
    }
?>
<tr>
  <td class="center wd40"><?php echo $this->input(array('type'=>'checkbox', 'id'=>'paids_'.$paid['id'], 'name'=>'paids['.$paid['id'].']', 'value'=>$paid['id'])); ?></td>
  <td><?php echo $this->link(array('href'=>$_SERVER['REQUEST_URI'], 'value'=>$paid['name'], 'onclick'=>"Javascript:document.getElementById('paid_id').value='".$paid['id']."';submitForm('paid_list', 'update_paid');return false;")); ?></td>
  <td class="right"><?php echo $this->money($paid['commission']); ?></td>
  <td><?php echo $this->input(array('id'=>'sort_'.$paid['id'], 'name'=>'sort['.$paid['id'].']', 'value'=>$paid['sort'], 'class'=>'input_40')); ?><?php $this->error($err_sort); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</form>
<?php else: ?>
<?php echo SCLNG_NOT_FOUND ?>
<?php endif; ?>
