<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: bp-simplecart-user.php 140016 2009-07-28 06:57:23Z tajima $
 */


define('BP_SIMPLECART_USER_IS_INSTALLED', 1);
define('BP_SIMPLECART_USER_VERSION', '1.0');
define('BP_SIMPLECART_USER_DB_VERSION', '1');

if (!defined('BP_SIMPLECART_USER_SLUG')) {
    define('BP_SIMPLECART_USER_SLUG', 'simplecart-user');
}

/**
 * bp_simplecart_user_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_simplecart_user_setup_globals() {
    global $bp;

    $bp->simplecart_user->image_base = BP_PLUGIN_SIMPLECART . '/images';
    $bp->simplecart_user->slug = BP_SIMPLECART_USER_SLUG;

    $bp->version_numbers->simplecart_user = BP_SIMPLECART_USER_VERSION;
}
add_action('plugins_loaded', 'bp_simplecart_user_setup_globals', 5);
add_action('admin_menu', 'bp_simplecart_user_setup_globals', 1);

add_action('wp', array('SimpleCartFunctions', 'userBuddyPressMenu'), 1);
add_action('admin_menu', array('SimpleCartFunctions', 'userBuddyPressMenu'), 1);
