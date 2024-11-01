<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Base Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: base.php 140016 2009-07-28 06:57:23Z tajima $
 */
$sc_options = $this->model['sc_options'];
$sc_categorys = $sc_options['sc_categorys'];
$sc_buddypress = $sc_options['sc_buddypress'];
$sc_timeout = $sc_options['sc_timeout'];
$sc_new = $sc_options['sc_new'];
$exists_bp = $this->model['exists_bp'];
$results = $this->model['results'];

//ƒGƒ‰[”»’è
if (isset($results)) {
    $err_timeout = $results->errors['sc_timeout'];
    $err_new = $results->errors['sc_new'];
}
?>
<h3><?php _e(SCLNG_ADMIN_BASE_SETTING, SC_DOMAIN) ?></h3>
<form name="wp_simple_cart" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
  <input type="hidden" id="action" name="sc_action" value="setting" />
  <table>
    <tr>
      <td colspan=2>
        <?php if($exists_bp==true): ?>
        <?php _e(SCLNG_ADMIN_BASE_FOUND_BP, SC_DOMAIN); ?>
        <?php else: ?>
        <?php _e(SCLNG_ADMIN_BASE_NOT_FOUND_BP, SC_DOMAIN); ?>
        <?php endif; ?>
      </td>
    </tr>
    <tr><td style="height:10px;"></td></tr>
    <tr>
      <td valign="top"><?php _e(SCLNG_ADMIN_BASE_USE_BP_MYPAGE, SC_DOMAIN) ?></td>
      <td>
        <?php
            $radio1 = array('type'=>'radio', 'name'=>'sc_buddypress', 'value'=>'1');
            $radio2 = array('type'=>'radio', 'name'=>'sc_buddypress', 'value'=>'0');
            if ($sc_buddypress=='1') {
                $radio1['checked'] = 'checked';
            }
            else if ($sc_buddypress=='0') {
                $radio2['checked'] = 'checked';
            }
            if ($exists_bp==false){
                $radio1['disabled'] = 'disabled';
                unset($radio1['checked']);
                $radio2['checked'] = 'checked';
            }
        ?>
        <?php echo $this->input($radio1); ?> <?php _e(SCLNG_ADMIN_BASE_YES, SC_DOMAIN); ?>
        <?php echo $this->input($radio2); ?> <?php _e(SCLNG_ADMIN_BASE_NO, SC_DOMAIN); ?>
      </td>
    </tr>
    <tr><td style="height:10px;"></td></tr>
    <tr>
      <td valign="top"><?php _e(SCLNG_ADMIN_BASE_CATEGORY, SC_DOMAIN) ?></td>
      <td>
        <table>
        <?php
        $cates = SimpleCartFunctions::getAllCategory();
        foreach ($cates as $key=>$cate) {
            if ($cate->parent==0) {
                echo '<tr>';
                echo '<td nowrap><input type="checkbox" id="sc_categorys_' . $cate->term_id . '" name="sc_categorys[' . $cate->term_id . ']" value="' . $cate->term_id . '" ' . ((isset($sc_categorys[$cate->term_id]))?'checked="checked"':'') . ' /></td>';
                echo '<td nowrap>' . $cate->name . "</td>";
                echo '</tr>';
            }
        }
        ?>
        </table>
      </td>
      <td valign="top"></td>
    </tr>
    <tr><td style="height:10px;"></td></tr>
    <tr>
      <td valign="top"><?php _e(SCLNG_ADMIN_BASE_CART_TIMEOUT, SC_DOMAIN) ?></td>
      <td><?php echo $this->input(array('name'=>'sc_timeout', 'value'=>$sc_timeout)); ?> <?php _e(SCLNG_ADMIN_BASE_MINUTES, SC_DOMAIN); ?><?php $this->error($err_timeout); ?></td>
      <td valign="top"></td>
    </tr>
    <tr><td style="height:10px;"></td></tr>
    <tr>
      <td valign="top"><?php _e(SCLNG_ADMIN_BASE_NEW, SC_DOMAIN) ?></td>
      <td><?php echo $this->input(array('name'=>'sc_new', 'value'=>$sc_new)); ?> <?php _e(SCLNG_ADMIN_BASE_DAY, SC_DOMAIN); ?><?php $this->error($err_new); ?></td>
      <td valign="top"></td>
    </tr>
  </table>
  <p class="submit"><input type="submit" value="Update Option &raquo;" /></p>
</form>

<hr/>

<strong><?php _e(SCLNG_ADMIN_BASE_PRODUCT_DIR_CHECK, SC_DOMAIN) ?></strong><br/><br/>
<?php
    $product_image_dir = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files';
    echo $product_image_dir . '<br/>';
    if (is_writable($product_image_dir)) {
        echo "<font color=\"#0000ff\"><strong>" . __(SCLNG_ADMIN_BASE_WRITABLE, SC_DOMAIN) . "</strong></font>";
    }
    else {
        echo "<font color=\"#ff0000\"><strong>" . __(SCLNG_ADMIN_BASE_NOT_WRITABLE, SC_DOMAIN) . "</strong></font>";
    }
