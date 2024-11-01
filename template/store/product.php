<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Product Register Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: product.php 147731 2009-08-21 05:28:05Z tajima $
 */
$product         = $this->model['product'];
$download_prefix = $this->model['download_prefix'];
$prefix1         = $this->model['prefix1'];
$prefix2         = $this->model['prefix2'];
$prefix3         = $this->model['prefix3'];
$categorys       = $this->model['categorys'];
$results         = $this->model['results'];
$top             = $this->model['top'];

$download_del_tag = '[<a href="#" onclick="Javascript:dlfile_del();return false;">' . __(SCLNG_DELETE, SC_DOMAIN) . '</a>]';
$image1_del_tag = '[<a href="#" onclick="Javascript:imgfile1_del();return false;">' . __(SCLNG_DELETE, SC_DOMAIN) . '</a>]';
$image2_del_tag = '[<a href="#" onclick="Javascript:imgfile2_del();return false;">' . __(SCLNG_DELETE, SC_DOMAIN) . '</a>]';
$image3_del_tag = '[<a href="#" onclick="Javascript:imgfile3_del();return false;">' . __(SCLNG_DELETE, SC_DOMAIN) . '</a>]';
$image1_no_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/noimage_1.jpg';
$image2_no_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/noimage_2.jpg';
$image3_no_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/noimage_3.jpg';
$nodownload = '1';//画像なし
$noimage1 = '1';//画像なし
$noimage2 = '1';//画像なし
$noimage3 = '1';//画像なし

//エラー判定
if (isset($results)) {
    $err_product_cd = $results->errors['product_cd'];
    $err_product_name = $results->errors['product_name'];
    $err_price = $results->errors['price'];
    $err_off = $results->errors['off'];
    $err_quantity_limit = $results->errors['quantity_limit'];
    $err_description = $results->errors['description'];
    $err_add_description = $results->errors['add_description'];
    $err_categorys = $results->errors['categorys'];
    $err_stock = $results->errors['stock'];
    $err_download = $results->errors['download'];
    $err_download_price = $results->errors['download_price'];
    $err_download_off = $results->errors['download_off'];
}

//イメージファイル設定
$upload_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-upload.php';
$image_url_dir1 = SimpleCartFunctions::ImageTemporaryUrl($prefix1);
$image_url_dir2 = SimpleCartFunctions::ImageTemporaryUrl($prefix2);
$image_url_dir3 = SimpleCartFunctions::ImageTemporaryUrl($prefix3);

//
$image_url1 = $image1_no_url;
$image_url2 = $image2_no_url;
$image_url3 = $image3_no_url;
$download_publish = '0';
$publish = '0';
$notax = '0';
$stock_manage = '0';
$download_file = __(SCLNG_NOT_REGISTER_PRODUCT_FILE, SC_DOMAIN);
$download_delete = '';
$image_delete1 = '';
$image_delete2 = '';
$image_delete3 = '';
if (is_array($product)) {
    if (!is_null($product['image_file_url1']) && $product['image_file_url1']!='') {
        $image_url1 = SimpleCartFunctions::ProductImageUrl($product['id'], 1, $product['image_file_url1'], null, $prefix1);
        $image_delete1 = $image1_del_tag;
        $noimage1 = '0';//既存画像
    }
    if (!is_null($product['image_file_url2']) && $product['image_file_url2']!='') {
        $image_url2 = SimpleCartFunctions::ProductImageUrl($product['id'], 2, $product['image_file_url2'], null, $prefix2);
        $image_delete2 = $image2_del_tag;
        $noimage2 = '0';//既存画像
    }
    if (!is_null($product['image_file_url3']) && $product['image_file_url3']!='') {
        $image_url3 = SimpleCartFunctions::ProductImageUrl($product['id'], 3, $product['image_file_url3'], null, $prefix3);
        $image_delete3 = $image3_del_tag;
        $noimage3 = '0';//既存画像
    }
    if (!is_null($product['publish'])) {
        $publish = $product['publish'];
    }
    if (!is_null($product['download_publish'])) {
        $download_publish = $product['download_publish'];
    }
    if (!empty($product['notax'])) {
        $notax = $product['notax'];
    }
    if (!empty($product['stock_manage'])) {
        $stock_manage = $product['stock_manage'];
    }
    if (isset($product['product_url']) && $product['product_url']!='') {
        $product_url = $product['product_url'];
        $download_delete = $download_del_tag;
        $nodownload = '0';//既存製品ファイル
    }
}

if ($this->mode==SC_MODE_BP) {
    global $bp;
    if ($top=='1') {
        $back_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_store->slug . '/' . SC_BPID_STORE_INFO);
    }
    else {
        $back_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_store->slug . '/' . SC_BPID_PRODUCT_LIST);
    }
}
else {
    if ($top=='1') {
        $back_url = site_url(SCLNG_PAGE_STORE_INFO);
    }
    else {
        $back_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_PRODUCT_LIST);
    }
}

//プレビュー用
$preview_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-preview.php';
?>

<form name="product" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'product_action', 'name'=>'sc_action', 'value'=>'save_product')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'product_id', 'value'=>$product['id'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'id', 'value'=>$product['id'])); ?>
<?php $this->hvars(); ?>

<?php if ($top=='1'): ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('product', '', '".$back_url."');return false;")); ?> </p>
<?php elseif (isset($product['id']) && $product['id']!=''): ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('product', '', '".$back_url."');return false;")); ?> </p>
<?php endif; ?>

<?php //基本情報 ?>
<div class="sc_subtitle_updt"><?php _e(SCLNG_STORE_PRODUCT_HEADER_BASE, SC_DOMAIN) ?></div>
<div class="sc_tbl_updt_product">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_CODE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'product_cd', 'value'=>$product['product_cd'], 'class'=>'input_120')); ?><?php $this->error($err_product_cd); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'product_name', 'value'=>$product['name'], 'class'=>'input_200')); ?><?php $this->error($err_product_name); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_PRICE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'price', 'value'=>$product['price'], 'class'=>'input_120')); ?><?php $this->error($err_price); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_OFF, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'off', 'value'=>$product['off'], 'class'=>'input_120')); ?><?php $this->error($err_off); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_QUANTITY_LIMIT, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'quantity_limit', 'value'=>$product['quantity_limit'], 'class'=>'input_120')); ?><?php $this->error($err_quantity_limit); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_DESCRIPTION, SC_DOMAIN) ?></th>
    <td><?php echo $this->text(array('id'=>'description', 'value'=>$product['description'], 'rows'=>'3', 'cols'=>'47')); ?><?php $this->error($err_description); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_ADD_DESCRIPTION, SC_DOMAIN) ?></th>
    <td><?php echo $this->text(array('id'=>'add_description', 'value'=>$product['add_description'], 'rows'=>'8', 'cols'=>'47')); ?><?php $this->error($err_add_description); ?></td>
  </tr>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_PUBLISH, SC_DOMAIN) ?></th>
    <td>
    <?php echo $this->input(array('type'=>'radio', 'name'=>'publish', 'value'=>'1', 'checked'=>($publish=='1')?'checked="checked"':'')); ?><?php _e(SCLNG_STORE_PRODUCT_PUBLISH_OPEN, SC_DOMAIN); ?>
    <?php echo $this->input(array('type'=>'radio', 'name'=>'publish', 'value'=>'0', 'checked'=>($publish=='0')?'checked="checked"':'')); ?><?php _e(SCLNG_STORE_PRODUCT_PUBLISH_CLOSE, SC_DOMAIN); ?>
    </td>
  </tr>
</table>
</div>

<?php //カテゴリー情報 ?>
<div class="sc_subtitle_updt"><?php _e(SCLNG_STORE_PRODUCT_HEADER_CATEGORY, SC_DOMAIN) ?></div>
<div class="sc_tbl_updt_product">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_PRODUCT_CATEGORY, SC_DOMAIN) ?></th>
    <td>
    <div style="overflow-y:scroll;height:300px;max-height:300px;">
    <?php echo SimpleCartFunctions::HTMLCategorys($categorys, $product['categorys']); ?>
    </div>
    <?php $this->error($err_categorys); ?>
    </td>
  </tr>
</table>
</div>

<?php //その他情報 ?>
<div class="sc_subtitle_updt"><?php _e(SCLNG_STORE_PRODUCT_HEADER_OTHER, SC_DOMAIN) ?></div>
<div class="sc_tbl_updt_product">
<table>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_TAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('type'=>'checkbox', 'name'=>'notax', 'value'=>'1', 'checked'=>($notax=='1')?'checked="checked"':'')); ?> <?php _e(SCLNG_STORE_PRODUCT_NOT_INCLUDE_TAX, SC_DOMAIN) ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_STOCK, SC_DOMAIN) ?></th>
    <td>
    <?php echo $this->input(array('type'=>'checkbox', 'id'=>'stock_manage', 'value'=>'1', 'checked'=>($stock_manage=='1')?'checked="checked"':'')); ?> <?php _e(SCLNG_STORE_PRODUCT_STOCK_CONTROL, SC_DOMAIN) ?><br/>
    <?php echo $this->input(array('type'=>'text', 'id'=>'stock', 'value'=>$product['stock'], 'class'=>'input_120')); ?>
    <?php $this->error($err_stock); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_DOWNLOAD, SC_DOMAIN) ?></th>
    <td>
    <?php echo $this->input(array('type'=>'radio', 'id'=>'download_publish_2', 'name'=>'download_publish', 'value'=>'2', 'checked'=>($download_publish=='2')?'checked="checked"':'', 'onclick'=>"Javascript:checkDownlod();")); ?><?php _e(SCLNG_STORE_PRODUCT_DOWNLOAD_PUBLISH, SC_DOMAIN); ?><br/>
    <?php echo $this->input(array('type'=>'radio', 'id'=>'download_publish_1', 'name'=>'download_publish', 'value'=>'1', 'checked'=>($download_publish=='1')?'checked="checked"':'', 'onclick'=>"Javascript:checkDownlod();")); ?><?php _e(SCLNG_STORE_PRODUCT_DOWNLOAD_ONLY, SC_DOMAIN); ?><br/>
    <?php echo $this->input(array('type'=>'radio', 'id'=>'download_publish_0', 'name'=>'download_publish', 'value'=>'0', 'checked'=>($download_publish=='0')?'checked="checked"':'', 'onclick'=>"Javascript:checkDownlod();")); ?><?php _e(SCLNG_STORE_PRODUCT_DOWNLOAD_CLOSE, SC_DOMAIN); ?><br/>
    <div id="div_download_price" class="row_content"><div class="col_content wd100"><?php _e(SCLNG_STORE_PRODUCT_DOWNLOAD_PRICE, SC_DOMAIN) ?></div><?php echo $this->input(array('id'=>'download_price', 'value'=>$product['download_price'], 'class'=>'input_120')); ?><?php $this->error($err_download_price); ?></div>
    <div id="div_download_off" class="row_content"><div class="col_content wd100"><?php _e(SCLNG_STORE_PRODUCT_DOWNLOAD_OFF, SC_DOMAIN) ?></div><?php echo $this->input(array('id'=>'download_off', 'value'=>$product['download_off'], 'class'=>'input_120')); ?><?php $this->error($err_download_off); ?></div>
    <?php echo $this->input(array('type'=>'button', 'id'=>'download_upload', 'value'=>__(SCLNG_ADD_FILE, SC_DOMAIN))); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'no_download', 'value'=>$nodownload)); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'download_prefix', 'value'=>$download_prefix)); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'product_url', 'value'=>$product['product_url'])); ?><br/>
    <div id="download_file_delete"><?php echo $download_delete ?></div>
    <div id="download_file_label"><?php echo $product_url ?><?php $this->error($err_download); ?></div>
    <script type="text/javascript">
    function checkDownlod() {
        var download_0 = document.getElementById('download_publish_0').checked;
        var download_1 = document.getElementById('download_publish_1').checked;
        var download_2 = document.getElementById('download_publish_2').checked;
        var download;
        if (download_0==true) {
            download = 0;
        }
        else if (download_1==true) {
            download = 1;
        }
        else if (download_2==true) {
            download = 2;
        }
        if (download==2) {
            jQuery("#div_download_price").css('display', 'block');
            jQuery("#div_download_off").css('display', 'block');
        }
        else {
            jQuery("#div_download_price").css('display', 'none');
            jQuery("#div_download_off").css('display', 'none');
        }
    }
    jQuery(document).ready(function() {
        checkDownlod();
    });/*]]>*/
    </script>
    </td>
  </tr>
</table>
<script type="text/javascript">
jQuery(document).ready(function() {
    //ダウンロードファイル用イベントアタッチ
    new AjaxUpload('#download_upload', {
        action: '<?php echo $upload_url ?>?prefix=<?php echo $download_prefix; ?>',
        onComplete : function(file, r_file){
            jQuery("#no_download").val('0');
            jQuery("#download_file_label").html(r_file);
            jQuery("#product_url").val(r_file);
            jQuery("#download_file_delete").html('<?php echo $download_del_tag ?>');
        }
    });
});/*]]>*/
function dlfile_del() {
    jQuery("#no_download").val('1');
    jQuery("#download_file_label").html('');
    jQuery("#product_url").val('');
    jQuery("#download_file_delete").html('');
}
</script>
</div>

<?php //イメージ情報 ?>
<div class="sc_subtitle_updt"><?php _e(SCLNG_STORE_PRODUCT_HEADER_IMAGE, SC_DOMAIN) ?></div>
<div class="sc_tbl_updt_product">
<table>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_IMAGE_1, SC_DOMAIN); ?></th>
    <td>
      <?php echo $this->input(array('type'=>'button', 'id'=>'media_upload_1', 'value'=>__(SCLNG_ADD_IMAGE, SC_DOMAIN))); ?>
      <div id="media_upload_delete_1"><?php echo $image_delete1 ?></div>
      <div id="media_upload_file_1"><img style="width:96px;" src="<?php echo $image_url1 ?>" /></div>
      <div>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'no_image1', 'value'=>$noimage1)); ?>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'image_prefix1', 'value'=>$prefix1)); ?>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'image_file_url1', 'value'=>$product['image_file_url1'])); ?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_IMAGE_2, SC_DOMAIN); ?></th>
    <td>
      <?php echo $this->input(array('type'=>'button', 'id'=>'media_upload_2', 'value'=>__(SCLNG_ADD_IMAGE, SC_DOMAIN))); ?>
      <div id="media_upload_delete_2"><?php echo $image_delete2 ?></div>
      <div id="media_upload_file_2"><img style="width:192px;" src="<?php echo $image_url2 ?>" /></div>
      <div>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'no_image2', 'value'=>$noimage2)); ?>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'image_prefix2', 'value'=>$prefix2)); ?>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'image_file_url2', 'value'=>$product['image_file_url2'])); ?>
      </div>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_PRODUCT_IMAGE_3, SC_DOMAIN); ?></th>
    <td>
      <?php echo $this->input(array('type'=>'button', 'id'=>'media_upload_3', 'value'=>__(SCLNG_ADD_IMAGE, SC_DOMAIN))); ?>
      <div id="media_upload_delete_3"><?php echo $image_delete3 ?></div>
      <div id="media_upload_file_3"><img style="width:384px;" src="<?php echo $image_url3 ?>" /></div>
      <div>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'no_image3', 'value'=>$noimage3)); ?>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'image_prefix3', 'value'=>$prefix3)); ?>
      <?php echo $this->input(array('type'=>'hidden', 'id'=>'image_file_url3', 'value'=>$product['image_file_url3'])); ?>
      </div>
    </td>
  </tr>
</table>
<script type="text/javascript">
jQuery(document).ready(function() {
    //画像１用イベントアタッチ
    new AjaxUpload('#media_upload_1', {
        action: '<?php echo $upload_url ?>?prefix=<?php echo $prefix1; ?>&file=<?php echo SC_IMAGE_001; ?>',
        onComplete : function(file, r_file) {
            var file_name = "<?php echo $image_url_dir1; ?>" + r_file;
            jQuery("#media_upload_file_1").html("<img style='width:96px;' src='" + file_name + "' />");
            jQuery("#image_file_url1").val(r_file);
            jQuery("#media_upload_delete_1").html('<?php echo $image1_del_tag ?>');
            jQuery("#no_image1").val('0');
        }
    });
    //画像２用イベントアタッチ
    new AjaxUpload('#media_upload_2', {
        action: '<?php echo $upload_url ?>?prefix=<?php echo $prefix2; ?>&file=<?php echo SC_IMAGE_002; ?>',
        onComplete : function(file, r_file) {
            var file_name = "<?php echo $image_url_dir2; ?>" + r_file;
            jQuery("#media_upload_file_2").html("<img style='width:192px;' src='" + file_name + "' />");
            jQuery("#image_file_url2").val(r_file);
            jQuery("#media_upload_delete_2").html('<?php echo $image2_del_tag ?>');
            jQuery("#no_image2").val('0');
        }
    });
    //画像３用イベントアタッチ
    new AjaxUpload('#media_upload_3', {
        action: '<?php echo $upload_url ?>?prefix=<?php echo $prefix3; ?>&file=<?php echo SC_IMAGE_003; ?>',
        onComplete : function(file, r_file) {
            var file_name = "<?php echo $image_url_dir3; ?>" + r_file;
            jQuery("#media_upload_file_3").html("<img style='width:384px;' src='" + file_name + "' />");
            jQuery("#image_file_url3").val(r_file);
            jQuery("#media_upload_delete_3").html('<?php echo $image3_del_tag ?>');
            jQuery("#no_image3").val('0');
        }
    });
});/*]]>*/
function imgfile1_del() {
    jQuery("#media_upload_file_1").html("<img style='width:96px;' src='<?php echo $image1_no_url ?>' />");
    jQuery("#image_file1").val('');
    jQuery("#media_upload_delete_1").html('');
    jQuery("#no_image1").val('1');
}
function imgfile2_del() {
    jQuery("#media_upload_file_2").html("<img style='width:192px;' src='<?php echo $image2_no_url ?>' />");
    jQuery("#image_file2").val('');
    jQuery("#media_upload_delete_2").html('');
    jQuery("#no_image2").val('1');
}
function imgfile3_del() {
    jQuery("#media_upload_file_3").html("<img style='width:384px;' src='<?php echo $image3_no_url ?>' />");
    jQuery("#image_file3").val('');
    jQuery("#media_upload_delete_3").html('');
    jQuery("#no_image3").val('1');
}
</script>
</div>

<?php if(is_null($product['id'])||$product['id']==''): ?>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_CREATE, SC_DOMAIN) . " &raquo;")); ?></p>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_STORE_PRODUCT_CREATE_VARIATION, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:actionForm('product', 'save_product_variation');")); ?></p>
<?php else: ?>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?></p>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_STORE_PRODUCT_UPDATE_VARIATION, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:actionForm('product', 'save_product_variation');")); ?></p>
<?php endif; ?>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_STORE_PRODUCT_PREVIEW, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:submitPreview();return false;")); ?></p>
<script type="text/javascript">
function submitPreview() {
    window.open('about:blank', '_sc_preview', 'width=1024px,height=768px,resizable=yes,scrollbars=yes');
    document.getElementById('product_action').value = 'preview';
    document.forms['product'].action = '<?php echo $preview_url ?>';
    document.forms['product'].target = '_sc_preview';
    document.forms['product'].submit();
    //戻し処理
    document.forms['product'].action = '<?php echo $_SERVER["REQUEST_URI"] ?>';
    document.forms['product'].target = '_self';
}
</script>

</form>
