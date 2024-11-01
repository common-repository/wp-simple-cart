<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Product List Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: product_list.php 143340 2009-08-06 11:47:12Z tajima $
 */
$categorys = $this->model['categorys'];
$product_search = $this->model['product_search'];
$product_count = $this->model['product_count'];
$product_list = $this->model['product_list'];

$check_publish = '';
if ($product_search['s_publish']=='1') {
    $check_publish = 'checked="checked"';
}
$check_stock = '';
if ($product_search['s_stock']=='1') {
    $check_stock = 'checked="checked"';
}
?>
<form name="product_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'product_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'product_id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'download_publish', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'pager_id', 'value'=>'1')); ?>

<!-- 検索条件 -->
<div class="sc_tbl_lstfr wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_STORE_PRODUCT_CODE, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('id'=>'s_product_cd', 'value'=>$product_search['s_product_cd'], 'class'=>'input_120')); ?></td>
  <th><?php _e(SCLNG_STORE_PRODUCT_NAME, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('id'=>'s_product_name', 'value'=>$product_search['s_product_name'], 'class'=>'input_120')); ?></td>
</tr>
<tr>
  <th rowspan=2><?php _e(SCLNG_STORE_PRODUCT_CATEGORY, SC_DOMAIN) ?></th>
  <td rowspan=2>
    <div style="overflow-y:scroll;height:100px;max-height:100px;">
    <?php echo SimpleCartFunctions::HTMLCategorys($categorys, $product_search['s_categorys'], '', 's_'); ?>
    </div>
  </td>
  <th><?php _e(SCLNG_STORE_PRODUCT_PUBLISH, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('type'=>'checkbox','id'=>'s_publish', 'value'=>'1', 'checked'=>$check_publish)); ?><? _e(SCLNG_STORE_PRODUCT_PUBLISH_ONLY, SC_DOMAIN) ?></td>
</tr>
<tr>
  <th><?php _e(SCLNG_STORE_PRODUCT_STOCK, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('type'=>'checkbox','id'=>'s_stock', 'value'=>'1', 'checked'=>$check_stock)); ?><? _e(SCLNG_STORE_PRODUCT_STOCK_ONLY, SC_DOMAIN) ?></td>
</tr>
<tr>
  <td colspan="4" class="center"><?php echo $this->submit(array('value'=>__(SCLNG_SEARCH, SC_DOMAIN), 'onclick'=>"Javascript:actionForm('product_list', 'search_product');")); ?></td>
</tr>
</table>
</div>

<!-- 製品一覧 -->
<div class="sc_list_header wd500">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$product_count)); ?></div>
  <div><?php echo $this->submit(array('value'=>__(SCLNG_DELETE, SC_DOMAIN), 'onclick'=>"Javascript:if(!confirm('" . __(SCLNG_CONFIRM_DELETE_PRODUCT, SC_DOMAIN) . "')){return false};actionForm('product_list', 'delete_product');")); ?></div>
</div>

<div class="sc_tbl_lstfr wd500">
<table class="wd500">
<tr>
  <th class="wd40"><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT_IMAGE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT_PRICE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT_CATEGORY, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT_STOCK, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($product_list)): ?>
<?php foreach ($product_list as $product): ?>
<?php
    $image     = SimpleCartFunctions::ProductImageUrl($product['id'], 1, $product['image_file_url1']);
    $image_url = SimpleCartFunctions::ProductImageUrl($product['id'], 3, $product['image_file_url3']);
    $open_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-product.php?image_url=' . $image_url;

    //画面遷移URL生成
    if ($this->mode==SC_MODE_BP) {
        global $bp;
        $product_url   = site_url('members/' . $this->user_login . '/' . $bp->simplecart_store->slug . '/' . SC_BPID_PRODUCT);
        $variation_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_store->slug . '/' . SC_BPID_VARIATION_RELATION);
    }
    else {
        $product_url   = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_PRODUCT);
        $variation_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_VARIATION_RELATION);
    }

    //販売形態
    if ($product['download_publish']=='0') {
        $download_publish = '';
    }
    else if ($product['download_publish']=='1') {
        $download_publish = __(SCLNG_STORE_PRODUCT_LIST_DOWNLOAD_ONLY, SC_DOMAIN);
    }
    else if ($product['download_publish']=='2') {
        $download_publish = __(SCLNG_STORE_PRODUCT_LIST_DOWNLOAD, SC_DOMAIN);
    }

    //公開／非公開
    $publish = ($product['publish']=='1')?"<font class='sc_blue'>".__(SCLNG_PUBLISH, SC_DOMAIN)."</font>":"<font class='sc_red'>".__(SCLNG_CLOSED, SC_DOMAIN)."</font>";

    //税込／税別
    $notax = ($product['notax']=='1')?__(SCLNG_NOT_INCLUDE_TAX, SC_DOMAIN):__(SCLNG_INCLUDE_TAX, SC_DOMAIN);

    //在庫管理
    $stock = ($product['stock_manage']=='1')?number_format($product['stock']):__(SCLNG_OUTSIDE_STOCK_CONTROLL, SC_DOMAIN);

    //カテゴリー情報
    $categorys = '';
    if (is_array($product['categorys'])) {
        foreach ($product['categorys'] as $category) {
            $cate = get_category($category);
            $categorys .= $cate->name . '<br/>';
        }
    }

    //金額情報（通常販売のみ or ダウンロード販売のみ）
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
            $price = $this->money($product['calc_min_price']) . ' - ' . $this->money($product['calc_max_price']);
            $off   = $this->money($product['calc_min_off_price']) . ' - ' . $this->money($product['calc_max_off_price']);
        }
    }
    if ($product['off']>0) {
        $price = '<span class="sc_price_off">' . $price . '</span><br/>' . $off;
    }

    //金額情報（通常販売＋ダウンロード販売）
    if ($product['download_publish']=='2') {
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
                $dl_price = $this->money($product['calc_download_min_price']) . ' - ' . $this->money($product['calc_download_max_price']);
                $dl_off   = $this->money($product['calc_download_min_off_price']) . ' - ' . $this->money($product['calc_download_min_off_price']);
            }
        }
        if ($product['download_off']>0) {
            $dl_price = '<span class="sc_price_off">' . $dl_price . '</span><br/>' . $dl_off;
        }
        $price = $price . '<br/>' . $dl_price;
    }
?>
<tr>
  <td class="center wd40"><?php echo $this->input(array('type'=>'checkbox', 'id'=>'products_'.$product['id'], 'name'=>'products['.$product['id'].']', 'value'=>$product['id'])); ?></td>
  <td class="sc_list_image"><img style="width:96px;" src="<?php echo $image ?>" onclick="javascript:window.open('<?php echo $open_url ?>', '_blank', 'width=384px,height=384px,resizable=no');" /></td>
  <td>
    <?php echo $this->link(array('href'=>'#', 'value'=>$product['product_cd'], 'onclick'=>"Javascript:document.getElementById('product_id').value=".$product['id'].";submitForm('product_list', '', '".$product_url."');return false;")); ?><?php echo $download_publish ?><br/>
    <?php echo $product['name']; ?><br/>
    <?php echo $publish; ?><br/>
    <?php //製品販売のみ ?>
    <?php if ($product['download_publish']=='0'): ?>
    <?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_PRODUCT_LIST_VARIATION, SC_DOMAIN), 'onclick'=>"Javascript:document.getElementById('product_id').value=".$product['id'].";document.getElementById('download_publish').value='0';submitForm('product_list', '', '".$variation_url."');return false;")); ?>
    <?php //ダウンロード販売のみ ?>
    <?php elseif ($product['download_publish']=='1'): ?>
    <?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_PRODUCT_LIST_VARIATION, SC_DOMAIN), 'onclick'=>"Javascript:document.getElementById('product_id').value=".$product['id'].";document.getElementById('download_publish').value='1';submitForm('product_list', '', '".$variation_url."');return false;")); ?>
    <?php //両方 ?>
    <?php elseif ($product['download_publish']=='2'): ?>
    <?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_PRODUCT_LIST_VARIATION, SC_DOMAIN), 'onclick'=>"Javascript:document.getElementById('product_id').value=".$product['id'].";document.getElementById('download_publish').value='0';submitForm('product_list', '', '".$variation_url."');return false;")); ?><br/>
    <?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_PRODUCT_LIST_VARIATION_DOWNLOAD, SC_DOMAIN), 'onclick'=>"Javascript:document.getElementById('product_id').value=".$product['id'].";document.getElementById('download_publish').value='1';submitForm('product_list', '', '".$variation_url."');return false;")); ?>
    <?php endif; ?>
  </td>
  <td class="wd80"><?php echo $price; ?><br/><?php echo $notax; ?></td>
  <td class="wd80"><?php echo $categorys; ?></td>
  <td class="wd40 center"><?php echo $stock; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</form>
