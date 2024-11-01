<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Zip Import Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: zip_import.php 140016 2009-07-28 06:57:23Z tajima $
 */
?>
<h3><?php _e(SCLNG_ADMIN_ZIP_IMPORT, SC_DOMAIN) ?></h3>
<div class='sc_admin_message' style='display:none;'></div>
<?php _e(SCLNG_ZIP_IMPORT_PLEASE_SELECT_FILE, SC_DOMAIN) ?><br />
<div id='waiting_message' style='margin-top:20px;display:none;'><?php _e(SCLNG_ZIP_IMPORT_PLEASE_WAIT, SC_DOMAIN) ?></div>
<input id="zip_file" name="zip_file" type="file" /><br />
<?php
//アップロード設定
$upload_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-upload-zipcode.php';
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function(){
    var message_obj = jQuery(".sc_admin_message");
    //アップロードフィル用イベントアタッチ
    new AjaxUpload('#zip_file', {
        action: '<?php echo $upload_url ?>',
        onSubmit: function(file, extension){
            message_obj.text("");
            message_obj.hide();
            if(confirm("[" + file + "]<?php echo SCLNG_ZIP_IMPORT_CONFIRM_IMPORT;?>")) {
                jQuery("#zip_file").hide();
                jQuery("#waiting_message").show();
            }
            else {
                return false;
            }
        },
        onComplete : function(file, json_text) {
            jQuery("#waiting_message").hide();
            jQuery("#zip_file").show();
            var result_info = JSON.parse(json_text);
            var r_code  = result_info['r_code'];
            var message = result_info['message'];
            if(r_code >= 0) {
                message_obj.css("color", "blue");
            }
            else {
                message_obj.css("color", "red");
            }
            message_obj.show();
            message_obj.text(message);
        }
    });
});
/*]]>*/
</script>
