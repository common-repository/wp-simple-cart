<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Info Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_info_password.php 140016 2009-07-28 06:57:23Z tajima $
 */
$results = $this->model['results'];
if (isset($results)) {
    $err_pass1 = $results->errors['pass1'];
    $err_pass2 = $results->errors['pass2'];
}
?>
<form name="user" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_action', 'name'=>'sc_action', 'value'=>'save_password')); ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('user','');return false;")); ?></p>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_USER, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_PASS, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('type'=>'password', 'id'=>'pass1', 'value'=>$user['pass1'], 'class'=>'input_120')); ?><?php $this->error($err_pass1); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_PASS_CONFIRM, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('type'=>'password', 'id'=>'pass2', 'value'=>$user['pass2'], 'class'=>'input_120')); ?><?php $this->error($err_pass2); ?></td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
