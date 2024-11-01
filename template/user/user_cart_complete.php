<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Cart Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_cart_complete.php 140016 2009-07-28 06:57:23Z tajima $
 */
$status = $this->model['results'];
?>
<?php //ナビゲーション ?>
<div class="sc_navigations">
  <div class="sc_navi"><?php _e(SCLNG_NAVI_ADDRESS, SC_DOMAIN); ?></div>
  <div class="sc_navi_arrow"></div>
  <div class="sc_navi"><?php _e(SCLNG_NAVI_CONFIRM, SC_DOMAIN); ?></div>
  <div class="sc_navi_arrow"></div>
  <div class="sc_navi_act"><?php _e(SCLNG_NAVI_COMPLETE, SC_DOMAIN); ?></div>
</div>

<?php //注文受付メッセージ ?>
<div class="sc_order_complete">
<table>
  <tr>
    <td>
    <div class="sc_thanks"><?php _e(SCLNG_USER_CART_THANKS_TITLE, SC_DOMAIN); ?></div>
    <?php foreach($status as $s): ?>
    <div class="sc_order_lst"><?php _e(SCLNG_USER_CART_THANKS_ORDER_NO, SC_DOMAIN); ?>:<?php echo SimpleCartFunctions::LPAD($s['order_id'], 8) ?>(<?php echo $s['store_name'] ?>)</div>
    <?php endforeach; ?>
    <div class="sc_order_message">
    <?php _e(SCLNG_USER_CART_THANKS_MESSAGE_1, SC_DOMAIN); ?><br/>
    <?php _e(SCLNG_USER_CART_THANKS_MESSAGE_2, SC_DOMAIN); ?><br/>
    <?php _e(SCLNG_USER_CART_THANKS_MESSAGE_3, SC_DOMAIN); ?><br/>
    <?php _e(SCLNG_USER_CART_THANKS_MESSAGE_4, SC_DOMAIN); ?><br/>
    <?php _e(SCLNG_USER_CART_THANKS_MESSAGE_5, SC_DOMAIN); ?><br/>
    </div>
    </td>
  </tr>
</table>
</div>
