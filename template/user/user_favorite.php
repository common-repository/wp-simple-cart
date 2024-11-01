<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Favorite Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_favorite.php 143340 2009-08-06 11:47:12Z tajima $
 */
$favorite_count = $this->model['favorite_count'];
$favorite_list  = $this->model['favorite_list'];

$product_url = site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_PRODUCT_DETAIL);
?>
<form name="favorite_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'favorite_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'favorite_id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'from', 'value'=>'2')); ?>

<!-- お気に入り一覧 -->
<div class="sc_list_header wd400">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$favorite_count)); ?></div>
</div>

<div class="sc_favorite_list wd400">
<table class="wd400">
<tr>
  <th colspan="2"><?php _e(SCLNG_USER_FAVORITE_PRODUCT, SC_DOMAIN); ?></th>
  <th class="wd40"><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($favorite_list)): ?>
<?php foreach ($favorite_list as $favorite): ?>
<?php
    $image = SimpleCartFunctions::ProductImageUrl($favorite['id'], 1, $favorite['image_file_url1'], $favorite['store_id']);
    $product_link = $this->link(array('href'=>'#', 'value'=>$favorite['product_cd'], 'onclick'=>"Javascript:document.getElementById('id').value='". $favorite['id'] ."';submitForm('favorite_list', '', '".$product_url.'/?id='.$product['id']."');return false;"));
    $image_link = $this->link(array('href'=>'#', 'value'=>'<img class="sc_list_image" src="' . $image . '" />', 'onclick'=>"Javascript:document.getElementById('id').value='". $favorite['id'] ."';submitForm('favorite_list', '', '".$product_url.'/?id='.$product['id']."');return false;"));
    $delete_link = $this->link(array('href'=>'#', 'value'=>SCLNG_USER_FAVORITE_DELETE, 'onclick'=>"Javascript:if(confirm('".__(SCLNG_CONFIRM_DELETE_FAVORITE, SC_DOMAIN)."')){document.getElementById('favorite_id').value='".$favorite['favorite_id']."';submitForm('favorite_list', 'delete_favorite');}return false;"));
?>
<tr>
  <td class="image"><?php echo $image_link; ?></td>
  <td><?php echo $product_link; ?><br/><span class="name"><?php echo $favorite['name']; ?></span></td>
  <td class="wd40 center"><?php echo $delete_link ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</form>
