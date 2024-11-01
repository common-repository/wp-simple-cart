<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCart.php 143626 2009-08-07 12:22:47Z tajima $
 */

/******************************************************************************
 * SimpleCart
 * 
 * @author      Exbridge,inc.
 * @version     0.1
 * 
 *****************************************************************************/
class SimpleCart {

    var $view;              //view管理用変数
    var $request;           //request管理用変数
    var $hidden_vars;       //hidden管理用変数
    var $model;             //model管理用変数
    var $mode;              //動作モード変数
    var $user_ID;           //ユーザーID
    var $user_login;        //ユーザーログインID

    var $template;

    /**
     * コンストラクタ
     *
     */
    function SimpleCart() {
        global $current_user, $sc_mode;
        $this->mode = $sc_mode;
        $this->user_ID = $current_user->ID;
        $this->user_login = $current_user->user_login;
        if (version_compare($wp_version, '2.6', '<')) {// Using old WordPress
            load_plugin_textdomain('simple-cart', 'wp-content/plugins/' . SC_PLUGIN_NAME . '/languages');
        }
        else {
            load_plugin_textdomain('simple-cart', 'wp-content/plugins/' . SC_PLUGIN_NAME . '/languages', SC_PLUGIN_NAME . '/languages');
        }
        //キャッシュファイル取得
        if (isset($_REQUEST['sc_redirect'])) {
            $temp = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/cache/' . $_REQUEST['sc_redirect'];
            //ちと強引過ぎるし、消すタイミングがあれなので、落ち着いたら再考。。。
            if (file_exists($temp)) {
                $fp = @fopen($temp, 'r');
                $contents = @fread($fp, filesize($temp));
                @fclose($fp);
                @unlink($temp);
                //現在のリクエストを一旦退避
                $request = $_REQUEST;
                $_REQUEST = unserialize($contents);
                //上書き
                foreach ($request as $key=>$val) {
                    $_REQUEST[$key] = $val;
                }
                unset($_GET['sc_redirect']);
                unset($_POST['sc_redirect']);
                unset($_REQUEST['sc_redirect']);
            }
        }
        $this->request = new SimpleCartRequestVo();
    }

    /**
     * 画面描画メソッド実行
     *
     */
    function exec($template) {

        $this->template = $template;

        //住所検索
        echo SimpleCartFunctions::TemplateConvert('js/zipcode.php');

        //BuddyPress連携時
        if ($this->mode==SC_MODE_BP) {
           global $bp;
            //MyPage内
            if ($bp->current_component == $bp->simplecart_store->slug
                || $bp->current_component == $bp->simplecart_user->slug) {
                $this->bpHeader();
                add_action('bp_template_content', array(&$this, 'header_contents'));
                add_action('bp_template_content', array(&$this, 'load_template'));
                add_action('bp_template_content', array(&$this, 'footer_contents'));
                $this->bpFooter();
            }
            //表ページ
            else {
                $this->header_contents();
                $this->header();
                $this->title();
                $this->load_template();
                $this->footer();
                $this->footer_contents();
            }
        }
        //Mu単独版
        else {
            $this->header_contents();
            $this->header();
            $this->title();
            $this->load_template();
            $this->footer();
            $this->footer_contents();
        }
    }

    /**
     * テンプレートファイルのローディング
     *
     */
    function load_template() {
        $file = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/template/' . $this->template . '.php';
        if (file_exists($file)) {
            ob_start();
            include($file);
            $contents = ob_get_contents();
            ob_end_clean();
        }
        echo $contents;
    }

    /**
     * ヘッダーHTML出力
     * abstract扱い
     *
     */
    function header() {
    }

    /**
     * フッターHTML出力
     * abstract扱い
     *
     */
    function footer() {
    }

    /**
     * タイトルHTML出力
     * abstract扱い
     *
     */
    function title() {
    }

    /**
     * カート用コンテンツエリア開始HTML出力
     *
     */
    function header_contents() {
        ?>
        <div class="sc_contents">
        <?php
    }

    /**
     * カート用コンテンツエリア終了HTML出力
     *
     */
    function footer_contents() {
        ?>
        </div>
        <?php
    }

    /**
     * BuddyPress連携用ヘッダー処理
     *
     */
    function bpHeader() {
        add_action('bp_template_content_header', array(&$this, 'header'));
        //@todo bp_template_title内でもタイトルにtagをつけて出力する。
        //templateによって<h2><h2>タイトル名</h2></h2>となる場合がある
        add_action('bp_template_title', array(&$this, 'title'));
    }

    /**
     * BuddyPress連携用フッター処理
     *
     */
    function bpFooter() {
        bp_core_load_template('plugin-template');
    }

    /**
     * HTML Redirect
     *
     */
    function redirect($url) {
        //フォルダ存在確認
        $temp = WP_PLUGIN_DIR . '/' . SC_PLUGIN_NAME . '/files/cache';
        SimpleCartFunctions::Mkdir($temp);
        //リクエストデータをキャッシュファイル化
        $file_nm = SimpleCartFunctions::RandMd5();
        $temp = $temp . '/' . $file_nm;
        $fp = fopen($temp, "w");
        unset($_REQUEST['sc_action']);//現在アクションを削除
        fwrite($fp, serialize($_REQUEST));
        fclose($fp);
        //画面遷移
        $param_flg = false;
        foreach ($url as $key=>$val) {
            if (strpos($val, '?') > 0) {
                $param_flg = true;
                break;
            }
        }
        if ($param_flg == true) {
            $url['_sc_redirect'] = '&sc_redirect=' . $file_nm;
        }
        else {
            $url['_sc_redirect'] = '?sc_redirect=' . $file_nm;
        }
        SimpleCartFunctions::Redirect($url);
    }

    /**
     * Hiddenタグの一括出力
     *
     */
    function hvars() {
        SimpleCartFunctions::EchoHTMLHiddens ($this->hidden_vars);
    }

    /**
     * Inputタグ出力
     *
     */
    function input($params) {
        return SimpleCartFunctions::HTMLInput($params);
    }

    /**
     * Textareaタグ出力
     *
     */
    function text($params) {
        return SimpleCartFunctions::HTMLText($params);
    }

    /**
     * Selectタグ出力
     *
     */
    function select($id, $params) {
        return SimpleCartFunctions::HTMLSelect($id, $params);
    }

    /**
     * Linkタグ出力
     *
     */
    function link($params) {
        return SimpleCartFunctions::HTMLLink($params);
    }

    /**
     * Submitタグ出力
     *
     */
    function submit($params) {
        return SimpleCartFunctions::HTMLSubmit($params);
    }

    /**
     * Ymdタグ出力
     *
     */
    function ymd($params) {
        return SimpleCartFunctions::HTMLYyyymmdd($params);
    }

    /**
     * 金額タグ出力
     *
     */
    function money($val) {
        return SimpleCartFunctions::MoneyFormat($val);
    }

    /**
     * Pageタグ出力
     *
     */
    function page($params) {
        return SimpleCartFunctions::HTMLPage($params);
    }

    /**
     * Output Error Message
     *
     */
    function error($param) {
        echo SimpleCartFunctions::HTMLError($param);
    }

    /**
     * Role Error Page
     *
     */
    function roleError() {
        ?>
        <?php _e(SCLNG_ERROR_AUTH, SC_DOMAIN); ?><br/>
        <?php echo $this->link(array('href'=>site_url(), 'value'=>__(SCLNG_GOTO_HOME, SC_DOMAIN))); ?>
        <?php
    }
}
