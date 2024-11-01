<?php
/*
Plugin Name: WP Simple Cart Widget
Plugin URI: http://eos.exbridge.jp/projects/show/wp-simple-cart
Description: This widget for Simple Shopping Cart Plugin
Author: Exbridge,inc.
Version: 1.0.15
Author URI: http://exbridge.jp
*/
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

/*
 * This file is part of wp-smple-cart widget.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * カート用ウィジェット
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     wp-simple-cart
 * @version     svn:$Id: wp-simple-cart-widget.php 162120 2009-10-10 13:28:30Z tajima $
 */
function widget_simplecart($args, $widget_args=1) {
    global $current_user, $sc_mode, $bp;

    $u = new WP_User($current_user->ID);

    extract($args, EXTR_SKIP);
    if (is_numeric($widget_args)) {
        $widget_args = array('number'=>$widget_args);
    }
    $widget_args = wp_parse_args($widget_args, array('number'=>-1));
    extract($widget_args, EXTR_SKIP);

    $options = get_option('sc_widget_options');
    if (!isset($options[$number])) {
        return;
    }

    $sign_in_url  = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-signin.php';
    $sign_out_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-signout.php';
    $member_entry = site_url(SCLNG_PAGE_PUBLIC_MEMBER_ENTRY);
    $public_top   = site_url(SCLNG_PAGE_PUBLIC_TOP);
    $store_info   = site_url(SCLNG_PAGE_STORE_INFO);
    if ($sc_mode==SC_MODE_BP) {
        $cart_info = site_url('members/' . $current_user->user_login . '/' . $bp->simplecart_user->slug . '/' . SC_BPID_CART);
    }
    else {
        $cart_info = site_url(SCLNG_PAGE_USER_INFO . '/' . SCLNG_PAGE_USER_CART);
    }
?>

<style type="text/css">
.sc_widget {
    border: 2px solid #0080c0;
    padding: 2px;
    -moz-border-radius-bottomleft: 8px;
    -moz-border-radius-bottomright: 8px;
    -moz-border-radius-topleft: 8px;
    -moz-border-radius-topright: 8px;
    -moz-box-sizing: content-box;
}
.sc_widget_title {
    background-color: #eeeeee;
    font-weight: bold;
    padding: 3px;
    width: 140px;
    margin:3px;
    border: 1px solid #0080c0;
}
.sc_widget_login {
    width: 90px;
    height: 14px;
    margin: 0px 0px 3px 3px;
}
.sc_widget_label {
    margin: 0px 0px 0px 3px;
}
.sc_widget_button {
    cursor: pointer;
    margin: 0px 0px 6px 3px;
}
.sc_widget_hello {
    margin: 3px 0px 3px 3px;
}
.sc_widget_cart_info {
    text-decoration: underline;
    margin: 3px 0px 3px 3px;
}
.sc_widget_cart {
    margin: 3px 0px 3px 8px;
}
.sc_widget_message {
    color: #ff0000;
    margin: 0px 0px 3px 8px;
}
</style>
<?php echo $before_widget; ?>
<div class="sc_widget">
<div class="sc_widget_title"><?php //echo $before_title; ?><?php _e(SCLNG_WIDGET_TITLE, SC_DOMAIN); ?><?php //echo $after_title; ?></div>
<div>
  <script type="text/javascript">
    function sc_login() {
        if (jQuery("#sc_log").val()=='' && jQuery("#sc_pwd").val()=='') {
            document.getElementById('err_message').innerHTML = '<?php echo _e(SCLNG_CHECK_EMPTY_LOGIN, SC_DOMAIN) ?>';
            return false;
        }
        jQuery.post('<?php echo $sign_in_url ?>', {
                'log': jQuery("#sc_log").val(),
                'pwd': jQuery("#sc_pwd").val()
            },
            function(res) {
                if (res['result']==true) {
                    window.location.reload(true);
                }
                else {
                    if (res['message']['empty_username']!=undefined) {
                        document.getElementById('err_message').innerHTML = res['message']['empty_username'];
                    }
                    if (res['message']['empty_password']!=undefined) {
                        document.getElementById('err_message').innerHTML = res['message']['empty_password'];
                    }
                    if (res['message']['invalid_username']!=undefined) {
                        document.getElementById('err_message').innerHTML = res['message']['invalid_username'];
                    }
                    if (res['message']['incorrect_password']!=undefined) {
                        document.getElementById('err_message').innerHTML = res['message']['incorrect_password'];
                    }
                    if (res['message']['authentication_failed']!=undefined) {
                        document.getElementById('err_message').innerHTML = res['message']['authentication_failed'];
                    }
                }
            }, "json");
    }
    function sc_logout() {
        jQuery.post('<?php echo $sign_out_url ?>', {},
            function(response) {
                window.location.href = '<?php echo site_url() ?>';
            });
    }
  </script>
  <?php //ユーザー情報表示 ?>
  <?php if($current_user->ID>0): ?>
    <div class="sc_widget_hello"><?php echo sprintf(__(SCLNG_WIDGET_HELLO, SC_DOMAIN), $current_user->display_name); ?></div>
    <?php //カート情報表示 ?>
    <div class="sc_widget_cart_info"><?php _e(SCLNG_WIDGET_CART, SC_DOMAIN) ?></div>
    <div class="sc_widget_cart"><?php _e(SCLNG_WIDGET_QUANTITY, SC_DOMAIN) ?>:<?php echo sc_get_sku(); ?></div>
    <div class="sc_widget_cart"><?php _e(SCLNG_WIDGET_TOTAL, SC_DOMAIN) ?>:<?php echo sc_get_total_cost(); ?></div>
  <?php else: ?>
    <div class="sc_widget_label"><?php _e(SCLNG_WIDGET_LOGIN_ID, SC_COMAIN); ?></div>
    <div><input type="text" id='sc_log' name="log" class="sc_widget_login"></div>
    <div class="sc_widget_label"><?php _e(SCLNG_WIDGET_LOGIN_PASSWORD, SC_COMAIN); ?></div>
    <div><input type="password" id='sc_pwd' name="pwd" class="sc_widget_login"></div>
    <div id="err_message" class="sc_widget_message"></div>
    <div><button onclick="Javascript:sc_login();return false;" class="sc_widget_button"><?php _e(SCLNG_WIDGET_LOGIN, SC_DOMAIN) ?></button></div>
  <?php endif; ?>

  <?php //関連リンク ?>
  <ul>
    <?php //新規会員登録 ?>
    <?php if($current_user->ID==0): ?>
    <?php if ($sc_mode!=SC_MODE_BP): ?>
    <li><a href="<?php echo $member_entry ?>"><?php _e(SCLNG_WIDGET_NEW_MEMBER, SC_DOMAIN) ?></a></li>
    <?php endif; ?>
    <?php endif; ?>

    <?php //商品情報へ ?>
    <li><a href="<?php echo $public_top; ?>"><?php _e(SCLNG_WIDGET_GOTO_PUBLIC, SC_DOMAIN) ?></a></li>

    <?php if($current_user->ID>0): ?>
    <?php //カートへ ?>
    <li><a href="<?php echo $cart_info; ?>"><?php _e(SCLNG_WIDGET_GOTO_CART, SC_DOMAIN) ?></a></li>
    <?php if ($sc_mode!=SC_MODE_BP): ?>
    <?php if ($u->has_cap(SC_CAP)): ?>
    <?php //店舗管理へ ?>
    <li><a href="<?php echo $store_info; ?>"><?php _e(SCLNG_WIDGET_GOTO_STORE, SC_DOMAIN) ?></a></li>
    <?php endif; ?>
    <?php endif; ?>
    <li><a href="#" onclick="Javascript:sc_logout();return false;"><?php _e(SCLNG_WIDGET_LOGOUT, SC_DOMAIN) ?></a></li>
    <?php endif; ?>
  </ul>
</div>
</div>
<?php echo $after_widget; ?>
<?php
}

/**
 * ダッシュボード：ウィジェット管理
 *
 */
function widget_simplecart_control($widget_args) {
    global $wp_registered_widgets;
    static $updated = false;

    if (is_numeric($widget_args)) {
        $widget_args = array('number'=>$widget_args);
    }
    $widget_args = wp_parse_args($widget_args, array('number'=>-1));
    extract($widget_args, EXTR_SKIP);

    $options = get_option('sc_widget_options');
    if (!is_array($options)) {
        $options = array();
    }

    if (!$updated && !empty($_POST['sidebar'])) {
        $sidebar = (string) $_POST['sidebar'];

        $sidebars_widgets = wp_get_sidebars_widgets();
        if (isset($sidebars_widgets[$sidebar])) {
            $this_sidebar =& $sidebars_widgets[$sidebar];
        }
        else {
            $this_sidebar = array();
        }

        foreach ($this_sidebar as $_widget_id) {
            if ('widget_simplecart'==$wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
                $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                unset($options[$widget_number]);
            }
        }

        foreach ((array)$_POST['widget-simplecart'] as $widget_number=>$widget_text) {
            $options[$widget_number] = compact('name');
        }
        update_option('sc_widget_options', $options);
        $updated = true;
    }
    if (-1 == $number) {
        $name = '';
        $number = '%i%';
    }
    else {
        $name = attribute_escape($options[$number]['name']);
    }
?>
<p>
<input type="text" id="simplecart-name-<?php echo $number; ?>" name="widget-simplecart[<?php echo $number; ?>][name]" type="text" value="<?php echo $number; ?>" />
<input type="text" id="simplecart-submit-<?php echo $number; ?>" name="simplecart-submit-<?php echo $number; ?>" value="1" />
<?php _e(SCLNG_WIDGET_DESCRIPTION, SC_DOMAIN); ?>
</p>
<?php
}

function widget_simplecart_register() {
    // Check for the required API functions
    if (!function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control')) {
        return;
    }

    if (!$options=get_option('sc_widget_options')) {
        $options = array();
    }

    $widget_ops = array('classname'=>'widget_simplecart', 'description' => __(SCLNG_WIDGET_DESCRIPTION, SC_DOMAIN));
    $control_ops = array('width' => 460, 'height' => 250, 'id_base' => 'simplecart');
    $name = __(SCLNG_WIDGET_NAME, SC_COMAIN);

    $id = false;
    foreach (array_keys($options) as $o) {
        // Old widgets can have null values for some reason
        $id = "simplecart-$o"; // Never never never translate an id

        $classname = $options[$o]['name'];
        $widget_ops['classname'] = 'widget_simplecart';
        if (strlen($classname) > 0) {
            $widget_ops['classname'] = 'widget_simplecart_' . $classname;
        }
        wp_register_sidebar_widget($id, $name, 'widget_simplecart', $widget_ops, array('number'=>$o));
        wp_register_widget_control($id, $name, 'widget_simplecart_control', $control_ops, array('number'=>$o));
    }

    // If there are none, we register the widget's existance with a generic template
    if (!$id) {
        wp_register_sidebar_widget('simplecart-1', $name, 'widget_simplecart', $widget_ops, array('number'=>-1));
        wp_register_widget_control('simplecart-1', $name, 'widget_simplecart_control', $control_ops, array('number'=>-1));
    }
}
add_action('widgets_init', 'widget_simplecart_register');
