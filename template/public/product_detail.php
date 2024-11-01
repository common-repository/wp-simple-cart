<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public Info Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: product_detail.php 147731 2009-08-21 05:28:05Z tajima $
 */
$sc_action = $this->model['sc_action'];
$from = $this->model['from'];

$product = $this->model['product'];
if ($product==false) {
    //商品が見つからなかった
    _e(SCLNG_PUBLIC_NOT_FOUND_PRODUCT, SC_DOMAIN);
    return;
}
$variation_values = $this->model['variation_values'];
$product_variations = $this->model['product_variations'];
$product_variation_values = $this->model['product_variation_values'];
$download_variation_values = $this->model['download_variation_values'];
$product_download_variations = $this->model['product_download_variations'];
$product_download_variation_values = $this->model['product_download_variation_values'];

$quantity_0 = $this->model['quantity_0'];
$quantity_1 = $this->model['quantity_1'];
$error_id = $this->model['error_id'];
$favorite_id = $this->model['favorite_id'];
$review_id = $this->model['review_id'];
$review_list = $this->model['review_list'];
$review_recommend_list = $this->model['review_recommend_list'];
if (is_array($review_recommend_list)) {
    krsort($review_recommend_list);
}
$conditions = $this->model['conditions'];
$conds = array();
foreach ($conditions as $key=>$val) {
    $conds[] = $key . '=' . $val;
}

$results = $this->model['results'];
$cart_message = null;
if (isset($results)) {
    if (isset($results->errors['quantity'])) {
        $err_quantity = $results->errors['quantity'];
    }
    else {
        $cart_message = __(SCLNG_PUBLIC_PRODUCT_DETAIL_SUCCESS_CART, SC_DOMAIN);
    }
}

//Url生成
$image_url2 = SimpleCartFunctions::ProductImageUrl($product['id'], 2, $product['image_file_url2'], $product['store_id'], $product['image_prefix2']);
$image_url3 = SimpleCartFunctions::ProductImageUrl($product['id'], 3, $product['image_file_url3'], $product['store_id'], $product['image_prefix3']);

$open_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-product.php?image_url=' . $image_url3;
$review_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-review.php';
$favorite_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-favorite.php';

//戻る処理用
//1 : 商品一覧（公開）
$back_product_list_url = site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_PRODUCT_LIST);
if ($this->mode==SC_MODE_BP) {
    global $bp;
    //2 : お気に入り（一般会員）
    $back_favorite_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_user->slug . '/' . SC_BPID_FAVORITE);
    //3 : レビュー（店舗会員）
    $back_review_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_product->slug . '/' . SC_BPID_REVIEW);
}
else {
    //2 : お気に入り（一般会員）
    $back_favorite_url = site_url(SCLNG_PAGE_USER_INFO . '/' . SCLNG_PAGE_USER_FAVORITE);
    //3 : レビュー（店舗会員）
    $back_review_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_REVIEW);
}

//在庫管理
$stock = null;
if ($product['stock_manage']==1) {
    $stock = $product['stock'];
}

//追記
$add_discription = null;
if (!is_null($product['add_discription']) && $product['add_discription']!='') {
    $add_discription = $product['add_discription'];
}

//カート制御
$cart_disabled = false;
if ($this->user_ID==0) {
    $cart_disabled = true;
}

//金額情報
//規格連携無し
if ($product['enable_variation']==0) {
    $price = $this->money($product['calc_price']);
    $off   = $this->money($product['calc_off_price']);
}
//規格連携有り
else {
    //最大／最小が同じ
    if ($product['calc_min_price']==$product['calc_max_price']) {
        $price = $this->money($product['calc_min_price']);
        $off   = $this->money($product['calc_min_off_price']);
    }
    //最大／最小に差異がある
    else {
        $price = $this->money($product['calc_min_price']) . '-' . $this->money($product['calc_max_price']);
        $off   = $this->money($product['calc_min_off_price']) . '-' . $this->money($product['calc_max_off_price']);
    }
}
//配送 + ダウンロード
if ($product['download_publish']=='2') {
    if ($product['off']>0) {
        $price = '<div class="pd_price_delivery"><div class="sc_price_off">' . $price . '</div> ' . $off . '</div>';
    }
    else {
        $price = '<div class="pd_price_delivery">' . $price . '</div>';
    }

    //規格連携無し
    if ($product['enable_download_variation']==0) {
        $dl_price = $this->money($product['calc_download_price']);
        $dl_off   = $this->money($product['calc_download_off_price']);
    }
    //規格連携有り
    else {
        //最大／最小が同じ
        if ($product['calc_min_price']==$product['calc_max_price']) {
            $dl_price = $this->money($product['calc_download_min_price']);
            $dl_off   = $this->money($product['calc_download_min_off_price']);
        }
        //最大／最小に差異がある
        else {
            $dl_price = $this->money($product['calc_download_min_price']) . '-' . $this->money($product['calc_download_max_price']);
            $dl_off   = $this->money($product['calc_download_min_off_price']) . '-' . $this->money($product['calc_download_min_off_price']);
        }
    }
    if ($product['download_off']>0) {
        $dl_price = '<div class="pd_price_download"><div class="sc_price_off">' . $dl_price . '</div> ' . $dl_off . '</div>';
    }
    else {
        $dl_price = '<div class="pd_price_download">' . $dl_price . '</div>';
    }
    $price = $price . $dl_price;
}
//ダウンロードのみ
else if ($product['download_publish']=='1') {
    if ($product['off']>0) {
        $price = '<span class="pd_price_download"><span class="sc_price_off">' . $price . '</span> ' . $off . '</span>';
    }
    else {
        $price = '<span class="pd_price_download">' . $price . '</span>';
    }
}
//配送製品
else if ($product['download_publish']=='0') {
    if ($product['off']>0) {
        $price = '<span class="pd_price_delivery"><span class="sc_price_off">' . $price . '</span> ' . $off . '</span>';
    }
    else {
        $price = '<span class="pd_price_delivery">' . $price . '</span>';
    }
}
?>
<?php //製品詳細ヘッダー ?>
<form name="product_detail" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'product_detail_action', 'name'=>'sc_action', 'value'=>'cart')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'id', 'value'=>$product['id'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'download', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'quantity', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'pricestock_id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'error_id', 'value'=>'')); ?>
<?php echo $this->hvars(); ?>

<?php //戻る ?>
<div class="sc_list_header">
  <?php if ($from=='1'): ?>
  <div><?php echo $this->link(array('href'=>'#', 'value'=>'&raquo; ' . SCLNG_PUBLIC_PRODUCT_DETAIL_BACK, 'onclick'=>"Javascript:submitForm('product_detail','','".$back_product_list_url."');return false;")); ?></div>
  <?php elseif ($from=='2'): ?>
  <div><?php echo $this->link(array('href'=>'#', 'value'=>'&raquo; ' . SCLNG_PUBLIC_PRODUCT_DETAIL_BACK, 'onclick'=>"Javascript:submitForm('product_detail','','".$back_favorite_url."');return false;")); ?></div>
  <?php elseif ($from=='3'): ?>
  <div><?php echo $this->link(array('href'=>'#', 'value'=>'&raquo; ' . SCLNG_PUBLIC_PRODUCT_DETAIL_BACK, 'onclick'=>"Javascript:submitForm('product_detail','','".$back_review_url."');return false;")); ?></div>
  <?php endif; ?>
</div>

<?php //製品詳細 ?>
<div class="sc_product">
<?php //カートへ投入のメッセージ表示 ?>
<?php if (!is_null($cart_message)): ?>
<div class="pd_message"><?php echo $cart_message ?></div>
<?php endif; ?>
<table>
  <tr>
    <td class="image_td">
      <div class="pd_image"><img class="sc_detail_image" src="<?php echo $image_url2 ?>" onclick="javascript:window.open('<?php echo $open_url ?>', '_blank', 'width=384px,height=384px,resizable=no');" /></div>

      <?php //会員の場合 ?>
      <?php if ($sc_action!='preview'): ?>
      <?php if(!$cart_disabled): ?>
      <div class="pd_option">
      <?php 
        echo $this->link(array(
                'id'      => 'lnk_review', 
                'href'    => '#', 
                'value'   => '<img src="' . WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/review.gif" width="16" height="16" alt="' . __(SCLNG_PUBLIC_REVIEW_POST, SC_DOMAIN) . '">' . __(SCLNG_PUBLIC_REVIEW_POST, SC_DOMAIN),
                'onclick' => "Javascript:show_review_input_area();return false;")); 
      ?>
      </div>
      <div class="pd_option">
      <?php 
        echo $this->link(array(
                'id'      => 'lnk_favorite', 
                'href'    => '#', 
                'value'   => '<img src="' . WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/favorite.gif" width="16" height="16" alt="' . __(SCLNG_PUBLIC_FAVORITE_ADD, SC_DOMAIN) . '">' . __(SCLNG_PUBLIC_FAVORITE_ADD, SC_DOMAIN),
                'onclick' => "Javascript:save_favorite();return false;"));
      ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </td>
    <td>
      <?php //取扱店舗 ?>
      <div class="pd_store"><?php echo $product['store_name']; ?></div>

      <?php //製品名 ?>
      <div class="pd_name"><?php echo $product['name']; ?></div>
      <div class="pd_code">(<?php echo $product['product_cd']; ?>)</div>

      <?php //カテゴリー ?>
      <div class="pd_category">
      <?php
            $categorys = array();
            if (is_array($product['categorys'])) {
                foreach ($product['categorys'] as $category) {
                    $cate = get_category($category);
                    $categorys[] = $cate->name;
                }
            }
            echo @implode(',', $categorys);
      ?>
      </div>

      <?php //価格 ?>
      <div class="pd_price"><?php echo $price; ?></div>

      <?php //説明 ?>
      <div class="pd_description"><?php echo nl2br($product['description']) ?></div>

      <?php //追加説明 ?>
      <?php if (!is_null($product['add_description'])): ?>
      <div class="pd_add_description"><?php echo nl2br($product['add_description']) ?></div>
      <?php endif; ?>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <div class="pd_separator"></div>

      <?php //カート ?>
      <div class="pd_data">

      <?php //----------------------------------------------------------------------------------// ?>
      <?php //会員でない場合 ?>
      <?php if($cart_disabled): ?>
        <div class="sc_cart_alert"><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_USER_ALERT, SC_DOMAIN) ?></div>

      <?php //----------------------------------------------------------------------------------// ?>
      <?php //通常製品出力 ?>
      <?php elseif ($product['download_publish']=='0' || $product['download_publish']=='2'): ?>

          <?php //----------------------------------------------------------------------------------// ?>
          <?php //規格なしの場合 ?>
          <?php if ($product['enable_variation']=='0'): ?>

              <?php //在庫がない ?>
              <?php if (($product['stock_manage']==1) && ($stock==0)): ?>
                  <div class="sc_cart_lack_stock"><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_LACK_STOCK, SC_DOMAIN) ?></div>
              <?php //在庫がある ?>
              <?php else: ?>
                  <?php if ($product['download_publish']=='2'): ?>
                  <div class="pd_product_type"><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_DELIVERY, SC_DOMAIN); ?></div>
                  <?php endif; ?>
                  <?php if ($product['download_publish']=='0'||$product['download_publish']=='1'): ?>
                  <div class="pd_buy_novariation">
                  <div class="pd_buy_quantity">
                  <?php
                      if (is_null($quantity_0) || $quantity_0=='') {
                          $quantity_0 = 1;
                      }
                  ?>
                  <?php echo $this->input(array('id'=>'quantity_0', 'name'=>'quantity_0', 'value'=>$quantity_0, 'class'=>'input_40')); ?>
                  <?php echo $this->submit(array('id'=>'buy_delivery_0', 'onclick'=>"
                                      Javascript:
                                          document.getElementById('download').value = 0;
                                          document.getElementById('pricestock_id').value = '';
                                          document.getElementById('quantity').value = document.getElementById('quantity_0').value;
                                          document.getElementById('error_id').value = '0';
                                      ", 'class'=>'button_buy', 'value'=>__(SCLNG_BUY_NOW, SC_DOMAIN))); ?>
                  <?php echo $this->error($err_quantity[$error_id]); ?>
                  </div>

                  <?php //在庫表示 ?>
                  <?php if (($product['stock_manage']==1) && $stock!=0): ?>
                  <div class="pd_buy_stock"><?php echo _e(sprintf(SCLNG_PUBLIC_PRODUCT_DETAIL_VARIATION_STOCK, $stock), SC_DOMAIN); ?></div>
                  <?php endif; ?>
                  </div>
                  <?php endif; ?>
              <?php endif; ?>

          <?php //----------------------------------------------------------------------------------// ?>
          <?php //規格連携の場合 ?>
          <?php elseif ($product['enable_variation']=='1'): ?>
              <?php if ($product['download_publish']=='2'): ?>
              <div class="pd_product_type"><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_DELIVERY, SC_DOMAIN); ?></div>
              <?php endif; ?>
              <div class="pd_buy">
              <?php SimpleCartFunctions::HTMLCartVariationValues(null, $results, $product, $product_variations, $product_variation_values, $variation_values, $quantity_0); ?>
              </div>
          <?php endif; ?>
      <?php endif; ?>

      <?php //----------------------------------------------------------------------------------// ?>
      <?php //ダウンロード製品出力 ?>
      <?php if ($product['download_publish']=='1' || $product['download_publish']=='2'): ?>

          <?php //----------------------------------------------------------------------------------// ?>
          <?php //規格なしの場合 ?>
          <?php if ($product['enable_download_variation']=='0'): ?>
              <?php if ($product['download_publish']=='2'): ?>
              <div class="pd_product_type"><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_DOWNLOAD, SC_DOMAIN); ?></div>
              <div class="pd_buy_novariation">
              <div class="pd_buy_quantity">
              <?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_QUANTITY, SC_DOMAIN); ?>
              <?php
                  if (is_null($quantitys_1) || $quantitys_1=='') {
                      $quantitys_1 = 1;
                  }
              ?>
              <?php echo $this->input(array('id'=>'quantity_1', 'name'=>'quantity_1', 'value'=>$quantitys_1, 'class'=>'input_40')); ?>
              <?php echo ' '; ?>
              <?php echo $this->submit(array('id'=>'buy_delivery_1', 'onclick'=>"
                                  Javascript:
                                      document.getElementById('download').value = 1;
                                      document.getElementById('pricestock_id').value = '';
                                      document.getElementById('quantity').value = document.getElementById('quantity_1').value;
                                      document.getElementById('error_id').value = '1';
                                  ", 'class'=>'button_buy', 'value'=>__(SCLNG_BUY_NOW, SC_DOMAIN))); ?>
              <?php echo $this->error($err_quantity[$error_id]); ?>
              </div>
              </div>
              <?php endif; ?>

          <?php //----------------------------------------------------------------------------------// ?>
          <?php //規格連携の場合 ?>
          <?php elseif ($product['enable_download_variation']=='1'): ?>
              <?php if ($product['download_publish']=='2'): ?>
              <div class="pd_product_type"><?php _e(SCLNG_PUBLIC_PRODUCT_DETAIL_DOWNLOAD, SC_DOMAIN); ?></div>
              <?php endif; ?>
              <div class="pd_buy">
              <?php SimpleCartFunctions::HTMLCartVariationValues(null, $results, $product, $product_download_variations, $product_download_variation_values, $download_variation_values, $quantitys_1); ?>
              </div>
          <?php endif; ?>
      <?php endif; ?>
      </div>

      <div class="pd_separator"></div>

      <!--レビュー-->
      <div class="pd_data review_area" id='review_list_area'>
      </div>
    </td>
  </tr>
</table>
</div>

<!-- ユーザーレビュー -->
<div id="review_input_area" style='display:none;'>
  <table>
    <tr>
      <th>
        <?php _e(SCLNG_PUBLIC_REVIEW_RECOMMEND, SC_DOMAIN); ?>:
      </th>
      <td class='review_recommend'>
        <?php echo $this->select(array('id'=>'review_recommend'), array('list' => $review_recommend_list)); ?>
      </td>
    </tr>
    <tr>
      <th>
        <?php _e(SCLNG_PUBLIC_REVIEW_COMMENT, SC_DOMAIN); ?>:
      </th>
      <td class='review_comment'><?php echo $this->text(array('id'=>'review_comment')); ?></td>
    </tr>
    <tr>
      <td colspan='2' class='review_control'>
        <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_PUBLIC_CANCEL, SC_DOMAIN), 'class'=>'btn_cancel_review', 'onclick'=>"Javascript:jQuery.unblockUI();")); ?>
        <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_PUBLIC_POST, SC_DOMAIN), 'class'=>'btn_save_review', 'onclick'=>"Javascript:save_review();")); ?>
      </td>
    </tr>
  </table>
</div>
</form>

<script type="text/javascript">
/* <![CDATA[ */
    jQuery(function() {
        get_review_list(disp_review_list);
    });

    /**
     *  お気に入りの登録
     */
    function save_favorite() {
        var product_id = "<?php echo $product['id']?>";
        jQuery.post("<?php echo $favorite_url;?>", {
                        action : "save",
                        product_id : product_id
                    },
                    function (res) {
                        if (res) {
                            jQuery("#lnk_favorite").replaceWith("<?php _e(SCLNG_PUBLIC_FAVORITE_ADDED, SC_DOMAIN)?>");
                        }
                    },
                    "json")
    }

    /**
     *  レビュー書き込み領域表示
     */
    function show_review_input_area() {
        jQuery("#review_recommend").val("");
        jQuery("#review_comment").val("");
        jQuery.blockUI({ 
                        message: jQuery('#review_input_area'), 
                        css: { 
                                cursor: "default"
                        },
                        overlayCSS: { 
                            opacity: 0.7 
                        }
                   }); 
        jQuery('.blockOverlay').css("cursor", "default").click(jQuery.unblockUI); 
    }

    /**
     *  レビュー一覧表示
     */
    function disp_review_list(review_info_list) {
        var default_list_count = 3;
        var html_list = new Array();
        var recommend_list = JSON.parse('<?php echo json_encode($review_recommend_list)?>');

        html_list.push("<table class='wd450'>");
        var list_count = 0;
        var review_list_css;
        for (var i = 0; i < review_info_list.length; i++) {
            var review_info = review_info_list[i];
            list_count = i + 1;
            review_list_css = "";
            if (list_count > default_list_count) {
                review_list_css = "review_list_all";
            }
            html_list.push("<tr class='" + review_list_css + "'>");
            html_list.push("  <td>");
            html_list.push(review_info['regist_date']);
            html_list.push("  <span class='review_user_lbl'><?php _e(SCLNG_PUBLIC_REVIEW_USER, SC_DOMAIN); ?>:</span>");
            html_list.push(review_info['user_name']);
            html_list.push("  </td>");
            html_list.push("</tr>");
            html_list.push("<tr class='" + review_list_css + "'>");
            html_list.push("  <td>");
            html_list.push("  <div class='review_recommend'>");
            html_list.push("    <span class='label'><?php _e(SCLNG_PUBLIC_REVIEW_RECOMMEND, SC_DOMAIN); ?>:</span>");
            html_list.push(recommend_list[review_info['recommend']]);
            html_list.push("  </div>");
            html_list.push("  <div class='review_comment'>");
            html_list.push(review_info['comment'].replace(/\n/g, "<br />"));
            html_list.push("  </div>");
            html_list.push("  </td>");
            html_list.push("</tr>");
        }
        html_list.push("</table>");
        html_list.push("<a id='lnk_show_review_list' href='#' onclick='javascript:show_review_list_all();return false;'><?php _e(SCLNG_PUBLIC_REVIEW_LIST_ALL_SHOW, SC_DOMAIN); ?></a>");
        html_list.push("<a id='lnk_hide_review_list' style='display:none;' href='#' onclick='javascript:hide_review_list_all();return false;'><?php _e(SCLNG_PUBLIC_REVIEW_LIST_ALL_HIDE, SC_DOMAIN); ?></a>");
        var html_text = html_list.join("\n");
        jQuery("#review_list_area").html(html_text);
    }

    /**
     *  レビュー一覧全表示
     */
    function show_review_list_all() {
        jQuery(".review_list_all").show();
        jQuery("#lnk_show_review_list").hide();
        jQuery("#lnk_hide_review_list").show();
    }

    /**
     *  レビュー一覧全表示解除
     */
    function hide_review_list_all() {
        jQuery(".review_list_all").hide();
        jQuery("#lnk_show_review_list").show();
        jQuery("#lnk_hide_review_list").hide();
    }

    /**
     *  レビュー書き込み領域表示
     */
    function get_review_list(callback) {
        var store_id = "<?php echo $product['store_id']?>";
        var product_id = "<?php echo $product['id']?>";

        jQuery.post("<?php echo $review_url;?>", 
                    {
                        action      : "get",
                        store_id    : store_id,
                        product_id  : product_id
                    },
                    function (res) {
                        if(res) {
                            if (typeof callback == "function") {
                                callback(res);
                            }
                        }
                    },
                    "json"
                  )
    }

    /**
     *  レビューの登録
     */
    function save_review() {
        var store_id = "<?php echo $product['store_id']?>";
        var product_id = "<?php echo $product['id']?>";
        var review_comment = jQuery("#review_comment").val();
        var review_recommend = jQuery("#review_recommend").val();
        var error_id = "error_review_comment";

        jQuery("#" + error_id).remove();
        if(jQuery.trim(review_comment) == ''){
            var message =  '<?php _e(SCLNG_CHECK_EMPTY_COMMENT, SC_DOMAIN)?>';
            jQuery("#review_comment").after("<p class='error_small' id='" + error_id +"'></p>");
            jQuery("#" + error_id).html(message).fadeIn();
            return false;
        }
        jQuery.unblockUI();
        jQuery.post("<?php echo $review_url;?>", 
                    {
                        action      : "save",
                        store_id    : store_id,
                        product_id  : product_id,
                        recommend   : review_recommend,
                        comment     : review_comment
                    },
                    function (res) {
                        if(res) {
                            jQuery("#lnk_review").replaceWith("<?php _e(SCLNG_PUBLIC_REVIEW_POSTED, SC_DOMAIN)?>");
                            get_review_list(disp_review_list);
                        }
                    },
                    "json"
                  )
    }
/*]]>*/
</script>
