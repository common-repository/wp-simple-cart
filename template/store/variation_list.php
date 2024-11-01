<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Variation Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: variation_list.php 140016 2009-07-28 06:57:23Z tajima $
 */
$variation_list = $this->model['variation_list'];
?>
<!-- 新規規格追加 -->
<form name="new_variation" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'new_variation_action', 'name'=>'sc_action', 'value'=>'update_variation')); ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_NEW_VARIATION, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:submitForm('new_variation');return false;")); ?></p>
</form>

<!-- 規格一覧 -->
<?php if (is_array($variation_list)): ?>
<form name="variation_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'variation_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'variation_id', 'value'=>'')); ?>

<span><?php echo $this->submit(array('value'=>__(SCLNG_DELETE, SC_DOMAIN), 'onclick'=>"Javascript:if(!confirm('". __(SCLNG_CONFIRM_DELETE_VARIATION, SC_DOMAIN) . "')){return false};actionForm('variation_list', 'delete_variation');")); ?></span>
<div class="sc_tbl_lstfr wd300">
<table class="wd300">
<tr>
  <th><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_VARIATION_NAME, SC_DOMAIN); ?></th>
</tr>
<?php foreach ($variation_list as $variation): ?>
<tr>
  <td class="center wd40"><?php echo $this->input(array('type'=>'checkbox', 'id'=>'variations_'.$variation['id'], 'name'=>'variations['.$variation['id'].']', 'value'=>$variation['id'])); ?></td>
  <td><?php echo $this->link(array('href'=>$_SERVER['REQUEST_URI'], 'value'=>$variation['name'], 'onclick'=>"Javascript:document.getElementById('variation_id').value='".$variation['id']."';submitForm('variation_list', 'update_variation');return false;")); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</form>
<?php else: ?>
<?php echo SCLNG_NOT_FOUND ?>
<?php endif; ?>
