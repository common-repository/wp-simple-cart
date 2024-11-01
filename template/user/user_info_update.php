<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile User Info Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: user_info_update.php 140016 2009-07-28 06:57:23Z tajima $
 */
$states = $this->model['states'];
$user = $this->model['user'];
$results = $this->model['results'];
if (is_null($user['send_state'])) {
    $user['send_state'] = SC_DEFAULT_STATE;
}
if (is_null($user['bill_state'])) {
    $user['bill_state'] = SC_DEFAULT_STATE;
}
if (isset($results)) {
    $err_display_name = $results->errors['display_name'];
}
?>
<form name="user" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_action', 'name'=>'sc_action', 'value'=>'confirm')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'id', 'value'=>$user['id'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_email', 'value'=>$user['user_email'])); ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('user','');return false;")); ?></p>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_USER, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'display_name', 'value'=>$user['display_name'], 'class'=>'input_200')); ?><?php $this->error($err_display_name); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $user['user_email']; ?></td>
  </tr>
</table>
</div>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_SEND, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_NAME, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'send_last_name', 'value'=>$user['send_last_name'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'send_first_name', 'value'=>$user['send_first_name'], 'class'=>'input_120')); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_FURI, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'send_last_furi', 'value'=>$user['send_last_furi'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'send_first_furi', 'value'=>$user['send_first_furi'], 'class'=>'input_120')); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ZIP, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'send_zip', 'value'=>$user['send_zip'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_SEARCH_ADDRESS, SC_DOMAIN), 'id'=>'send_btn_search_address', 'class'=>'btn_search_address', 'onclick'=>"Javascript:set_address_info('send_');")); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $this->select(array('id'=>'send_state'), array('list'=>$states, 'default'=>$user['send_state'])); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_street', 'value'=>$user['send_street'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_address', 'value'=>$user['send_address'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_tel', 'value'=>$user['send_tel'], 'class'=>'input_200')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_fax', 'value'=>$user['send_fax'], 'class'=>'input_200')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_MOBILE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'send_mobile', 'value'=>$user['send_mobile'], 'class'=>'input_200')); ?></td>
  </tr>
</table>
</div>

<div class="sc_subtitle_updt"><?php _e(SCLNG_USER_HEADER_BILL, SC_DOMAIN); ?></div>
<div class="sc_tbl_updt">
<table>
  <tr>
    <th><?php _e(SCLNG_USER_NAME, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'bill_last_name', 'value'=>$user['bill_last_name'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'bill_first_name', 'value'=>$user['bill_first_name'], 'class'=>'input_120')); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_FURI, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'bill_last_furi', 'value'=>$user['bill_last_furi'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('id'=>'bill_first_furi', 'value'=>$user['bill_first_furi'], 'class'=>'input_120')); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ZIP, SC_DOMAIN) ?></th>
    <td>
        <?php echo $this->input(array('id'=>'bill_zip', 'value'=>$user['bill_zip'], 'class'=>'input_120')); ?>
        <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_SEARCH_ADDRESS, SC_DOMAIN),  'id'=>'bill_btn_search_address', 'class'=>'btn_search_address', 'onclick'=>"Javascript:set_address_info('bill_');")); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $this->select(array('id'=>'bill_state'), array('list'=>$states, 'default'=>$user['bill_state'])); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_street', 'value'=>$user['bill_street'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_address', 'value'=>$user['bill_address'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_tel', 'value'=>$user['bill_tel'], 'class'=>'input_200')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_fax', 'value'=>$user['bill_fax'], 'class'=>'input_200')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_USER_MOBILE, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'bill_mobile', 'value'=>$user['bill_mobile'], 'class'=>'input_200')); ?></td>
  </tr>
</table>
</div>

<p class="submit"><?php echo $this->submit(array('value'=>__(SCLNG_CONFIRM, SC_DOMAIN) . " &raquo;")); ?></p>
</form>
<script type="text/javascript">
/* <![CDATA[ */
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
