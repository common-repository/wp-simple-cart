<?php
/*
 * This file is part of wp-simple-cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Image file upload
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     wp-simple-cart
 * @version     svn:$Id: simple-cart-preview.php 143905 2009-08-08 07:26:39Z tajima $
 */
require_once('../../../../wp-config.php');

get_header();

echo '<div id="content" class="narrowcolumn">';
require_once(PLUGIN_SIMPLE_CART . '/_model/public/SimpleCartPublicModel.php');
require_once(PLUGIN_SIMPLE_CART . '/_controller/public/SimpleCartPublicProductDetail.php');
$sc_controller = & new SimpleCartPublicProductDetail();
$sc_controller->execute();
echo '</div>';

get_sidebar();

get_footer();

