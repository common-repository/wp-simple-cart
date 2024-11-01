<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Tax Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: tax.php 140016 2009-07-28 06:57:23Z tajima $
 */
$tax_method = $this->model['tax_method'];
$tax = $this->model['tax'];
$results = $this->model['results'];
if (isset($results)) {
    $err_tax = $results->errors['tax'];
}
?>
<form name="tax" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'tax_action', 'name'=>'sc_action', 'value'=>'save_tax')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'tax_id', 'value'=>$tax['id'])); ?>

<div class="sc_tbl_updt">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_TAX_RATE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'tax', 'value'=>$tax['tax'], 'class'=>'input_40')); ?><?php $this->error($err_tax); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_TAX_METHOD, SC_DOMAIN) ?></th>
    <td>
    <?php
        reset($tax_method);
        echo $this->select(array('id'=>'method'), array('list'=>$tax_method,'default'=>$tax['method']));
    ?>
    </td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
