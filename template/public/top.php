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
 * @version     svn:$Id: top.php 143626 2009-08-07 12:22:47Z tajima $
 */
$s_categorys = $this->model['s_categorys'];
$s_stores    = $this->model['s_stores'];
$count       = $this->model['count'];

$product_link = site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_PRODUCT_LIST);
?>

<?php //各種商品情報 ?>
<div class="sc_top_lbl"><span class="sc_top_mark"></span><?php _e(SCLNG_PUBLIC_VARIOUS_PRODUCT_LIST, SC_DOMAIN); ?></div>
<div class="sc_top_data"><?php echo $this->link(array('href'=>$product_link . '/?s_product_search=1', 'value'=>__(SCLNG_PUBLIC_PRODUCT_LIST_NEW, SC_DOMAIN))); ?></div>
<div class="sc_top_data"><?php echo $this->link(array('href'=>$product_link . '/?s_product_search=2', 'value'=>__(SCLNG_PUBLIC_PRODUCT_LIST_SALES10, SC_DOMAIN))); ?></div>
<div class="sc_top_data"><?php echo $this->link(array('href'=>$product_link . '/?s_product_search=3', 'value'=>__(SCLNG_PUBLIC_PRODUCT_LIST_QUANTITY10, SC_DOMAIN))); ?></div>
<br/>

<?php //テキスト検索 ?>
<div class="sc_top_lbl"><span class="sc_top_mark"></span><?php _e(SCLNG_PUBLIC_TEXT_SEARCH, SC_DOMAIN); ?></div>
<div class='sc_top_data'>
<form method="post" action="<?php echo $product_link ?>">
<?php _e(SCLNG_PUBLIC_PRODUCT_NAME, SC_DOMAIN) ?> <?php echo $this->input(array('id'=>'s_text')); ?> <?php echo $this->submit(array('id'=>'s_search', 'value'=>__(SCLNG_SEARCH, SC_DOMAIN))) ?><br/>
<?php _e(SCLNG_PUBLIC_PRODUCT_DESCRIPTION, SC_DOMAIN) ?>
<?php echo $this->input(array('type'=>'checkbox', 'id'=>'s_text_description', 'value'=>'1')); ?>
</form>
</div>
<br/>

<?php //カテゴリー一覧 ?>
<div class="sc_top_lbl"><span class="sc_top_mark"></span><?php _e(SCLNG_PUBLIC_CATEGORY_LIST, SC_DOMAIN); ?></div>
<?php if(is_array($s_categorys)): ?>
<?php echo SimpleCartFunctions::HTMLCategorysLink($s_categorys); ?>
<?php else: ?>
<div class="sc_top_nodata"><?php _e(SCLNG_PUBLIC_NOT_FOUND_CATEGORY, SC_DOMAIN); ?></div>
<?php endif; ?>
<br/>

<?php //店舗一覧 ?>
<div class="sc_top_lbl"><span class="sc_top_mark"></span><?php _e(SCLNG_PUBLIC_STORE_LIST, SC_DOMAIN); ?></div>
<?php if(is_array($s_stores)): ?>
<?php foreach ($s_stores as $store): ?>
<div class="sc_top_data"><?php echo $this->link(array('href'=>$product_link . '/?s_store='.$store['ID'], 'value'=>$store['display_name'])); ?> (<?php echo $count[$store['ID']] ?>)</div>
<?php endforeach; ?>
<?php else: ?>
<div class="sc_top_nodata"><?php _e(SCLNG_PUBLIC_NOT_FOUND_STORE, SC_DOMAIN); ?></div>
<?php endif; ?>
<br/>
