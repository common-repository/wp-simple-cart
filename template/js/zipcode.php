<?php
$zip2addressinfo_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-zipcode.php';
?>
<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
/**
 *  郵便番号から住所情報を取得
 *  params callback_obj['function'] コールバック用関数
 *         callback_obj['params']   コールバック関数の引数
 *         callback_obj['popup_target_id']   住所候補選択一覧ポップアップウィンドウ用の表示場所
 */
function sc_zip2AddressInfo(zipcode, callback_obj) {
    jQuery(".address_list_area").remove();
    jQuery.get("<?php echo $zip2addressinfo_url;?>", 
                {
                    "zipcode":zipcode
                },
                function (address_info_list) {
                    var callback = callback_obj['function'];
                    var params   = callback_obj['params'];

                    if (!address_info_list) {
                        return false;
                    }

                    if (address_info_list.length == 1) {
                        var address_info = address_info_list[0];
                        if (typeof callback == "function") {
                            callback(address_info, params);
                        }
                    }
                    else if (address_info_list.length > 1) {
                        sc_show_address_list(address_info_list, callback_obj)
                    }
                },
                "json"
              )
}

/**
 *  住所情報候補一覧
 */
function sc_show_address_list(address_info_list, callback_obj) {
    var parent_id   = callback_obj['popup_target_id'];
    var callback    = callback_obj['function'];
    var params      = callback_obj['params'];
    var html_list = new Array();

    html_list.push("<div class='address_list_area'>");
    html_list.push("<table>");
    html_list.push("<thead>");
    html_list.push("<tr>");
    html_list.push("<td><?php _e(SCLNG_SELECT_ADDRESS, SC_DOMAIN)?></td>");
    html_list.push("</tr>");
    html_list.push("</thead>");
    html_list.push("<tbody>");
    for (var i = 0; i < address_info_list.length; i++) {
        var address_info = address_info_list[i];
        var address = "";
        var prefix = parent_id + "_" + i + "_";
        address += address_info["state"];
        address += "　" + address_info["city"];
        address += "　" + address_info["street"];

        html_list.push("<tr>");
        html_list.push("<td class='address' list_no='" + i + "'>" + address + "</td>");
        html_list.push("</tr>");
    }
    html_list.push("</tbody>");
    html_list.push("</table>");
    html_list.push("<div class='close' onclick='javascript:jQuery(\".address_list_area\").remove();'><?php _e(SCLNG_CLOSE, SC_DOMAIN)?></div>");
    html_list.push("</div>");
    var html_text = html_list.join("\n");

    jQuery("#" + parent_id).after(html_text)
                           .next()
                           .children("table")
                           .find(".address").click(
                                    function () {
                                        var key = jQuery(this).attr("list_no");
                                        var address_info = address_info_list[key];
                                        if (typeof callback == "function") {
                                            callback(address_info, params);
                                            jQuery(".address_list_area").remove();
                                        }
                                    }
                                )
                                .hover(
                                      function () {
                                        jQuery(this).addClass("highlight");
                                      }, 
                                      function () {
                                        jQuery(this).removeClass("highlight");
                                      }
                                 );
}
/*]]>*/
</script>
