<?php

namespace App\customLib;

/*if (!defined('IN_ECS')) {

    die('Hacking attempt');

}
*/


/*
$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/allpay_card.php';



if (file_exists($payment_lang)) {

    global $_LANG;



    include_once($payment_lang);

}
*/



/* 模塊的基本信息 */

if (isset($set_modules) && $set_modules == TRUE) {

    $i = isset($modules) ? count($modules) : 0;



    /* 代碼 */

    $modules[$i]['code'] = basename(__FILE__, '.php');



    /* 描述對應的語言項 */

    $modules[$i]['desc'] = 'allpay_card_desc';



    /* 是否支持貨到付款 */

    $modules[$i]['is_cod'] = '0';



    /* 是否支持在線支付 */

    $modules[$i]['is_online'] = '1';



    /* 排序 */

    //$modules[$i]['pay_order']  = '1';



    /* 作者 */

    $modules[$i]['author'] = '歐付寶';



    /* 網址 */

    $modules[$i]['website'] = 'https://www.allpay.com.tw';



    /* 版本號 */

    $modules[$i]['version'] = 'V0.1';



    /* 配置信息 */

    $modules[$i]['config'] = array(

        array('name' => 'allpay_card_test_mode', 'type' => 'select', 'value' => 'Yes'),

        array('name' => 'allpay_card_account', 'type' => 'text', 'value' => '1111'),

        array('name' => 'allpay_card_iv', 'type' => 'text', 'value' => 'iv'),

        array('name' => 'allpay_card_key', 'type' => 'text', 'value' => 'key')

    );

    return;

}


include_once('AllPay.Payment.Integration.php');


/**

 * 類

 */

class allpay_card extends AllInOne {



    /**

     * 構造函數

     *

     * @access  public

     * @param

     *

     * @return void

     */

    function __construct() {

        parent::__construct();

        $this->allpay_card();

    }



    function allpay_card() {

        

    }



    /**

     * 提交函數

     */

    function get_code($order, $payment) {

        $isTestMode = ($payment['allpay_card_test_mode'] == 'Yes');

        $this->ServiceURL = ($isTestMode ? "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut" : "https://payment.ecpay.com.tw/Cashier/AioCheckOut");

        $this->HashKey = trim($payment['allpay_card_key']);

        $this->HashIV = trim($payment['allpay_card_iv']);

        $this->MerchantID = trim($payment['allpay_card_account']);

        

        /*$szRetUrl = return_url(basename(__FILE__, '.php'))."&log_id=".$order['log_id'];

        $szRetUrl = str_ireplace('/mobile/', '/', $szRetUrl);*/
        
        $szRetUrl = "http://127.0.0.1:8000/payed";
        

        $this->Send['ReturnURL'] = "http://127.0.0.1/***REMOVED***2/respond.php?code=allpay_card"."&log_id=".$order['log_id'];//$szRetUrl . '&background=1';

        $this->Send['ClientBackURL'] = "http://127.0.0.1:8000/payed";

        $this->Send['OrderResultURL'] = $szRetUrl;

        $this->Send['MerchantTradeNo'] = $order['order_sn'];

        $this->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');

        $this->Send['TotalAmount'] = (int)$order['order_amount'];

        $this->Send['TradeDesc'] = "AllPay_ECShop_Module";

        $this->Send['ChoosePayment'] = PaymentMethod::Credit;

        $this->Send['Remark'] = '';

        $this->Send['ChooseSubPayment'] = PaymentMethodItem::None;

        //$this->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::No;
		$this->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::Yes;

        $this->Send['DeviceSource'] = DeviceType::PC;

        

        array_push($this->Send['Items'], array('Name' => $GLOBALS['_LANG']['text_goods'], 'Price' => intval($order['order_amount']), 'Currency' => $GLOBALS['_LANG']['text_currency'], 'Quantity' => 1, 'URL' => ''));

        

        return $this->CheckOutString($GLOBALS['_LANG']['pay_button']);

    }



    /**

     * 處理函數

     */

    function respond() {

        $arPayment = get_payment('allpay_card');
        $isTestMode = ($arPayment['allpay_card_test_mode'] == 'Yes');

        $arFeedback = null;

        $arQueryFeedback = null;

        $szLogID = $_GET['log_id'];
		$cardno = $_GET['card6no'];
        
       /*
        * 為確保金流資訊安全,所以將資料庫相關資訊加密後儲存,如要正常使用
        * 則必須要執行加解密動作
        *
        */
        
        // 宣告兩個暫存的變數分別用來存放解密後的KEY 及 IV 
        //$tmpHashKey = ecEncryptDecrypt( $arPayment['allpay_card_account'] , trim($arPayment['allpay_card_key']) , 1);
        //$tmpHashIv  = ecEncryptDecrypt( $arPayment['allpay_card_account'] , trim($arPayment['allpay_card_iv'])  , 1);

        $this->HashKey = trim( ecEncryptDecrypt($arPayment['allpay_card_account'],$arPayment['allpay_card_key'],1) );

        $this->HashIV = trim( ecEncryptDecrypt($arPayment['allpay_card_account'],$arPayment['allpay_card_iv'],1));

        //$this->HashKey = trim($tmpHashKey);
        //$this->HashIV  = trim($tmpHashIv) ;
        
        /* 下列兩行為尚未採用加密前的做法
        $this->HashKey = trim($arPayment['allpay_card_key']);
        $this->HashIV = trim($arPayment['allpay_card_iv']);
        */
        try {

            // 取得回傳的付款結果。

            $arFeedback = $this->CheckOutFeedback();

            if (sizeof($arFeedback) > 0) {

                // 查詢付款結果資料。

                $this->ServiceURL = ($isTestMode ? "https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V2" : "https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V2");

                $this->MerchantID = trim($arPayment['allpay_card_account']);

                $this->Query['MerchantTradeNo'] = $arFeedback['MerchantTradeNo'];

                $arQueryFeedback = $this->QueryTradeInfo();


                if (sizeof($arQueryFeedback) > 0) {

                    // 檢查支付金額與訂單是否相符。

                    if (check_money($szLogID, $arFeedback['TradeAmt']) && $arQueryFeedback['TradeAmt'] == $arFeedback['TradeAmt']) {

                        $szCheckAmount = '1';

                    }

                    // 確認付款結果。

                    if ($arFeedback['RtnCode'] == '1' && $szCheckAmount == '1' && $arQueryFeedback["TradeStatus"] == '1') {

                        //$szNote = $GLOBALS['_LANG']['text_paid'] . date("Y-m-d H:i:s");
						$szNote = '卡號：'.$arFeedback['card4no'].'<br>'.$GLOBALS['_LANG']['text_paid'] . date("Y-m-d H:i:s");

						order_paid($szLogID, PS_PAYED, $szNote, $arFeedback['card4no']);
                        //order_paid($szLogID, PS_PAYED, $szNote);



                        if ($_GET['background']){

                            echo '1|OK';

                            exit;

                        } else {

                            return true;

                        }

                    } else {

                        if ($_GET['background']){

                            echo (!$szCheckAmount ? '0|訂單金額不符。' : $arFeedback['RtnMsg']);

                            exit;

                        } else {

                            return false;

                        }

                    }

                } else {

                    throw new Exception('AllPay 查無訂單資料。');

                }

            }

        } catch (Exception $ex) { /* 例外處理 */
            
            var_dump($ex);
        }



        return false;

    }



}

