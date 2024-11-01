<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Cart View Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_cart_confirm.php 140016 2009-07-28 06:57:23Z tajima $
 */
$cart = $this->model['cart'];
$user = $this->model['user'];
$states = $this->model['states'];
$deliverys = $this->model['deliverys'];
$deliverys_cost = $this->model['deliverys_cost'];
$deliverys_value = $this->model['deliverys_value'];
$deliverys_times = $this->model['deliverys_times'];
$paid_methods = $this->model['paid_methods'];
$commissions = $this->model['commissions'];

$selected_deliverys = $user['deliverys'];
$selected_deliverys_times = $user['deliverys_times'];
$selected_paid_methods = $user['paid_methods'];
$frees = $user['frees'];
?>
<?php //Sender/Billing Addressへ戻る処理用 ?>
<div style="display:none;">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'value'=>'back_send_address')); ?>
<?php $this->hvars() ?>
<?php echo $this->submit(array('id'=>'back', 'value'=>__('dummy', SC_DOMAIN))); ?>
</form>
</div>

<?php //ナビゲーション ?>
<div class="sc_navigations">
  <div class="sc_navi"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_NAVI_ADDRESS, SC_DOMAIN), 'onclick'=>"Javascript:document.getElementById('back').click();return false;")); ?></div>
  <div class="sc_navi_arrow"></div>
  <div class="sc_navi_act"><?php _e(SCLNG_NAVI_CONFIRM, SC_DOMAIN) ?></div>
  <div class="sc_navi_arrow"></div>
  <div class="sc_navi"><?php _e(SCLNG_NAVI_COMPLETE, SC_DOMAIN) ?></div>
</div>

<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'value'=>'complete')); ?>

<?php //店舗別カート一覧 ?>
<?php if (is_array($cart)): ?>
<?php $total = 0; ?>
<?php //店舗ループ ?>
<?php foreach ($cart as $k=>$s): ?>
    <?php $delivery_flg = false; ?>
    <?php $sub_total = 0; ?>
    <div class="sc_cart_store_lbl"><?php echo $s['store_name'] ?></div>
    <div class="sc_cart_list wd500">
    <table class="wd500">
    <tr>
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
        else {
            $delivery_flg = true;
        }

        //金額情報
        $price = $c['fixed_price'];
        if ($c['off'] > 0) {
            $price = $c['fixed_off_price'];
        }
        $cost = $c['su'] * $price;
        $total += $cost;
        $sub_total += $cost;
    ?>
    <tr>
      <td class="image"><img class="sc_list_image" src="<?php echo $image ?>" onclick="javascript:window.open('<?php echo $open_url ?>', '_blank', 'width=384px,height=384px,resizable=no');" /></td>
      <td class="name"><?php echo $product_name; ?></td>
      <td class="wd60 right"><?php echo $this->money($price); ?></td>
      <td class="wd60 quantity"><?php echo number_format($c['su']); ?></td>
      <td class="wd60 right"><?php echo $this->money($cost); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php //配送料 ?>
    <?php if ($deliverys_cost[$k]>0): ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_DELIVERY_COST, SC_DOMAIN); ?></td>
      <td class="wd100 right sc_blue">
        <?php
            $cost = $deliverys_cost[$k];
            $disp_cost = $this->money($cost);
            //ここまでの小計で判定
            if ($s['price_limit'] > 0) {
                if ($sub_total > $s['price_limit']) {
                    //配送料無料
                    $cost = 0;
                    $disp_cost = '<span class="sc_red bold">' . __(SCLNG_USER_CART_DELIVERY_FREE, SC_DOMAIN) . '</span>';
                }
            }
            $total += $cost;
            $sub_total += $cost;
        ?>
        <?php echo $disp_cost; ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_cost_'.$k, 'name'=>'deliverys_cost['.$k.']', 'value'=>$cost)); ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_value_'.$k, 'name'=>'deliverys_value['.$k.']', 'value'=>$deliverys_value[$k])); ?>
      </td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
      <td>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_cost_'.$k, 'name'=>'deliverys_cost['.$k.']', 'value'=>'0')); ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_value_'.$k, 'name'=>'deliverys_value['.$k.']', 'value'=>$deliverys_value[$k])); ?>
      </td>
    </tr>
    <?php endif; ?>
    <?php //手数料 ?>
    <?php if ($commissions[$k]>0): ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_COMMISSION_COST, SC_DOMAIN); ?></td>
      <td class="wd100 right sc_blue">
        <?php
            $cost = $commissions[$k];
            $total += $cost;
            $sub_total += $cost;
        ?>
        <?php echo $this->money($cost); ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'commissions_'.$k, 'name'=>'commissions['.$k.']', 'value'=>$cost)); ?>
      </td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
      <td><?php echo $this->input(array('type'=>'hidden', 'id'=>'commissions_'.$k, 'name'=>'commissions['.$k.']', 'value'=>'0')); ?></td>
    </tr>
    <?php endif; ?>
    <?php //合計金額 ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_TOTAL, SC_DOMAIN); ?></td>
      <td class="wd100 right sc_blue">
        <?php echo $this->money($sub_total); ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'totals_'.$k, 'name'=>'totals['.$k.']', 'value'=>$sub_total)); ?>
      </td>
    </tr>
    <?php //希望配送業者情報 ?>
    <?php if($delivery_flg==true): ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_DELIVERY, SC_DOMAIN); ?></td>
      <td class="wd100">
        <?php echo $deliverys[$k][$selected_deliverys[$k]]; ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_'.$k, 'name'=>'deliverys['.$k.']', 'value'=>$selected_deliverys[$k])); ?>
      </td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
      <td><?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_'.$k, 'name'=>'deliverys['.$k.']', 'value'=>$selected_deliverys[$k])); ?></td>
    </tr>
    <?php endif; ?>
    <?php //希望配送時間帯情報 ?>
    <?php if($delivery_flg==true): ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_DELIVERY_TIME, SC_DOMAIN); ?></td>
      <td class="wd100">
        <?php echo $deliverys_times[$k][$selected_deliverys_times[$k]]; ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_times_'.$k, 'name'=>'deliverys_times['.$k.']', 'value'=>$selected_deliverys_times[$k])); ?>
      </td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
      <td><?php echo $this->input(array('type'=>'hidden', 'id'=>'deliverys_times_'.$k, 'name'=>'deliverys_times['.$k.']', 'value'=>$selected_deliverys[$k])); ?></td>
    </tr>
    <?php endif; ?>
    <?php //希望支払方法情報 ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_PAID_METHOD, SC_DOMAIN); ?></td>
      <td class="wd100">
        <?php echo $paid_methods[$k][$selected_paid_methods[$k]]; ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'paid_methods_'.$k, 'name'=>'paid_methods['.$k.']', 'value'=>$selected_paid_methods[$k])); ?>
      </td>
    </tr>
    <?php //備考 ?>
    <?php if ($frees[$k]!=''): ?>
    <tr>
      <td colspan="5">
        <div class="free"><?php echo nl2br($frees[$k]); ?></div>
        <div style="display:none;">
        <?php echo $this->text(array('id'=>'free_'.$k, 'name'=>'frees['.$k.']', 'value'=>$frees[$k])); ?>
        </div>
      </td>
    </tr>
    <?php endif; ?>
    <?php endif; ?>
    </table>
    </div>
<?php endforeach; ?>
<?php else: ?>
<div class="sc_cart_empty"><?php _e(SCLNG_USER_CART_EMPTY, SC_DOMAIN) ?></div>
<?php endif; ?>

<div class="sc_cart_store_lbl"><?php _e(SCLNG_USER_CART_HEADER_TOTAL, SC_DOMAIN); ?></div>
<div class="sc_cart_list wd500">
<table class="wd500">
<tr>
  <td class="right"><?php _e(SCLNG_USER_CART_TOTAL_COST, SC_DOMAIN); ?></td>
  <td class="wd100 right sc_blue"><?php echo $this->money($total); ?></td>
</tr>
</table>
</div>

<?php //ユーザー基本情報 ?>
<div class="sc_subtitle"><?php _e(SCLNG_USER_CART_HEADER_USER, SC_DOMAIN); ?></div>
<div class="sc_tbl">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_CART_NAME, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['display_name']; ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'display_name', 'value'=>$user['display_name'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_EMAIL, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['user_email']; ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'user_email', 'value'=>$user['user_email'])); ?>
    </td>
  </tr>
</table>
</div>

<?php //ユーザー送付先情報 ?>
<div class="sc_subtitle"><?php _e(SCLNG_USER_CART_HEADER_SEND, SC_DOMAIN); ?></div>
<div class="sc_tbl">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_CART_NAME, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_last_name'] ?> <?php echo $user['send_first_name'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_first_name', 'value'=>$user['send_first_name'])); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_last_name', 'value'=>$user['send_last_name'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FURI, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_last_furi'] ?> <?php echo $user['send_first_furi'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_first_furi', 'value'=>$user['send_first_furi'])); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_last_furi', 'value'=>$user['send_last_furi'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ZIP, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_zip'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_zip', 'value'=>$user['send_zip'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STATE, SC_DOMAIN) ?></th>
    <td>
    <?php echo $states[$user['send_state']] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_state', 'value'=>$user['send_state'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STREET, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_street'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_street', 'value'=>$user['send_street'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ADDRESS, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_address'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_address', 'value'=>$user['send_address'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_TEL, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_tel'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_tel', 'value'=>$user['send_tel'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FAX, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_fax'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_fax', 'value'=>$user['send_fax'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_MOBILE, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['send_mobile'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'send_mobile', 'value'=>$user['send_mobile'])); ?>
    </td>
  </tr>
</table>
</div>

<?php //ユーザー請求先情報 ?>
<div class="sc_subtitle"><?php _e(SCLNG_USER_CART_HEADER_BILL, SC_DOMAIN); ?></div>
<div class="sc_tbl">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_CART_NAME, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_last_name'] ?> <?php echo $user['bill_first_name'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_first_name', 'value'=>$user['bill_first_name'])); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_last_name', 'value'=>$user['bill_last_name'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FURI, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_last_furi'] ?> <?php echo $user['bill_first_furi'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_first_furi', 'value'=>$user['bill_first_furi'])); ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_last_furi', 'value'=>$user['bill_last_furi'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ZIP, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_zip'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_zip', 'value'=>$user['bill_zip'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STATE, SC_DOMAIN) ?></th>
    <td>
    <?php echo $states[$user['bill_state']] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_state', 'value'=>$user['bill_state'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STREET, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_street'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_street', 'value'=>$user['bill_street'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ADDRESS, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_address'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_address', 'value'=>$user['bill_address'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_TEL, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_tel'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_tel', 'value'=>$user['bill_tel'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FAX, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_fax'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_fax', 'value'=>$user['bill_fax'])); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_MOBILE, SC_DOMAIN) ?></th>
    <td>
    <?php echo $user['bill_mobile'] ?>
    <?php echo $this->input(array('type'=>'hidden', 'id'=>'bill_mobile', 'value'=>$user['bill_mobile'])); ?>
    </td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_COMPLETE, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
