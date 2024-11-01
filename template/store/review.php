<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Review Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: review.php 143340 2009-08-06 11:47:12Z tajima $
 */
$categorys = $this->model['categorys'];
$recommend = $this->model['recommend'];
$review_search = $this->model['review_search'];
$review_count = $this->model['review_count'];
$review_list = $this->model['review_list'];

//画面遷移URL生成
$product_url = site_url(SCLNG_PAGE_PUBLIC_TOP . '/' . SCLNG_PAGE_PUBLIC_PRODUCT_DETAIL);
?>
<form name="review_list" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'review_list_action', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'review_id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'id', 'value'=>'')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'from', 'value'=>'3')); ?>

<!-- 検索条件 -->
<div class="sc_tbl_lstfr wd500">
<table class="wd500">
<tr>
  <th><?php _e(SCLNG_STORE_REVIEW_RECOMMEND, SC_DOMAIN) ?></th>
  <td><?php echo $this->select(array('id'=>'s_review_recommend'), array('list'=>array(''=>'')+$recommend, 'default'=>$review_search['s_review_recommend'])) ?></td>
  <th><?php _e(SCLNG_STORE_REVIEW_COMMENT, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('id'=>'s_review_comment', 'value'=>$review_search['s_review_comment'], 'class'=>'input_120')); ?></td>
</tr>
<tr>
  <th><?php _e(SCLNG_STORE_REVIEW_USER, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('id'=>'s_review_user', 'value'=>$review_search['s_review_user'], 'class'=>'input_120')); ?></td>
  <th><?php _e(SCLNG_STORE_PRODUCT_CODE, SC_DOMAIN) ?></th>
  <td><?php echo $this->input(array('id'=>'s_product_cd', 'value'=>$review_search['s_product_cd'], 'class'=>'input_120')); ?></td>
</tr>
<tr>
  <th><?php _e(SCLNG_STORE_PRODUCT_CATEGORY, SC_DOMAIN) ?></th>
  <td colspan=3>
    <div style="overflow-y:scroll;height:100px;max-height:100px;">
    <?php echo SimpleCartFunctions::HTMLCategorys($categorys, $review_search['s_categorys'], '', 's_'); ?>
    </div>
  </td>
</tr>
<tr>
  <td colspan="4" class="center"><?php echo $this->submit(array('value'=>__(SCLNG_SEARCH, SC_DOMAIN), 'onclick'=>"Javascript:actionForm('review_list', 'search_review');")); ?></td>
</tr>
</table>
</div>

<!-- レビュー一覧 -->
<div class="sc_list_header wd500">
  <div class="sc_page"><?php echo $this->page(array('total-count'=>$review_count)); ?></div>
</div>

<div class="sc_review_list wd500">
<table class="wd500">
<tr>
  <th class="wd40"><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_REVIEW_REGIST_DATE, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_PRODUCT, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_REVIEW_USER, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_REVIEW_RECOMMEND, SC_DOMAIN); ?></th>
  <th><?php _e(SCLNG_STORE_REVIEW_COMMENT, SC_DOMAIN); ?></th>
</tr>
<?php if (is_array($review_list)): ?>
<?php foreach ($review_list as $review): ?>
<?php
    $product_link = $this->link(array('href'=>'#', 'value'=>$review['product_cd'], 'onclick'=>"Javascript:document.getElementById('id').value='". $review['product_id'] ."';submitForm('review_list', '', '".$product_url.'/?id='.$product['id']."');return false;"));
?>
<tr>
  <td class="wd40 center"><?php echo $this->link(array('href'=>'#', 'value'=>SCLNG_DELETE, 'onclick'=>"Javascript:if(confirm('".__(SCLNG_CONFIRM_DELETE_REVIEW, SC_DOMAIN)."')){document.getElementById('review_id').value='".$review['id']."';submitForm('review_list', 'delete_review');}return false;")); ?></td>
  <td class="wd80 center"><?php echo $review['regist_date']; ?></td>
  <td><?php echo $product_link; ?><br/><span class="name"><?php echo $review['product_name']; ?></span></td>
  <td class="wd40"><?php echo $review['display_name']; ?></td>
  <td class="wd40 center"><?php echo $review['recommend']; ?></td>
  <td><?php echo $review['comment']; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</form>
