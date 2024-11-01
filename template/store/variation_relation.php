<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Variation Relation Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: variation_relation.php 143626 2009-08-07 12:22:47Z tajima $
 */
$product = $this->model['product'];
$download_publish = $this->model['download_publish'];
$variations = $this->model['variations'];
$variation_values = $this->model['variation_values'];
$product_variations = $this->model['product_variations'];
$product_variation_values = $this->model['product_variation_values'];
$results = $this->model['results'];
$from_product = $this->model['from_product'];

$image = SimpleCartFunctions::ProductImageUrl($product['id'], 1, $product['image_file_url1']);

if ($this->mode==SC_MODE_BP) {
    global $bp;
    $back_product_list_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_product->slug . '/' . SC_BPID_PRODUCT_LIST);
    $back_product_url = site_url('members/' . $this->user_login . '/' . $bp->simplecart_product->slug . '/' . SC_BPID_PRODUCT);
}
else {
    $back_product_list_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_PRODUCT_LIST);
    $back_product_url = site_url(SCLNG_PAGE_STORE_INFO . '/' . SCLNG_PAGE_STORE_PRODUCT);
}
?>
<form name="variation_relation" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'variation_relation_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'name'=>'product_id', 'value'=>$product['id'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'name'=>'download_publish', 'value'=>$download_publish)); ?>
<?php $this->hvars(); ?>

<?php if ($from_product=='1'): ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_STORE_VARIATION_RELATION_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('variation_relation', '', '".$back_product_list_url."');return false;")); ?></p>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('variation_relation', '', '".$back_product_url."');return false;")); ?></p>
<?php else: ?>
<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('variation_relation', '', '".$back_product_list_url."');return false;")); ?></p>
<?php endif; ?>


<?php //製品情報 ?>
<div class="sc_tbl_lstfr wd400">
<table class="wd400">
<tr>
  <th><?php _e(SCLNG_STORE_PRODUCT_IMAGE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT_PRICE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT_STOCK, SC_DOMAIN); ?></th>
</tr>
<?php
    $image     = SimpleCartFunctions::ProductImageUrl($product['id'], 1, $product['image_file_url1']);
    $image_url = SimpleCartFunctions::ProductImageUrl($product['id'], 3, $product['image_file_url3']);
    $open_url = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-product.php?image_url=' . $image_url;

    //公開／非公開
    $publish = ($product['publish']=='1')?"<font class='sc_blue'>".__(SCLNG_PUBLISH, SC_DOMAIN)."</font>":"<font class='sc_red'>".__(SCLNG_CLOSED, SC_DOMAIN)."</font>";

    //税込／税別
    $notax = ($product['notax']=='1')?__(SCLNG_NOT_INCLUDE_TAX, SC_DOMAIN):__(SCLNG_INCLUDE_TAX, SC_DOMAIN);

    //在庫管理
    $stock = ($product['stock_manage']=='1')?__(SCLNG_STORE_VARIATION_RELATION_STOCK_CONTROLL, SC_DOMAIN):__(SCLNG_STORE_VARIATION_RELATION_OUTSIDE_STOCK_CONTROLL, SC_DOMAIN);

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
  <td class="sc_list_image"><img style="width:96px;" src="<?php echo $image ?>" onclick="javascript:window.open('<?php echo $open_url ?>', '_blank', 'width=384px,height=384px,resizable=no');" /></td>
  <td>
    <?php echo $product['product_cd']; ?><br/>
    <?php echo $product['name']; ?><br/>
    <?php echo $publish; ?>
  </td>
  <td class="wd80"><?php echo $price; ?><br/><?php echo $notax; ?></td>
  <td class="wd40 center"><?php echo $stock; ?></td>
</tr>
</table>
</div>

<?php //規格情報 ?>
<?php if (is_array($variations)): ?>
<div class="sc_tbl_lstfr wd120">
<table class="wd120">
<tr>
  <th><?php _e(SCLNG_STORE_VARIATION_RELATION_VARIATION, SC_DOMAIN); ?></th>
</tr>
<tr>
  <td>
  <?php foreach ($variations as $variation): ?>
  <?php
      //規格選択状態の確認
      $variation_checked = '';
      $vari_box = array();
      $vari_box['type']  = 'checkbox';
      $vari_box['id']    = 'product_variations_'.$variation['id'];
      $vari_box['name']  = 'product_variations['.$variation['id'].']';
      $vari_box['value'] = $variation['id'];
      if (isset($product_variations[$variation['id']])) {
          $vari_box['checked'] = 'checked';
      }
      $vari_box['onclick'] = "Javascript:submitForm('variation_relation', 'checked_variation');";
  ?>
  <?php echo $this->input($vari_box) . ' ' . $variation['name'] . '<br/>' ?>
  <?php endforeach; ?>
  </td>
</tr>
</table>
</div>

<div class="sc_tbl_lstfr wd500">
<table class="wd500">
<tr>
  <!--<th class="wd30"><?php _e(SCLNG_STORE_VARIATION_RELATION_VISIBLE, SC_DOMAIN); ?></th>-->
  <th><?php _e(SCLNG_STORE_VARIATION_RELATION_VALUES, SC_DOMAIN); ?></th>
  <?php if($product['stock_manage']=='1'): ?>
  <th class="wd80"><?php _e(SCLNG_STORE_VARIATION_RELATION_STOCK, SC_DOMAIN); ?></th>
  <?php endif; ?>
  <th class="wd80"><?php _e(SCLNG_STORE_VARIATION_RELATION_PRICE, SC_DOMAIN); ?></th>
</tr>
<?php SimpleCartFunctions::HTMLVariationValues(null, $results, $product, $product_variations, $product_variation_values, $variation_values); ?>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;", 'onclick'=>"Javascript:submitForm('variation_relation', 'save_variation_relation');")); ?></p>
<?php else: ?>
<?php _e(SCLNG_STORE_VARIATION_RELATION_NOT_FOUND, SC_DOMAIN); ?>
<?php endif; ?>
</form>
