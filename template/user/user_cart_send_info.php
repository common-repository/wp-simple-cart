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
 * @version     svn:$Id: user_cart_send_info.php 140016 2009-07-28 06:57:23Z tajima $
 */
$cart = $this->model['cart'];
$user = $this->model['user'];
$deliverys = $this->model['deliverys'];
$deliverys_times = $this->model['deliverys_times'];
$paid_methods = $this->model['paid_methods'];
$states = $this->model['states'];
if (is_null($user['send_state'])) {
    $user['send_state'] = SC_DEFAULT_STATE;
}
if (is_null($user['bill_state'])) {
    $user['bill_state'] = SC_DEFAULT_STATE;
}
$default_deliverys = $user['deliverys'];
$default_deliverys_times = $user['deliverys_times'];
$default_paid_methods = $user['paid_methods'];
$frees = $user['frees'];

//エラー判定
$results = $this->model['results'];
if (isset($results)) {
    if (isset($results->errors['send_first_name'])) {
        $err_send_first_name = $results->errors['send_first_name'];
    }
    if (isset($results->errors['send_last_name'])) {
        $err_send_last_name = $results->errors['send_last_name'];
    }
    if (isset($results->errors['send_first_furi'])) {
        $err_send_first_furi = $results->errors['send_first_furi'];
    }
    if (isset($results->errors['send_last_furi'])) {
        $err_send_last_furi = $results->errors['send_last_furi'];
    }
    if (isset($results->errors['send_address'])) {
        $err_send_address = $results->errors['send_address'];
    }
    if (isset($results->errors['send_street'])) {
        $err_send_street = $results->errors['send_street'];
    }
    if (isset($results->errors['send_zip'])) {
        $err_send_zip = $results->errors['send_zip'];
    }
    if (isset($results->errors['send_tel'])) {
        $err_send_tel = $results->errors['send_tel'];
    }
    if (isset($results->errors['send_mobile'])) {
        $err_send_mobile = $results->errors['send_mobile'];
    }
    if (isset($results->errors['bill_first_name'])) {
        $err_bill_first_name = $results->errors['bill_first_name'];
    }
    if (isset($results->errors['bill_last_name'])) {
        $err_bill_last_name = $results->errors['bill_last_name'];
    }
    if (isset($results->errors['bill_first_furi'])) {
        $err_bill_first_furi = $results->errors['bill_first_furi'];
    }
    if (isset($results->errors['bill_last_furi'])) {
        $err_bill_last_furi = $results->errors['bill_last_furi'];
    }
    if (isset($results->errors['bill_address'])) {
        $err_bill_address = $results->errors['bill_address'];
    }
    if (isset($results->errors['bill_street'])) {
        $err_bill_street = $results->errors['bill_street'];
    }
    if (isset($results->errors['bill_zip'])) {
        $err_bill_zip = $results->errors['bill_zip'];
    }
    if (isset($results->errors['bill_tel'])) {
        $err_bill_tel = $results->errors['bill_tel'];
    }
    if (isset($results->errors['bill_mobile'])) {
        $err_bill_mobile = $results->errors['bill_mobile'];
    }
}
?>
<?php //MyCartへ戻る処理用 ?>
<div style="display:none;">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'value'=>'')); ?>
<?php echo $this->submit(array('id'=>'back', 'value'=>__('dummy', SC_DOMAIN))); ?>
</form>
</div>

<?php //ナビゲーション ?>
<div class="sc_navigations">
  <div class="sc_navi_act"><?php _e(SCLNG_NAVI_ADDRESS, SC_DOMAIN) ?></div>
  <div class="sc_navi_arrow"></div>
  <div class="sc_navi"><?php _e(SCLNG_NAVI_CONFIRM, SC_DOMAIN) ?></div>
  <div class="sc_navi_arrow"></div>
  <div class="sc_navi"><?php _e(SCLNG_NAVI_COMPLETE, SC_DOMAIN) ?></div>
</div>

<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'name'=>'sc_action', 'value'=>'confirm')); ?>

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
        if ($c['download']=='1' ) {
            $product_name .= '<div class="dl_product">' . __(SCLNG_DOWNLOAD, SC_DOMAIN) . '</div>';
        }
        else {
            $delivery_flg = true;
        }

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
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_TOTAL, SC_DOMAIN); ?></td>
      <td class="wd100 right"><?php echo $this->money($sub_total); ?></td>
    </tr>
    <?php //希望配送業者情報 ?>
    <?php if($delivery_flg==true): ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_DELIVERY, SC_DOMAIN); ?></td>
      <td class="wd100"><?php echo $this->select(array('id'=>'deliverys_'.$k, 'name'=>'deliverys['.$k.']'), array('list'=>$deliverys[$k], 'default'=>(isset($default_deliverys[$k]))?$default_deliverys[$k]:'')); ?></td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
      <td><?php echo $this->input(array('name'=>'deliverys['.$k.']', 'value'=>'0')); ?></td>
    </tr>
    <?php endif; ?>
    <?php //希望配送時間帯情報 ?>
    <?php if($delivery_flg==true): ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_DELIVERY_TIME, SC_DOMAIN); ?></td>
      <td class="wd100"><?php echo $this->select(array('id'=>'deliverys_times_'.$k, 'name'=>'deliverys_times['.$k.']'), array('list'=>$deliverys_times[$k], 'default'=>(isset($default_deliverys_times[$k]))?$default_deliverys_times[$k]:'')); ?></td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
      <td><?php echo $this->input(array('name'=>'deliverys_times['.$k.']', 'value'=>'0')); ?></td>
    </tr>
    <?php endif; ?>
    <?php //希望支払方法情報 ?>
    <tr>
      <td class="right" colspan="4"><?php _e(SCLNG_USER_CART_PAID_METHOD, SC_DOMAIN); ?></td>
      <td class="wd100"><?php echo $this->select(array('id'=>'paid_methods_'.$k, 'name'=>'paid_methods['.$k.']'), array('list'=>$paid_methods[$k], 'default'=>(isset($default_paid_methods[$k]))?$default_paid_methods[$k]:'')); ?></td>
    </tr>
    <?php //備考 ?>
    <tr>
      <td colspan="5"><?php echo $this->text(array('id'=>'free_'.$k, 'name'=>'frees['.$k.']', 'value'=>$frees[$k], 'rows'=>'6', 'cols'=>'58')); ?></td>
    </tr>
    <?php endif; ?>
    </table>
    </div>
<?php endforeach; ?>
<?php else: ?>
<div class="sc_cart_empty"><?php _e(SCLNG_USER_CART_EMPTY, SC_DOMAIN) ?></div>
<?php endif; ?>

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
      <?php echo $this->input(array('id'=>'send_last_name', 'value'=>$user['send_last_name'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'send_first_name', 'value'=>$user['send_first_name'], 'class'=>'input_120')); ?>
      <?php $this->error($err_send_last_name); ?>
      <?php $this->error($err_send_first_name); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FURI, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'send_last_furi', 'value'=>$user['send_last_furi'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'send_first_furi', 'value'=>$user['send_first_furi'], 'class'=>'input_120')); ?>
      <?php $this->error($err_send_last_furi); ?>
      <?php $this->error($err_send_first_furi); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ZIP, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'send_zip', 'value'=>$user['send_zip'], 'class'=>'input_120')); ?><?php $this->error($err_send_zip); ?>
      <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_SEARCH_ADDRESS, SC_DOMAIN), 'id'=>'send_btn_search_address', 'class'=>'btn_search_address', 'onclick'=>"Javascript:set_address_info('send_');")); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $this->select(array('id'=>'send_state'), array('list'=>$states, 'default'=>$user['send_state'])); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_street', 'value'=>$user['send_street'], 'class'=>'input_280')); ?><?php $this->error($err_send_street); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_address', 'value'=>$user['send_address'], 'class'=>'input_280')); ?><?php $this->error($err_send_address); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_tel', 'value'=>$user['send_tel'], 'class'=>'input_200')); ?><?php $this->error($err_send_tel); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_fax', 'value'=>$user['send_fax'], 'class'=>'input_200')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_MOBILE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_mobile', 'value'=>$user['send_mobile'], 'class'=>'input_200')); ?><?php $this->error($err_send_mobile); ?></td>
  </tr>
</table>
</div>

<?php //ユーザー請求先情報 ?>
<div class="sc_subtitle"><?php _e(SCLNG_USER_CART_HEADER_BILL, SC_DOMAIN); ?></div>
<div>
  <?php echo $this->input(array('type'=>'checkbox', 'id'=>'chk_same_send', 'onclick'=>'Javascript:checkSameSend();')) ?>
  <?php _e(SCLNG_USER_CART_COPY_UNDO, SC_DOMAIN); ?>
  <script type="text/javascript">
  /* <![CDATA[ */
    var tmp_bill_first_name = '';
    var tmp_bill_last_name  = '';
    var tmp_bill_first_furi = '';
    var tmp_bill_last_furi  = '';
    var tmp_bill_address    = '';
    var tmp_bill_street     = '';
    var tmp_bill_state      = '';
    var tmp_bill_zip        = '';
    var tmp_bill_tel        = '';
    var tmp_bill_fax        = '';
    var tmp_bill_mobile     = '';
    function checkSameSend() {
        var obj = document.getElementById('chk_same_send');
        if (obj.checked) {
            tmp_bill_first_name = document.getElementById('bill_first_name').value;
            tmp_bill_last_name  = document.getElementById('bill_last_name').value;
            tmp_bill_first_furi = document.getElementById('bill_first_furi').value;
            tmp_bill_last_furi  = document.getElementById('bill_last_furi').value;
            tmp_bill_address    = document.getElementById('bill_address').value;
            tmp_bill_street     = document.getElementById('bill_street').value;
            tmp_bill_state      = document.getElementById('bill_state').value;
            tmp_bill_zip        = document.getElementById('bill_zip').value;
            tmp_bill_tel        = document.getElementById('bill_tel').value;
            tmp_bill_fax        = document.getElementById('bill_fax').value;
            tmp_bill_mobile     = document.getElementById('bill_mobile').value;
            document.getElementById('bill_first_name').value = document.getElementById('send_first_name').value;
            document.getElementById('bill_last_name').value  = document.getElementById('send_last_name').value;
            document.getElementById('bill_first_furi').value = document.getElementById('send_first_furi').value;
            document.getElementById('bill_last_furi').value  = document.getElementById('send_last_furi').value;
            document.getElementById('bill_address').value    = document.getElementById('send_address').value;
            document.getElementById('bill_street').value     = document.getElementById('send_street').value;
            document.getElementById('bill_state').value      = document.getElementById('send_state').value;
            document.getElementById('bill_zip').value        = document.getElementById('send_zip').value;
            document.getElementById('bill_tel').value        = document.getElementById('send_tel').value;
            document.getElementById('bill_fax').value        = document.getElementById('send_fax').value;
            document.getElementById('bill_mobile').value     = document.getElementById('send_mobile').value;
        }
        else {
            document.getElementById('bill_first_name').value = tmp_bill_first_name;
            document.getElementById('bill_last_name').value  = tmp_bill_last_name;
            document.getElementById('bill_first_furi').value = tmp_bill_first_furi;
            document.getElementById('bill_last_furi').value  = tmp_bill_last_furi;
            document.getElementById('bill_address').value    = tmp_bill_address;
            document.getElementById('bill_street').value     = tmp_bill_street;
            document.getElementById('bill_state').value      = tmp_bill_state;
            document.getElementById('bill_zip').value        = tmp_bill_zip;
            document.getElementById('bill_tel').value        = tmp_bill_tel;
            document.getElementById('bill_fax').value        = tmp_bill_fax;
            document.getElementById('bill_mobile').value     = tmp_bill_mobile;
        }
    }
    /**
     *  住所情報の設定
     */
    function set_address_info(prefix) {
        var zipcode = jQuery("#" + prefix + "zip").val();

        var callback_obj = {
                            "function":set_address_info_callback, 
                            "params":{
                                        "prefix":prefix
                                    },
                            "popup_target_id":prefix + "btn_search_address"
                           };
        sc_zip2AddressInfo(zipcode, callback_obj);
    }

    function set_address_info_callback(address_info, params) {
        var prefix = params["prefix"];
        var street = address_info["state"];
        jQuery("#" + prefix + "state").val(address_info["state"]);
        jQuery("#" + prefix + "street").val(address_info["city"] + address_info["street"]);
    }
  /*]]>*/
  </script>
</div>
<div class="sc_tbl">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_CART_NAME, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'bill_last_name', 'value'=>$user['bill_last_name'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'bill_first_name', 'value'=>$user['bill_first_name'], 'class'=>'input_120')); ?>
      <?php $this->error($err_bill_first_name); ?>
      <?php $this->error($err_bill_last_name); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FURI, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'bill_last_furi', 'value'=>$user['bill_last_furi'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'bill_first_furi', 'value'=>$user['bill_first_furi'], 'class'=>'input_120')); ?>
      <?php $this->error($err_bill_first_furi); ?>
      <?php $this->error($err_bill_last_furi); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ZIP, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'bill_zip', 'value'=>$user['bill_zip'], 'class'=>'input_120')); ?><?php $this->error($err_bill_zip); ?>
      <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_SEARCH_ADDRESS, SC_DOMAIN), 'id'=>'bill_btn_search_address', 'class'=>'btn_search_address', 'onclick'=>"Javascript:set_address_info('bill_');")); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $this->select(array('id'=>'bill_state'), array('list'=>$states, 'default'=>$user['bill_state'])); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_street', 'value'=>$user['bill_street'], 'class'=>'input_280')); ?><?php $this->error($err_bill_street); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_address', 'value'=>$user['bill_address'], 'class'=>'input_280')); ?><?php $this->error($err_bill_address); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_tel', 'value'=>$user['bill_tel'], 'class'=>'input_200')); ?><?php $this->error($err_bill_tel); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_fax', 'value'=>$user['bill_fax'], 'class'=>'input_200')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_CART_MOBILE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_mobile', 'value'=>$user['bill_mobile'], 'class'=>'input_200')); ?><?php $this->error($err_bill_mobile); ?></td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_CONFIRM, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
