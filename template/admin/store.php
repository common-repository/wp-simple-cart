<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Store User Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store.php 140016 2009-07-28 06:57:23Z tajima $
 */
$user    = $this->model['user'];
$states  = $this->model['states'];
$roles   = $this->model['roles'];
$results = $this->model['results'];

$err_login = '';
$err_pass  = '';
$err_email = '';
$err_fatal = '';
if (is_object($results)) {
    if (isset($results->errors['user_login'])) {
        $err_login = $results->errors['user_login'];
    }
    if (isset($results->errors['pass'])) {
        $err_pass = $results->errors['pass'];
    }
    if (isset($results->errors['user_email'])) {
        $err_email = $results->errors['user_email'];
    }
    if (isset($results->errors['fatal'])) {
        $err_fatal = $results->errors['fatal'];
    }
    if (($err_login=='')&&($err_pass=='')&&($err_email=='')&&($err_fatal=='')) {
    }
}
else {
    $success = __(SCLNG_ADMIN_SUCCESS_NEW_STORE_USER, SC_DOMAIN);
}
if (isset($user)) {
    $id           = $user['ID'];
    $user_login   = $user['user_login'];
    $email        = $user['email'];
    $display_name = $user['display_name'];
    $url          = ($user['url']=='http://')?'':$user['url'];
    $address      = $user['address'];
    $street       = $user['street'];
    $state        = $user['state'];
    $zip          = $user['zip'];
    $tel          = $user['tel'];
    $fax          = $user['fax'];
    $role         = $user['role'];
}
if (is_null($state)) {
    $state = SC_DEFAULT_STATE;
}
?>
<?php if (!isset($user)||is_object($results)): ?>
<h3><?php _e(SCLNG_ADMIN_STORE, SC_DOMAIN) ?></h3>
<?php else: ?>
<a href="/wp-admin/admin.php?page=SimpleCartAdminStoreUser.php"><?php _e(SCLNG_ADMIN_NEW_STORE_USER, SC_DOMAIN) ?></a>
<h3><?php _e(SCLNG_ADMIN_STORE_UPDATE, SC_DOMAIN) ?></h3>
<?php endif; ?>
<?php if (!is_null($err_fatal)): ?>
<font color="red"><?php echo $err_fatal ?></font>
<?php endif; ?>
<form name="wp_simple_cart" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
  <?php echo $this->input(array('type'=>'hidden', 'id'=>'sc_action', 'value'=>'new_store_user')); ?>
  <?php echo $this->input(array('type'=>'hidden', 'id'=>'id', 'value'=>$id)); ?>
  <table class="form-table">
    <!-- Login ID -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_LOGIN, SC_DOMAIN) ?></th>
      <?php if (!isset($user)||is_object($results)): ?>
      <td><?php echo $this->input(array('id'=>'user_login', 'value'=>$user_login)); ?><?php $this->error($err_login); ?></td>
      <?php else: ?>
      <td>
        <?php echo $this->input(array('id'=>'user_login', 'value'=>$user_login, 'disabled'=>'disabled')); ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'user_login', 'value'=>$user_login)); ?>
      </td>
      <?php endif; ?>
    </tr>
    <!-- Password -->
    <?php if (!isset($user)||is_object($results)): ?>
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_PASSWORD, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'pass1')); ?><?php $this->error($err_pass); ?></td>
    </tr>
    <?php endif; ?>
    <!-- Display Name -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_NAME, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'display_name', 'value'=>$display_name, 'class'=>'input_200' )); ?></td>
    </tr>
    <!-- Email -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_EMAIL, SC_DOMAIN) ?></th>
      <?php if (!isset($user)||is_object($results)): ?>
      <td><?php echo $this->input(array('id'=>'email', 'value'=>$email, 'class'=>'input_280')); ?><?php $this->error($err_email); ?></td>
      <?php else: ?>
      <td>
        <?php echo $this->input(array('id'=>'dimmy_email', 'value'=>$email, 'class'=>'input_280', 'disabled'=>'disabled')); ?>
        <?php echo $this->input(array('type'=>'hidden', 'id'=>'email', 'value'=>$email)); ?>
      </td>
      <?php endif; ?>
    </tr>
    <!-- Url -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_URL, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'url', 'value'=>$url, 'class'=>'input_280')); ?></td>
    </tr>
    <!-- Zip -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_ZIP, SC_DOMAIN) ?></th>
      <td>
        <?php echo $this->input(array('id'=>'zip', 'value'=>$zip)); ?>
        <?php echo $this->input(array('type'=>'button', 'value'=>__(SCLNG_SEARCH_ADDRESS, SC_DOMAIN), 'id'=>'btn_search_address', 'class'=>'btn_search_address', 'onclick'=>"Javascript:set_address_info();")); ?>
      </td>
    </tr>
    <!-- State -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_STATE, SC_DOMAIN) ?></th>
      <td>
        <?php
            reset($states);
            echo $this->select(array('id'=>'state'), array('list'=>$states,'default'=>$state));
        ?>
      </td>
    </tr>
    <!-- Address1 -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_STREET, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'street', 'value'=>$street, 'class'=>'input_280')); ?></td>
    </tr>
    <!-- Address2 -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_ADDRESS, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'address', 'value'=>$address, 'class'=>'input_280')); ?></td>
    </tr>
    <!-- Tel -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_TEL, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'tel', 'value'=>$tel)); ?></td>
    </tr>
    <!-- Fax -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_FAX, SC_DOMAIN) ?></th>
      <td><?php echo $this->input(array('id'=>'fax', 'value'=>$fax)); ?></td>
    </tr>
    <!-- Role -->
    <tr>
      <th scope="row"><?php _e(SCLNG_ADMIN_STORE_ROLE, SC_DOMAIN) ?></th>
      <td>
        <select name="role" id="role">
        <?php 
            reset($roles);
            foreach ($roles as $key=>$role_assoc) {
                $name = translate_with_context($role_assoc['name']);
                $selected = ($key==$role)?'selected="selected"':'';
                echo "<option {$selected} value=\"{$role}\">{$name}</option>";
            }
        ?>
        </select>
      </td>
    </tr>
  </table>
  <?php if (!isset($user)||is_object($results)): ?>
  <p class="submit"><?php echo $this->input(array('type'=>'submit', 'value'=>__(SCLNG_CREATE, SC_DOMAIN)." &raquo;")); ?></p>
  <?php else: ?>
  <p class="submit"><?php echo $this->input(array('type'=>'submit', 'value'=>__(SCLNG_UPDATE, SC_DOMAIN)." &raquo;")); ?></p>
  <?php endif; ?>
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
