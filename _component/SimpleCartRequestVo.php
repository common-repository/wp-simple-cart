<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Value Object Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartRequestVo.php 140016 2009-07-28 06:57:23Z tajima $
 */

/******************************************************************************
 * ValueObject Class
 * 
 * @author     Exbridge,inc.
 * @version    0.1
 * 
 *****************************************************************************/
class SimpleCartRequestValueObject {

    var $params = array();

    /**
     * setParam
     */
    function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * getParam
     */
    function getParam($name) {
        return $this->params[$name];
    }
}

/******************************************************************************
 * HTTPRequestVo Class
 * 
 * @author     Exbridge,inc.
 * @version    0.1
 * 
 *****************************************************************************/
class SimpleCartRequestVo extends SimpleCartRequestValueObject {

    /**
     * The Constructor
     */
    function SimpleCartRequestVo() {
        if (is_array($_REQUEST)) {
            foreach($_REQUEST as $name => $value) {
                $this->setParam($name, $value);
            }
        }
    }
}
