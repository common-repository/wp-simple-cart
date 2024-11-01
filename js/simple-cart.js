/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart JS Library（ライブラリ）
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id:$
 */
function submitForm(frm_name, action_value, action_url) {
    if (action_value!=null || action_value!=undefined) {
        var obj = document.getElementById(frm_name + '_action');
        if (obj || obj!=undefined) {
            obj.value = action_value;
        }
    }
    if (action_url != '' && action_url != undefined) {
        document.forms[frm_name].action = encodeURI(action_url);
    }
    document.forms[frm_name].submit();
}

function actionForm(frm_name, action_value, action_url) {
    if (action_value!=null || action_value!=undefined) {
        var obj = document.getElementById(frm_name + '_action');
        if (obj || obj!=undefined) {
            obj.value = action_value;
        }
    }
    if (action_url != '' && action_url != undefined) {
        document.forms[frm_name].action = encodeURI(action_url);
    }
}
