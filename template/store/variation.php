<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart MyProfile Store Variation Template
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: variation.php 140016 2009-07-28 06:57:23Z tajima $
 */
//規格情報取得
$variation = $this->model['variation'];
//規格詳細数取得
$values_count = $this->model['values_count'];
if (is_null($values_count) || $values_count=='') {
    //初期値設定
    $values_count = 1;
}

//入力チェック結果確認
$results = $this->model['results'];
if (isset($results)) {
    $err_variation_name = $results->errors['variation_name'];
    $err_value_name = $results->errors['value_name'];
}

//規格詳細一覧調整
$variation_values = array();
if (count($variation['values_value']) > 0) {
    $vcnt = count($variation['values_value']);
    $i = 0;
    foreach ($variation['values_value'] as $key=>$val) {
        $variation_values[$key] = $val;
        $i++;
        if ($i >= $values_count) {
            break;
        }
    }
    for ($i=count($variation_values)+1; $i<=$values_count; $i++) {
        $variation_values[($i*-1)] = '';
    }
}
else {
    for ($i=1; $i<=$values_count; $i++) {
        $variation_values[($i*-1)] = '';
    }
}

//リストボックス用
$values_list = array();
for ($i=1; $i<=10; $i++) {
    $values_list[$i] = $i;
}

?>
<!-- 規格登録／更新 -->
<form name="variation" action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
<?php echo $this->input(array('type'=>'hidden', 'id'=>'variation_action', 'name'=>'sc_action', 'value'=>'save_variation')); ?>
<?php echo $this->input(array('type'=>'hidden', 'id'=>'variation_id', 'value'=>$variation['id'])); ?>

<p class="submit"><?php echo $this->link(array('href'=>'#', 'value'=>__(SCLNG_BACK, SC_DOMAIN), 'onclick'=>"Javascript:submitForm('variation','');return false;")); ?></p>

<div class="sc_tbl_updt">
<table>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_VARIATION_NAME, SC_DOMAIN) ?></th>
    <td><?php echo $this->input(array('id'=>'variation_name', 'value'=>$variation['name'], 'class'=>'input_200')); ?><?php $this->error($err_variation_name); ?></td>
  </tr>
</table>
</div>

<?php echo SCLNG_STORE_VARIATION_VALUE_COUNT; ?> <?php echo $this->select(array('id'=>'values_count', 'onchange'=>"Javascript:submitForm('variation', 'change_values_count');"), array('list'=>$values_list, 'default'=>$values_count)); ?>
<div class="sc_tbl_updt">
<table>
  <?php $i=1; ?>
  <?php foreach ($variation_values as $key=>$val): ?>
  <tr>
    <th class="required"><?php _e(SCLNG_STORE_VARIATION_VALUE_NAME, SC_DOMAIN) ?><?php echo $i++ ?></th>
    <td>
    <?php
        echo $this->input(array('id'=>'values_value_'.$key, 'name'=>'values_value['.$key.']', 'value'=>$val, 'class'=>'input_200'));
    ?>
    <?php $this->error($err_value_name[$key]); ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>

<p class="submit">
<?php if(is_null($variation['id'])||$variation['id']==''): ?>
<?php echo $this->submit(array('value'=>__(SCLNG_CREATE, SC_DOMAIN) . " &raquo;")); ?>
<?php else: ?>
<?php echo $this->submit(array('value'=>__(SCLNG_UPDATE, SC_DOMAIN) . " &raquo;")); ?>
<?php endif; ?>
</p>
</form>
