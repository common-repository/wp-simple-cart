<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Store Zip Import Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartAdminZipImport.php 140016 2009-07-28 06:57:23Z tajima $
 */

class SimpleCartAdminZipImport extends SimpleCartAdmin {

    /**
     * Store User Controller
     *
     */
    function execute() {
        $this->exec('admin/zip_import');
    }
}
