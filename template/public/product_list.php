<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Public ProductList Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: product_list.php 147731 2009-08-21 05:28:05Z tajima $
 */
$product_count = $this->model['product_count'];
$product_list  = $this->model['product_list'];
$product_cond  = $this->model['product_cond'];

$product_url = site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_PRODUCT_DETAIL);
$store_url   = site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_STORE);
?>
<form name="product_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'product_list_action', 'name'=>'sc_action')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'from', 'value'=>'1')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'id')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'store_id')); ?>
<?php $this->hvars(); ?>
</form>

<div><?php echo $this->link(array('href'=>site_url(SCLNG_PAGE_PUBLIC_TOP), 'value'=>__(SCLNG_BACK, SC_DOMAIN))); ?></div>

<!-- 製品一覧ヘッダー -->
<div class="sc_list_header wd250">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$product_count)); ?></div>
  <?php if(!isset($product_cond['s_product_search'])): ?>
  <div class="sc_sort">
    <?php
        $sort = $product_cond['s_sort'];
        $new_link    = $this->link(array('class'=>($sort==1)?'active':'', 'href'=>'#', 'value'=>__(SCLNG_PUBLIC_PRODUCT_LIST_SORT_BY_NEW, SC_DOMAIN),        'onclick'=>"Javascriot:document.getElementById('s_sort').value='1';submitForm('product_list');return false;"));
        $p_asc_link  = $this->link(array('class'=>($sort==2)?'active':'', 'href'=>'#', 'value'=>__(SCLNG_PUBLIC_PRODUCT_LIST_SORT_BY_PRICE_ASC, SC_DOMAIN),  'onclick'=>"Javascriot:document.getElementById('s_sort').value='2';submitForm('product_list');return false;"));
        $p_desc_link = $this->link(array('class'=>($sort==3)?'active':'', 'href'=>'#', 'value'=>__(SCLNG_PUBLIC_PRODUCT_LIST_SORT_BY_PRICE_DESC, SC_DOMAIN), 'onclick'=>"Javascriot:document.getElementById('s_sort').value='3';submitForm('product_list');return false;"));
    ?>
    <strong><?php _e(SCLNG_PUBLIC_PRODUCT_LIST_SORT_BY, SC_DOMAIN) ?></strong> |
    <?php echo $new_link ?> |
    <?php echo $p_asc_link ?> |
    <?php echo $p_desc_link ?>
  </div>
  <?php endif; ?>
</div>

<!-- 製品一覧 -->
<?php if (is_array($product_list)): ?>
<table class="sc_product_list">
<tr>
<?php $i=0; ?>
<?php foreach ($product_list as $product): ?>
<?php
    $image     = SimpleCartFunctions::ProductImageUrl($product['id'], 1, $product['image_file_url1'], $product['store_id']);

    $product_link = $this->link(array('href'=>'#', 'value'=>$product['name'], 'onclick'=>"Javascript:document.getElementById('id').value='". $product['id'] ."';submitForm('product_list', '', '".$product_url.'/?id='.$product['id']."');return false;"));

    //店舗へのリンク
    $store_link = $this->link(array('href'=>'#', 'value'=>$product['store_name'], 'onclick'=>"Javascript:document.getElementById('store_id').value='". $product['store_id'] ."';submitForm('product_list', '', '".$store_url."');return false;"));

    //新着商品の判定
    $new = '';
    if (SimpleCartFunctions::NewValid($product['regist_date'])) {
        $new = '<span class="sc_new">New</span>';
    }

    //カテゴリー情報
    //$categorys = '';
    //if (is_array($product['categorys'])) {
    //    foreach ($product['categorys'] as $category) {
    //        $cate = get_category($category);
    //        $categorys .= $cate->name . '<br/>';
    //    }
    //}

    //製品説明情報
    //$descriptions = explode(chr(10), $product['description']);
    //if (count($descriptions)>2) { 
    //    $description = $descriptions[0] . "<br/>" . $descriptions[1] . "...";
    //}
    //else {
    //    $description = @implode("<br/>", $descriptions);
    //}

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
            $price = $this->money($product['calc_min_price']) . '-' . $this->money($product['calc_max_price']);
            $off   = $this->money($product['calc_min_off_price']) . '-' . $this->money($product['calc_max_off_price']);
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
                $dl_price = $this->money($product['calc_download_min_price']) . '-' . $this->money($product['calc_download_max_price']);
                $dl_off   = $this->money($product['calc_download_min_off_price']) . '-' . $this->money($product['calc_download_min_off_price']);
            }
        }
        if ($product['download_off']>0) {
            $dl_price = '<span class="sc_price_off">' . $dl_price . '</span><br/>' . $dl_off;
        }
        $price = $price . '<br/>' . $dl_price;
    }

    //販売タイプ
    //$type = '';
    //if ($product['download_publish']=='2') {
    //    $type.= '<img src="' . WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/delivery.gif">';
    //    $type.= '<img style="margin-left:2px;" src="' . WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/download.gif">';
    //}
    //else if ($product['download_publish']=='1') {
    //    $type.= '<img src="' . WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/download.gif">';
    //}
    //else {
    //    $type.= '<img src="' . WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/images/delivery.gif">';
    //}
?>
  <td>
    <div class="image"><img class="sc_list_image" src="<?php echo $image ?>" onclick="Javascript:document.getElementById('id').value='<?php echo $product['id'] ?>';submitForm('product_list', '', '<?php echo $product_url ?>/?id=<?php echo $product['id'] ?>');return false;" /></div>
    <div class="name"><?php echo $new.$product_link; ?></div>
    <div class="price"><?php echo $price; ?></div>
    <div class="store"><?php echo $store_link; ?></div>
  </td>
  <?php if ($i % 4 == 3): ?>
    </tr>
  </table>
  <div class="sc_list_separator"></div>
  <table class="sc_product_list">
    <tr>
  <?php endif ?>
<?php $i++; ?>
<?php endforeach; ?>
</tr>
</table>
<?php endif; ?>
