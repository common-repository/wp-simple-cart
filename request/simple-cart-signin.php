<?php
/*
 * This file is part of wp-simple-cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sign Out from WordPress
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     wp-simple-cart
 * @version     svn:$Id: simple-cart-signin.php 140016 2009-07-28 06:57:23Z tajima $
 */
require_once('../../../../wp-config.php');

$secure_cookie = '';
$user = wp_signon('', $secure_cookie);

$results = array();
if (isset($user->errors)) {
    $results['result'] = false;
    $results['message'] = $user->errors;
}
else {
    $results['result'] = true;
}
echo json_encode($results);
