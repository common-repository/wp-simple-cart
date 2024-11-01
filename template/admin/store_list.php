<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Admin Store List Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: store_list.php 151036 2009-09-01 06:50:07Z tajima $
 */
$roles = $this->model['roles'];
$user_list = $this->model['user_list'];
?>
<h3><?php _e(SCLNG_ADMIN_STORE_LIST, SC_DOMAIN) ?></h3>
<form id="form-user-list" action='<?php echo $_SERVER['REQUEST_URI'] ?>&sc_action=adduser' method='post'>
  <?php _e(SCLNG_ADMIN_USER_LOGIN, SC_DOMAIN) ?>
  <?php echo $this->input(array('type'=>'text', 'id'=>'user_login')); ?>
  <input type="submit" value="<?php _e(SCLNG_ADMIN_ADD_STORE_MEMEBER, SC_DOMAIN) ?>" name="add_user" class="button-secondary delete" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_ADD_USER, SC_DOMAIN) ?>')){return false}"/>
  <?php
    if (isset($this->model['add_user_error'])) {
        echo '<font color=red><strong>' . $this->model['add_user_error'] . '</strong></font>';
    }
  ?>
</form>
<form id="form-user-list" action='<?php echo $_SERVER['REQUEST_URI'] ?>&sc_action=allusers' method='post'>
<div class="tablenav">
  <div class="alignleft actions">
    <input type="submit" value="<?php _e(SCLNG_DELETE, SC_DOMAIN) ?>" name="alluser_delete" class="button-secondary" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_DELETE_USER, SC_DOMAIN) ?>')){return false}"/>
    <input type="submit" value="<?php _e(SCLNG_STOP, SC_DOMAIN) ?>" name="alluser_non_active" class="button-secondary" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_STOP_USER, SC_DOMAIN) ?>')){return false}"/>
    <input type="submit" value="<?php _e(SCLNG_REVIVAL, SC_DOMAIN) ?>" name="alluser_active" class="button-secondary" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_REVIVAL_USER, SC_DOMAIN) ?>')){return false}"/>
    <br class="clear" />
  </div>
</div>
<table class="widefat" cellspacing="0">
  <thead>
  <tr>
    <th scope="col" class="check-column"><input type="checkbox" /></th>
    <th scope="col" colspan="2"><?php _e(SCLNG_ADMIN_STORE_LOGIN, SC_DOMAIN) ?></th>
    <th scope="col"><?php _e(SCLNG_ADMIN_STORE_NAME, SC_DOMAIN) ?></th>
    <th scope="col"><?php _e(SCLNG_ADMIN_STORE_EMAIL, SC_DOMAIN) ?></th>
    <th scope="col"><?php _e(SCLNG_ADMIN_STORE_REGIST_DATE, SC_DOMAIN) ?></th>
    <th scope="col"><?php _e(SCLNG_ADMIN_STORE_URL, SC_DOMAIN) ?></th>
  </tr>
  </thead>
  <tbody id="users" class="list:user user-list">
  <?php
    $bgcolor = '';
    if (is_array($user_list)) {
        foreach ($user_list as $user) { 
            //$u = new WP_User($user['ID']);
            //$role_nm = translate_with_context($wp_roles->role_names[$u->roles[0]]);
            $class = ('alternate'==$class)?'':'alternate';
            $avatar = get_avatar($user['user_email'], 32);
            $edit_url = $_SERVER["PHP_SELF"] . '?page=SimpleCartAdminStoreUser.php&id=' . $user['ID'];
            $user_status = ($user['user_status']==SC_USER_DISABLE)?__(SCLNG_ADMIN_STORE_STATUS_STOP, SC_DOMAIN):'';
  ?>
  <tr class="<?php echo $class; ?>">
    <th scope="row" class="check-column"><input type='checkbox' id='user_<?php echo $user['ID'] ?>' name='allusers[]' value='<?php echo $user['ID'] ?>' /></th>
    <td width="50px"><?php echo $avatar; ?></td>
    <td><font style="color:#FF0000;font-weight:bold;"><?php echo $user_status ?></font> <?php echo stripslashes($user['user_login']); ?><br/><a href="<?php echo $edit_url ?>"><?php _e(SCLNG_ADMIN_STORE_EDIT, SC_DOMAIN); ?></a></td>
    <td><strong><?php echo $user['display_name']; ?></a></strong></td>
    <td><a href="mailto:<?php echo $user['user_email'] ?>"><?php echo $user['user_email'] ?></a></td>
    <td><?php echo mysql2date(__('Y-m-d \<\b\r \/\> g:i a'), $user['user_registered']); ?></td>
    <td><a href="<?php echo $user['user_url'] ?>" target="_blank"><?php echo ($user['user_url']=='http://')?'':$user['user_url'] ?></a></td>
  </tr>
  <?php
        }
    }
  ?>
  </tbody>
</table>
<div class="tablenav">
  <div class="alignleft actions">
    <input type="submit" value="<?php _e(SCLNG_DELETE, SC_DOMAIN) ?>" name="alluser_delete" class="button-secondary" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_DELETE_USER, SC_DOMAIN) ?>')){return false}"/>
    <input type="submit" value="<?php _e(SCLNG_STOP, SC_DOMAIN) ?>" name="alluser_non_active" class="button-secondary" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_STOP_USER, SC_DOMAIN) ?>')){return false}"/>
    <input type="submit" value="<?php _e(SCLNG_REVIVAL, SC_DOMAIN) ?>" name="alluser_active" class="button-secondary" onclick="if(!confirm('<?php echo _e(SCLNG_CONFIRM_REVIVAL_USER, SC_DOMAIN) ?>')){return false}"/>
    <br class="clear" />
  </div>
</div>
</form>
