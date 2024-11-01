<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store User Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_info_update.php 140016 2009-07-28 06:57:23Z tajima $
 */
$states = $this->model['states'];
$store = $this->model['store_info'];
$results = $this->model['results'];
if (isset($results)) {
    $err_name = $results->errors['user_name'];
}
?>
<form name="store" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'store_action', 'name'=>'sc_action', 'value'=>'confirm')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'ID', 'value'=>$store['ID'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'user_login', 'value'=>$store['user_login'])); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'email', 'value'=>$store['email'])); ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('store','');return false;")); ?></p>

<div class="sc_tbl_updt">
<table>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'display_name', 'value'=>$store['display_name'], 'class'=>'input_120')); ?><?php $this->error($err_name); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_LOGIN, SC_DOMAIN) ?></th>
    <td><?php echo $store['user_login']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_EMAIL, SC_DOMAIN) ?></th>
    <td><?php echo $store['email']; ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_URL, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'url', 'value'=>$store['url'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_ZIP, SC_DOMAIN) ?></th>
    <td>
      <?php echo $this->input(array('id'=>'zip', 'value'=>$store['zip'], 'class'=>'input_120')); ?>
      <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_SEARCH_ADDRESS, SC_DOMAIN), 'id'=>'btn_search_address', 'class'=>'btn_search_address', 'onclick'=>"Javascript:set_address_info();")); ?>
    </td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_STATE, SC_DOMAIN) ?></th>
    <td><?php echo $this->select(array('id'=>'state'), array('list'=>$states, 'default'=>$store['state'])); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_STREET, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'street', 'value'=>$store['street'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_ADDRESS, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'address', 'value'=>$store['address'], 'class'=>'input_280')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_TEL, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'tel', 'value'=>$store['tel'], 'class'=>'input_120')); ?></td>
  </tr>
  <tr>
    <th><?php _e(SCLNG_STORE_USER_FAX, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'fax', 'value'=>$store['fax'], 'class'=>'input_120')); ?></td>
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
    function set_address_info() {
        var zipcode = jQuery("#zip").val();

        var callback_obj = {
                            "function":set_address_info_callback, 
                            "popup_target_id":"btn_search_address"
                           };
        sc_zip2AddressInfo(zipcode, callback_obj);
    }

    function set_address_info_callback(address_info) {
        var street = address_info["state"];
        jQuery("#state").val(address_info["state"]);
        jQuery("#street").val(address_info["city"] + address_info["street"]);
    }
/*]]>*/
</script>
