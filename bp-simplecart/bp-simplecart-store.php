<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Manage
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: bp-simplecart-store.php 140016 2009-07-28 06:57:23Z tajima $
 */

define('BP_SIMPLECART_STORE_IS_INSTALLED', 1);
define('BP_SIMPLECART_STORE_VERSION', '1.0');
define('BP_SIMPLECART_STORE_DB_VERSION', '1');

if (!defined('BP_SIMPLECART_STORE_SLUG')) {
    define('BP_SIMPLECART_STORE_SLUG', 'simplecart-store');
}

/**
 * bp_simplecart_store_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_simplecart_store_setup_globals() {
    global $bp;

    $bp->simplecart_store->image_base = BP_PLUGIN_SIMPLECART . '/images';
    $bp->simplecart_store->slug = BP_SIMPLECART_STORE_SLUG;

    $bp->version_numbers->simplecart_store = BP_SIMPLECART_STORE_VERSION;
}
add_action('plugins_loaded', 'bp_simplecart_store_setup_globals', 5);
add_action('admin_menu', 'bp_simplecart_store_setup_globals', 1);

add_action('wp', array('SimpleCartFunctions', 'storeBuddyPressMenu'), 1);
add_action('admin_menu', array('SimpleCartFunctions', 'storeBuddyPressMenu'), 1);
