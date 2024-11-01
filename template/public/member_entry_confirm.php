<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Member Entry Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: member_entry_confirm.php 140016 2009-07-28 06:57:23Z tajima $
 */
$user = $this->model['user'];
?>
<form name="user" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_action', 'name'=>'sc_action', 'value'=>'save_user')); ?>
<?php $this->hvars() ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('user','entry_back');return false;")); ?></p>

<div class="sc_subtitle_updt"><?php _e(SCLNG_PUBLIC_MEMBER_HEADER, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <?php //ログインID ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_MEMBER_ID, SC_DOMAIN) ?></th>
    <td><?php echo $user['user_login']; ?></td>
  </tr>
  <?php //メールアドレス ?>
  <tr>
    <th><?php _e(SCLNG_PUBLIC_MEMBER_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $user['email']; ?></td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_CREATE, SC_DOMAIN) . " &raquo;")); ?></p>

</form>
