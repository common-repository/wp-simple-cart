<?php
/*
 * This file is part of Simple Cart.
 * Copyright (c) EXBRIDGE,Inc. <info@exbridge.jp>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple Cart Value Object Class
 *
 * @copyright   2009 EXBRIDGE,Inc. All Rights Reserved.
 * @link        http://exbridge.jp/
 * @package     WP Simple Cart
 * @version     svn:$Id: SimpleCartInvoicePDF.php 141652 2009-08-01 06:16:59Z tajima $
 */

/******************************************************************************
 * Invoice PDF Creater Class
 * 
 * @author     Exbridge,inc.
 * @version    0.1
 * 
 *****************************************************************************/
class SimpleCartInvoicePDF extends PDF_Japanese{

    var $order_id;
    var $order_no;
    var $order;

    var $flg_all_border = false;

    /**
     * コンストラクタ
     *
     */
    function SimpleCartInvoicePDF($order_id) {
        parent::PDF_Japanese('P', 'mm', 'A4');
        $this->order_id = $order_id;
        $this->order_no = SimpleCartFunctions::LPAD($order_id, 8);
        $model = new SimpleCartStoreOrderModel();
        $this->order = $model->getOrder(array('order_id'=>$order_id));
        $this->AddSJISFont();
        //全ページの番号のエイリアスを定義します
        $this->AliasNbPages(); 
        // マージン設定
        $this->SetMargins(15, 20);
    }

    function create() {
        $this->Open();
        $this->AddPage();

        //請求書送付先情報出力
        $this->billInfo();

        //店舗住所情報出力
        $this->storeInfo();

        //支払総額出力
        $this->totalInfo();

        //明細ヘッダー出力
        $this->detailHeader();

        //商品送付先情報出力
        $this->sendInfo();

        //商品明細ヘッダー情報出力
        $this->product_title_list = array();
        $this->product_title_list[] = array('name' => $this->convert(__(SCLNG_PDF_ORDER_PRODUCT, SC_DOMAIN)), 'width' => '110.3');
        $this->product_title_list[] = array('name' => $this->convert(__(SCLNG_PDF_ORDER_QUANTITY, SC_DOMAIN)), 'width' => '12');
        $this->product_title_list[] = array('name' => $this->convert(__(SCLNG_PDF_ORDER_PRICE, SC_DOMAIN)), 'width' => '21.7');
        $this->product_title_list[] = array('name' => $this->convert(__(SCLNG_PDF_ORDER_SUBTOTAL, SC_DOMAIN)), 'width' => '24.5');
        $base_x = 21;
        $base_y = 130;

        $this->productListHeader($base_x, $base_y);

        //商品明細情報出力
        $this->productList();

        //備考欄出力（空欄）
        $this->free();

        //PDF生成
        $this->Output();
    }

    /**
     * 納品書ヘッダー表示(自動呼出し)
     *
     */
    function Header() {
        $this->SetFont('SJIS', '', 18);
        $this->SetLineWidth(0.5);

        $this->SetY(10);
        $this->Cell(0, 10, $this->convert(__(SCLNG_PDF_ORDER_TITLE, SC_DOMAIN)), 1, 0, 'C');

        $this->SetFont('SJIS', '', 8);
        $this->SetY(10);
        $page_text = $this->convert(__(SCLNG_PDF_ORDER_PAGE, SC_DOMAIN));
        $this->Cell(0, 10, $this->PageNo().'/{nb} ' . $page_text , 0, 0, 'R');
        $this->Ln(20);

        if ($this->PageNo() > 1) {
            $this->productListHeader(21, 30);
        }
        $this->SetLineWidth(0.3);
    }

    /**
     * 請求書送付先情報出力
     *
     */
    function billInfo() {
        //州情報を取得
        global $sc_states;

        $base_x = 21;
        $base_y = 30;
        $space = 4;
        $line_height = 4;

        //フォントを設定
        $this->SetFont('SJIS', '', 10);

        //郵便番号
        $bill_zip = __(SCLNG_PDF_ORDER_ZIP, SC_DOMAIN) . ' ' . $this->order['bill_zip'];
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, $line_height, $this->convert($bill_zip), ($this->flg_all_border)?1:0); 

        $base_y += $line_height;

        //住所
        $bill_address1 = $sc_states[$this->order['bill_state']] . ' ' . $this->order['bill_street'];
        $bill_address2 = $this->order['bill_address'];
        $this->SetXY($base_x + $space, $base_y);
        $this->MultiCell(0, $line_height, $this->convert($bill_address1 . "\n" . $bill_address2), ($this->flg_all_border)?1:0); 

        //フォントサイズ設定
        $this->SetFontSize(11);

        $base_y += ($line_height * 2);
        $base_y += $line_height;

        //氏名
        $bill_name = $this->order['bill_last_name'] . ' ' . $this->order['bill_first_name'] . ' ' . __(SCLNG_PDF_ORDER_BILL_TITLE, SC_DOMAIN);
        $this->SetXY($base_x + $space, $base_y);
        $this->Cell(40, $line_height + 1, $this->convert($bill_name), ($this->flg_all_border)?1:0); 

        $base_y += $line_height + $line_height;

        //フォントサイズ設定
        $this->SetFontSize(8);
        //お礼
        $bill_thanks = __(SCLNG_PDF_ORDER_MESSAGE, SC_DOMAIN);
        $this->SetXY($base_x + $space, $base_y);
        $this->MultiCell(80, $line_height + 1, $this->convert($bill_thanks), ($this->flg_all_border)?1:0); 
    }

    /**
     * 店舗住所情報出力
     *
     */
    function storeInfo() {
        //州情報を取得
        global $sc_states;

        $base_x = 125;
        $base_y = 50;
        $line_height = 3;

        //フォントを設定
        $this->SetFont('SJIS', 'B', 8);
        //ショップ名
        $store_name = $this->order['store_name'];
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, $line_height, $this->convert($store_name), ($this->flg_all_border)?1:0); 

        $base_y += $line_height;

        //フォントを設定
        $this->SetFont('SJIS', '', 8);

        //URL
        $store_url = $this->order['store_url'];
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, $line_height, $this->convert($store_url), ($this->flg_all_border)?1:0); 

        $base_y += $line_height;

        //郵便番号
        $store_zip = __(SCLNG_PDF_ORDER_ZIP, SC_DOMAIN) . ' ' . $this->order['store_zip'];
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, $line_height, $this->convert($store_zip), ($this->flg_all_border)?1:0); 

        $base_y += $line_height;

        //住所
        $store_address1 = $sc_states[$this->order['store_state']] . ' ' . $this->order['store_street'];
        $store_address2 = $this->order['store_address'];
        $this->SetXY($base_x, $base_y);
        $this->MultiCell(0, $line_height, $this->convert($store_address1 . "\n" . $store_address2), ($this->flg_all_border)?1:0); 

        $base_y += ($line_height * 2);
        //TEL
        $store_tel = __(SCLNG_PDF_ORDER_TEL, SC_DOMAIN) . " " . $this->order['store_tel'];
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, $line_height, $this->convert($store_tel), ($this->flg_all_border)?1:0); 
    }

    /**
     * 支払総額出力
     *
     */
    function totalInfo() {
        $base_x = 21;
        $base_y = 80;
        //フォントを設定
        $this->SetXY($base_x, $base_y);
        $this->SetFont('SJIS', 'B', 9);
        $this->Cell(20, 8, $this->convert(__(SCLNG_PDF_ORDER_TOTAL, SC_DOMAIN)), ($this->flg_all_border)?1:'B', 0, 'L', 0, '');
        $this->SetFont('SJIS', 'B', 15);
        $this->Cell(40, 8, $this->convert(SimpleCartFunctions::MoneyFormat($this->order['total'])), ($this->flg_all_border)?1:'B', 0, 'R', 0, '');

        //------------------------------------------
        //消費税額の表示処理
        //------------------------------------------
        $order_detail_list = $this->order['data'];

        //表示フラグ
        $flg_disp_tax_total = true;
        $tax_total = 0;
        foreach ($order_detail_list as $order_detail) {
            $item_su    = floatval($order_detail['su']);
            $tax        = floatval($order_detail['tax']);
            $tax_total += $item_su * $tax;
            //内税の場合
            if ($tax == 0) {
                //合計金額に対して税金表示をしない
                $flg_disp_tax_total = false;
                break;
            }
        }

        if ($flg_disp_tax_total) {
            $this->SetFont('SJIS', 'B', 9);
            $tax_text = __(sprintf(SCLNG_PDF_ORDER_TAX, SimpleCartFunctions::MoneyFormat($tax_total)), SC_DOMAIN);
            $this->Cell(40, 8, $this->convert($tax_text), ($this->flg_all_border)?1:'B', 0);
        }
    }

    /**
     * 明細ヘッダー出力
     *
     */
    function detailHeader() {
        $base_x = 21;
        $base_y = 95;

        //フォントを設定
        $this->SetFont('SJIS', 'B', 9);
        $this->SetXY($base_x, $base_y);
        $this->SetFillColor(216,216,216);
        $this->Cell(168.3, 6, $this->convert(__(SCLNG_PDF_ORDER_DETAIL, SC_DOMAIN)), 1, 0, 'L', 1, '');
    }

    /**
     * 商品送付先情報出力
     *
     */
    function sendInfo() {
        //州情報を取得
        global $sc_states;

        $space = 3;

        $base_x = 21;
        $base_y = 102;
        $line_height = 3;

        $base_x2 = 125;
        $base_y2 = 102;
        $line_height2 = 8;


        //フォントを設定
        $this->SetFont('SJIS', 'B', 9);
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, 6, $this->convert(__(SCLNG_PDF_ORDER_SENDER_INFO, SC_DOMAIN)), ($this->flg_all_border)?1:0);

        $base_y += $line_height + $line_height;

        //フォントを設定
        $this->SetFont('SJIS', '', 9);

        //郵便番号
        $send_zip = __(SCLNG_PDF_ORDER_ZIP, SC_DOMAIN) . ' ' . $this->order['send_zip'];
        $this->SetXY($base_x, $base_y);
        $this->Cell(40, $line_height, $this->convert($send_zip), ($this->flg_all_border)?1:0); 

        $base_y += $line_height;

        //住所
        $send_address1 = $sc_states[$this->order['send_state']] . ' ' . $this->order['send_street'];
        $send_address2 = $this->order['send_address'];
        $this->SetXY($base_x + $space, $base_y);
        $this->MultiCell(80, $line_height, $this->convert($send_address1 . "\n" . $send_address2), ($this->flg_all_border)?1:0); 

        $base_y += ($line_height * 2);
        $base_y += $line_height;

        //フォントを設定
        $this->SetFont('SJIS', '', 10);

        //氏名
        $send_name = $this->order['send_last_name'] . ' ' . $this->order['send_first_name'] . ' ' . __(SCLNG_PDF_ORDER_BILL_TITLE, SC_DOMAIN);
        $this->SetXY($base_x + $space, $base_y);
        $this->Cell(40, $line_height + 1, $this->convert($send_name), ($this->flg_all_border)?1:0); 

        //-----------------------------------------------------------------------------------------------
        //ご注文日＆注文番号
        //-----------------------------------------------------------------------------------------------
        $title_width = 20;
        $this->SetFont('SJIS', 'B', 9);
        $this->SetXY($base_x2, $base_y2);
        $this->Cell($title_width, 6, $this->convert(__(SCLNG_PDF_ORDER_DATE_INFO, SC_DOMAIN)), ($this->flg_all_border)?1:0);

        //フォントを設定
        $this->SetFont('SJIS', '', 9);

        //注文日
        $order_date = substr($this->order['regist_date'], 0, 10);
        $this->SetXY($base_x2 + $title_width, $base_y2);
        $this->Cell(0, 6, $this->convert($order_date), ($this->flg_all_border)?1:0); 

        $base_y2 += $line_height2;

        $this->SetFont('SJIS', 'B', 9);
        $this->SetXY($base_x2, $base_y2);
        $this->Cell($title_width, 6, $this->convert(__(SCLNG_PDF_ORDER_NO_INFO, SC_DOMAIN)), ($this->flg_all_border)?1:0);

        //注文番号
        $order_no = SimpleCartFunctions::LPAD($this->order['id'], 8);
        $this->SetXY($base_x2 + $title_width, $base_y2);
        $this->Cell(0, 6, $this->convert($order_no), ($this->flg_all_border)?1:0); 
    }

    /**
     * 商品明細情報出力
     *
     */
    function productListHeader($base_x, $base_y) {
        $this->SetXY($base_x, $base_y);

        //Colors, line width and bold font
        $this->SetFillColor(216,216,216);
        $this->SetTextColor(0);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(0.3);
        $this->SetFont('SJIS', 'B', 9);
        //Header
        foreach ($this->product_title_list as $title_info) {
            $this->Cell($title_info['width'], 7, $title_info['name'], 1, 0, 'C', 1);
        }
        $this->Ln();
    }
    /**
     * 商品明細情報出力
     *
     */
    function productList() {
        $base_x = 21;

        //Color and font restoration
        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('SJIS', '', 8);

        //Data
        $fill=0;

        $row_height = 4;

        $order_detail_list = $this->order['data'];

        foreach ($order_detail_list as $order_detail) {
            $item_name  = $this->convert($order_detail['product_name'] . '/' . $order_detail['product_cd'] . '  ' . @str_replace(chr(10), "  ", $order_detail['variation_name']));
            $item_su    = floatval($order_detail['su']);
            $tax        = floatval($order_detail['tax']);
            $off        = floatval($order_detail['off']);
            $item_price = floatval($order_detail['price']) + $tax - $off;
            $item_kin   = $item_su * $item_price;

            $this->SetX($base_x);
            $this->Cell($this->product_title_list[0]['width'], $row_height, $item_name, 1, 0, 'L', $fill);
            $this->Cell($this->product_title_list[1]['width'], $row_height, number_format($item_su), 1, 0, 'R', $fill);
            $this->Cell($this->product_title_list[2]['width'], $row_height, $this->convert(SimpleCartFunctions::MoneyFormat($item_price)), 1, 0, 'R', $fill);
            $this->Cell($this->product_title_list[3]['width'], $row_height, $this->convert(SimpleCartFunctions::MoneyFormat($item_kin)), 1, 0, 'R', $fill);
            $this->Ln();
            $fill=!$fill;
        }
    }

    /**
     * 備考欄出力（空欄）
     *
     */
    function free() {
        $this->Cell(0, 10, '', 0, 1, 'C', 0, '');
        $this->SetFont('SJIS', '', 9);
        $this->MultiCell(0, 6, $this->convert(__(SCLNG_PDF_ORDER_FREE, SC_DOMAIN)), 'T', 'L', 1);  //備考
        $this->SetFont('SJIS', '', 8);
        //@todo
        $this->MultiCell(0, 4, $this->convert("\n"), '', 'L', 1);  //備考
    }

    /**
     * フッター出力
     *
     */
    function Footer() {
    }

    /**
     * 文字コード変換
     *
     */
    function convert($str) {
        return mb_convert_encoding($str, "SHIFT-JIS","UTF-8");
    }
}
