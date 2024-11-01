<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Cart Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_cart.php 143626 2009-08-07 12:22:47Z tajima $
 */
$cart = $this->model['cart'];
?>
<?php //店舗別カート一覧 ?>
<?php if (is_array($cart)): ?>
<?php $total = 0; ?>
<?php //店舗ループ ?>
<?php foreach ($cart as $s): ?>
    <?php $sub_total = 0; ?>
    <div class="sc_cart_store_lbl"><?php echo $s['store_name'] ?></div>

    <?php //配送料金無料オプション情報表示 ?>
    <?php if($s['delivery_fee_flg']=='1'): ?>
    <?php if($s['price_limit']>0): ?>
    <div class="sc_cart_store_info">
    <div class="sc_cart_store_info_title"><?php _e(SCLNG_USER_CART_STORE_INFO, SC_DOMAIN); ?></div>
    <?php _e(sprintf(SCLNG_USER_CART_DELIVERY_LIMIT_DESC, $this->money($s['price_limit'])), SC_DOMAIN); ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <div class="sc_cart_list wd500">
    <table class="wd500">
    <tr>
      <th><?php _e(SCLNG_DELETE, SC_DOMAIN); ?></th>
      <th><?php _e(SCLNG_USER_CART_PRODUCT_IMAGE, SC_DOMAIN); ?></th>
      <th><?php _e(SCLNG_USER_CART_PRODUCT_NAME, SC_DOMAIN); ?></th>
      <th><?php _e(SCLNG_USER_CART_PRODUCT_PRICE, SC_DOMAIN); ?></th>
      <th><?php _e(SCLNG_USER_CART_QUANTITY, SC_DOMAIN); ?></th>
      <th><?php _e(SCLNG_USER_CART_SUB_TOTAL, SC_DOMAIN); ?></th>
    </tr>
    <?php if (is_array($s['data'])): ?>
    <?php //製品ループ ?>
    <?php foreach ($s['data'] as $c): ?>
    <?php
        $image     = SimpleCartFunctions::ProductImageUrl($c['product_id'], 1, $c['image_file_url1'], $c['store_id']);
        $image_url = SimpleCartFunctions::ProductImageUrl($c['product_id'], 3, $c['image_file_url3'], $c['store_id']);
        $open_url  = WP_PLUGIN_URL . '/' . SC_PLUGIN_NAME . '/request/simple-cart-product.php?image_url=' . $image_url;

        //製品名
        $product_name = $c['name'];
        $variation_name = '';
        if (isset($c['variation_names'])) {
            foreach ($c['variation_names'] as $variation_names) {
                $variation_name .= $variation_names['variation'] . ':' . $variation_names['value'] . '<br/>';
            }
            $product_name .= '<br/>' . $variation_name;
        }
        if ($c['download']=='1') {
            $product_name .= '<div class="dl_product">' . __(SCLNG_DOWNLOAD, SC_DOMAIN) . '</div>';
        }

        //金額関連
        $price = $c['fixed_price'];
        if ($c['off'] > 0) {
            $price = $c['fixed_off_price'];
        }
        $cost = $c['su'] * $price;
        $total += $cost;
        $sub_total += $cost;
    ?>
    <tr>
      <td class="center wd40">
        <div style="text-align:center;width:100%;">
          <form style="text-align:center;margin:0px;padding:0px;" name="delete_<?php echo $c['product_id'] ?>_<?php echo $c['pricestock_id'] ?>" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'id'=>'delete_'.$c['product_id'].'_'.$c['pricestock_id'].'_action', 'value'=>'')); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'product_id', 'value'=>$c['product_id'])); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'store_id', 'value'=>$c['store_id'])); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'pricestock_id', 'value'=>$c['pricestock_id'])); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'download', 'value'=>$c['download'])); ?>
          <?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_DELETE, SC_DOMAIN), 'onclick'=>"Javascript:if(confirm('".__(SCLNG_CONFIRM_DELETE_CART, SC_DOMAIN)."')){submitForm('delete_".$c['product_id']."_".$c['pricestock_id']."', 'delete_cart');}return false;")); ?>
          </form>
        </div>
      </td>
      <td class="image"><img class="sc_list_image" src="<?php echo $image ?>" onclick="javascript:window.open('<?php echo $open_url ?>', '_blank', 'width=384px,height=384px,resizable=no');" /></td>
      <td class="name"><?php echo $product_name; ?></td>
      <td class="wd60 right"><?php echo $this->money($price); ?></td>
      <td class="wd60 quantity">
        <?php echo number_format($c['su']); ?>
        <div style="text-align:center;width:100%;">
          <form style="text-align:center;margin:0px;padding:0px;" name="quantity_<?php echo $c['product_id'] ?>_<?php echo $c['pricestock_id'] ?>" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'id'=>'quantity_'.$c['product_id'].'_'.$c['pricestock_id'].'_action', 'value'=>'')); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'product_id', 'value'=>$c['product_id'])); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'store_id', 'value'=>$c['store_id'])); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'pricestock_id', 'value'=>$c['pricestock_id'])); ?>
          <?php echo $this->input(array('type'=>'hidden', 'name'=>'download', 'value'=>$c['download'])); ?>
          <?php echo $this->submit(array('class'=>'sc_quantity_up',   'onclick'=>"Javascript:actionForm('quantity_".$c['product_id'].'_'.$c['pricestock_id']."', 'change_quantity_up');")); ?>
          <?php echo $this->submit(array('class'=>'sc_quantity_down', 'onclick'=>"Javascript:actionForm('quantity_".$c['product_id'].'_'.$c['pricestock_id']."', 'change_quantity_down');")); ?>
          </form>
        </div>
      </td>
      <td class="wd60 right"><?php echo $this->money($cost); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <td class="right" colspan="5"><?php _e(SCLNG_USER_CART_TOTAL, SC_DOMAIN); ?></td>
      <td class="wd60 right"><?php echo $this->money($sub_total); ?></td>
    </tr>
    <?php endif; ?>
    </table>
    </div>
<?php endforeach; ?>
<?php else: ?>
<div class="sc_cart_empty"><?php _e(SCLNG_USER_CART_EMPTY, SC_DOMAIN) ?></div>
<?php endif; ?>

<?php if (is_array($cart)): ?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'value'=>'update_send_address')); ?>
<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_BUY, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
<?php endif; ?>
