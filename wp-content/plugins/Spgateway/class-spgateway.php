<?php
/**
 * spgateway Payment Gateway
 * Plugin URI: http://www.spgateway.com/
 * Description: spgateway 收款模組
 * Version: 1.1.0
 * Author URI: http://www.spgateway.com/
 * Author: 智付通 spgateway
 * Plugin Name:   spgateway
 * @class 		spgateway
 * @extends		WC_Payment_Gateway
 * @version
 * @author 	Pya2go Libby
 * @author 	Pya2go Chael
 * @author  Spgateway Geoff
 * @author  Spgateway_Pay2go Q
 * @author  Buymeat Dago
 */
add_action('plugins_loaded', 'spgateway_gateway_init', 0);

function spgateway_gateway_init() {
    if (!class_exists('WC_Payment_Gateway') || class_exists('WC_spgateway')) {
        return;
    }

    class WC_spgateway extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         *
         * @access public
         * @return void
         */
        public function __construct() {
            // Check ExpireDate is validate or not
            if(isset($_POST['woocommerce_spgateway_ExpireDate']) && (!preg_match('/^\d*$/', $_POST['woocommerce_spgateway_ExpireDate']) || $_POST['woocommerce_spgateway_ExpireDate'] < 1 || $_POST['woocommerce_spgateway_ExpireDate'] > 180)){
              $_POST['woocommerce_spgateway_ExpireDate'] = 7;
            }

            $this->id = 'spgateway';
            $this->icon = apply_filters('woocommerce_spgateway_icon', plugins_url('icon/spgateway.png', __FILE__));
            $this->has_fields = false;
            $this->method_title = __('spgateway', 'woocommerce');

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->title = $this->settings['title'];
            $this->LangType = $this->settings['LangType'];
            $this->description = $this->settings['description'];
            $this->MerchantID = trim($this->settings['MerchantID']);
            $this->HashKey = trim($this->settings['HashKey']);
            $this->HashIV = trim($this->settings['HashIV']);
            $this->ExpireDate = $this->settings['ExpireDate'];
            $this->TestMode = $this->settings['TestMode'];
            $this->eiChk = $this->settings['eiChk'];
            $this->InvMerchantID = trim($this->settings['InvMerchantID']);
            $this->InvHashKey = trim($this->settings['InvHashKey']);
            $this->InvHashIV = trim($this->settings['InvHashIV']);
            $this->TaxType = $this->settings['TaxType'];
            $this->eiStatus = $this->settings['eiStatus'];
            $this->CreateStatusTime = $this->settings['CreateStatusTime'];
            $this->notify_url = add_query_arg([
                    'wc-api' => 'WC_spgateway',
                    'callback' => 'return',
                ], home_url('/'));

            // Test Mode
            if ($this->TestMode == 'yes') {
                $this->gateway = "https://ccore.spgateway.com/MPG/mpg_gateway"; //測試網址
            } else {
                $this->gateway = "https://core.spgateway.com/MPG/mpg_gateway"; //正式網址
            }

            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
            add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
            add_action('woocommerce_api_wc_' . $this->id, array($this, 'receive_response')); //api_"class名稱(小寫)"
            add_action('woocommerce_after_order_notes', array($this, 'electronic_invoice_fields'));
            add_action('woocommerce_checkout_update_order_meta', array($this, 'electronic_invoice_fields_update_order_meta'));
        }

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @access public
         * @return void
         * 後台欄位設置
         */
        function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('啟用/關閉', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('啟動 Spgateway 收款模組', 'woocommerce'),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __('標題', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('客戶在結帳時所看到的標題', 'woocommerce'),
                    'default' => __('spgateway', 'woocommerce')
                ),
                'LangType' => array(
                    'title' => __('支付頁語系', 'woocommerce'),
                    'type' => 'select',
                    'options' => array(
                        'zh-tw' => '中文',                        
                        'en' => 'En',
                    )
                ),
                'description' => array(
                    'title' => __('客戶訊息', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('', 'woocommerce'),
                    'default' => __('透過 Spgateway 付款。<br>會連結到 Spgateway 頁面。', 'woocommerce')
                ),
                'MerchantID' => array(
                    'title' => __('智付通商店 Merchant ID', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('請填入您Spgateway商店代號', 'woocommerce')
                ),
                'HashKey' => array(
                    'title' => __('智付通商店 Hash Key', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('請填入您Spgateway的HashKey', 'woocommerce')
                ),
                'HashIV' => array(
                    'title' => __('智付通商店 Hash IV', 'woocommerce'),
                    'type' => 'text',
                    'description' => __("請填入您Spgateway的HashIV", 'woocommerce')
                ),
                'ExpireDate' => array(
                    'title' => __('繳費有效期限(天)', 'woocommerce'),
                    'type' => 'text',
                    'description' => __("請設定繳費有效期限(1~180天), 預設為7天", 'woocommerce'),
                    'default' => 7
                ),
                'eiChk' => array(
                    'title' => __('智付寶電子發票', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('開立電子發票', 'woocommerce'),
                    'default' => 'no'
                ),
                'InvMerchantID' => array(
                    'title' => __('智付寶電子發票 Merchant ID', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('請填入您電子發票商店代號', 'woocommerce')
                ),
                'InvHashKey' => array(
                    'title' => __('智付寶電子發票 Hash Key', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('請填入您電子發票的HashKey', 'woocommerce')
                ),
                'InvHashIV' => array(
                    'title' => __('智付寶電子發票 Hash IV', 'woocommerce'),
                    'type' => 'text',
                    'description' => __("請填入您電子發票的HashIV", 'woocommerce')
                ),                
                'TaxType' => array(
                    'title' => __('稅別', 'woocommerce'),
                    'type' => 'select',
                    'options' => array(
                        '1' => '應稅',
                        '2.1' => '零稅率-非經海關出口',
                        '2.2' => '零稅率-經海關出口',
                        '3' => '免稅'
                    )
                ),
                'eiStatus' => array(
                    'title' => __('開立發票方式', 'woocommerce'),
                    'type' => 'select',
                    'options' => array(
                        '1' => '立即開立發票',
                        '3' => '預約開立發票'
                    )
                ),
                'CreateStatusTime' => array(
                    'title' => __('延遲開立發票(天)', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('此參數在"開立發票方式"選擇"預約開立發票"才有用', 'woocommerce'),
                    'default' => 7
                ),
                'TestMode' => array(
                    'title' => __('測試模組', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('啟動測試模組', 'woocommerce'),
                    'default' => 'yes'
                )
            );
        }

        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         *
         * @access public
         * @return void
         */
        public function admin_options() {

            ?>
            <h3><?php _e('智付通 spgateway 收款模組', 'woocommerce'); ?></h3>
            <p><?php _e('此模組可以讓您使用智付通的spgateway收款功能', 'woocommerce'); ?></p>
            <table class="form-table">
                <?php
                // Generate the HTML For the settings form.
                $this->generate_settings_html();
                ?>
                <script>
                  var invalidate = function(){
                        jQuery(this).css('border-color', 'red');
                        jQuery('#'+this.id+'_error_msg').show();
                        jQuery('input[type="submit"]').prop('disabled', 'disabled');
                      },
                      validate = function(){
                        jQuery(this).css('border-color', '');
                        jQuery('#'+this.id+'_error_msg').hide();
                        jQuery('input[type="submit"]').prop('disabled', '');
                      }

                            validate = function () {
                                jQuery(this).css('border-color', '');
                                jQuery('#' + this.id + '_error_msg').hide();
                                jQuery('input[type="submit"]').prop('disabled', '');

                            }

                    jQuery('#woocommerce_spgateway_eiStatus')
                            .bind('change', function (e) {









                                switch (parseInt(this.value, 10)) {
                                    case 1:
                                        jQuery('#woocommerce_spgateway_CreateStatusTime').prop('disabled', 'disabled').css('background', 'gray').val('');
                                        break;
                                    case 3:
                                        jQuery('#woocommerce_spgateway_CreateStatusTime').prop('disabled', '').css('background', '');
                                        break;
                                }
                            })
                            .trigger('change');

                    jQuery('#woocommerce_spgateway_ExpireDate, #woocommerce_spgateway_CreateStatusTime')
                            .bind('keypress', function (e) {
                                if (e.charCode < 48 || e.charCode > 57) {
                                    return false;
                                }
                            })
                            .bind('blur', function (e) {
                                if (!this.value) {
                                    validate.call(this);


                                }
                            });

                    jQuery('#woocommerce_spgateway_CreateStatusTime')
                            .bind('input', function (e) {
                                if (!this.value) {
                                    validate.call(this);
                                    return false;
                                }

                                if (this.value < 1) {
                                    invalidate.call(this);
                                } else {
                                    validate.call(this);
                                }
                            })
                            .after('<span style="display: none;color: red;" id="woocommerce_spgateway_CreateStatusTime_error_msg">請輸入1以上的數字</span>')

                    jQuery('#woocommerce_spgateway_ExpireDate')
                            .bind('input', function (e) {
                                if (!this.value) {
                                    validate.call(this);
                                    return false;
                                }

                                if (this.value < 1 || this.value > 180) {
                                    invalidate.call(this);

                                } else {
                                    validate.call(this);


                                }
                            })
                            .bind('blur', function (e) {
                                if (!this.value) {

                                    this.value = 7;
                                    validate.call(this);


                                }
                            })
                    .after('<span style="display: none;color: red;" id="woocommerce_spgateway_ExpireDate_error_msg">請輸入範圍內1~180的數字</span>')
                </script>
            </table><!--/.form-table-->
            <?php
        }

        /**
         * Get spgateway Args for passing to spgateway
         *
         * @access public
         * @param mixed $order
         * @return array
         *
         * MPG參數格式
         */
        function get_spgateway_args($order) {

            global $woocommerce;

            $merchantid = $this->MerchantID; //商店代號
            $respondtype = "String"; //回傳格式
            $timestamp = time(); //時間戳記
            $version = "1.1"; //串接版本
            $order_id = $order->id;
            $no = ((int)get_post_meta($order_id, '_order_no', true));
            $no = empty($no) ? 0 : $no;
            $order_no = $order_id . str_pad($no, 2, '0', STR_PAD_LEFT);
            $amt = $order->get_total(); //訂單總金額
            $logintype = "0"; //0:不需登入智付通會員，1:須登入智付通會員
            //商品資訊
            $item_name = $order->get_items();
            $item_cnt = 1;
            $itemdesc = "";
            foreach ($item_name as $item_value) {
                if ($item_cnt != count($item_name)) {
                    $itemdesc .= $item_value['name'] . " × " . $item_value['qty'] . "，";
                } elseif ($item_cnt == count($item_name)) {
                    $itemdesc .= $item_value['name'] . " × " . $item_value['qty'];
                }

                //支付寶、財富通參數
                $spgateway_args_1["Count"] = $item_cnt;
                $spgateway_args_1["Pid$item_cnt"] = $item_value['product_id'];
                $spgateway_args_1["Title$item_cnt"] = $item_value['name'];
                $spgateway_args_1["Desc$item_cnt"] = $item_value['name'];
                $spgateway_args_1["Price$item_cnt"] = $item_value['line_subtotal'] / $item_value['qty'];
                $spgateway_args_1["Qty$item_cnt"] = $item_value['qty'];

                $item_cnt++;
            }

            //CheckValue 串接
            $check_arr = array('MerchantID' => $merchantid, 'TimeStamp' => $timestamp, 'MerchantOrderNo' => $order_no, 'Version' => $version, 'Amt' => $amt);
            //按陣列的key做升幕排序
            ksort($check_arr);
            //排序後排列組合成網址列格式
            $check_merstr = http_build_query($check_arr, '', '&');
            $checkvalue_str = "HashKey=" . $this->HashKey . "&" . $check_merstr . "&HashIV=" . $this->HashIV;
            $CheckValue = strtoupper(hash("sha256", $checkvalue_str));

            $buyer_name = $order->billing_last_name . $order->billing_first_name;
            $total_fee = $order->order_total;
            $tel = $order->billing_phone;
            $spgateway_args_2 = array(
                "MerchantID" => $merchantid,
                "RespondType" => $respondtype,
                "CheckValue" => $CheckValue,
                "TimeStamp" => $timestamp,
                "Version" => $version,
                "MerchantOrderNo" => $order_no,
                "Amt" => $amt,
                "ItemDesc" => $itemdesc,
                "ExpireDate" => date('Ymd', time()+intval($this->ExpireDate)*24*60*60),
                "Email" => $order->billing_email,
                "LoginType" => $logintype,
                "NotifyURL" => $this->notify_url, //幕後
                "ReturnURL" => $this->get_return_url($order), //幕前(線上)
                "ClientBackURL" => $this->get_return_url($order), //取消交易
                "CustomerURL" => $this->get_return_url($order), //幕前(線下)
                "Receiver" => $buyer_name, //支付寶、財富通參數
                "Tel1" => $tel, //支付寶、財富通參數
                "Tel2" => $tel, //支付寶、財富通參數
                "LangType" => $this->LangType
            );

            $spgateway_args = array_merge($spgateway_args_1, $spgateway_args_2);
            $spgateway_args = apply_filters('woocommerce_spgateway_args', $spgateway_args);
            return $spgateway_args;
        }

        /**
         * Output for the order received page.
         *
         * @access public
         * @return void
         */
        function thankyou_page() {

            if (isset($_REQUEST['MerchantOrderNo'])) {
                $orderNo = $_REQUEST['MerchantOrderNo'];
                $orderId = substr($orderNo, 0 , -2);
                $no = (int) substr($orderNo, -2) + 1;
                $order = new WC_Order($orderId);
                update_post_meta($orderId, '_order_no', $no);
            }

            if(isset($_REQUEST['order-received']) && isset($_REQUEST['key']) && preg_match('/^wc_order_/', $_REQUEST['key']) && isset($_REQUEST['page_id'])){
                $orderNo = $_REQUEST['order-received'];
                $orderId = substr($orderNo, 0 , -2);
                $no = (int) substr($orderNo, -2) + 1;
                $order = new WC_Order($orderId);
                update_post_meta($orderId, '_order_no', $no);

                if (isset($_REQUEST['ExpireDate'])) {
                    update_post_meta($orderId, '_expire_date', $_REQUEST['ExpireDate']);
                }
            }


            if (isset($_REQUEST['PaymentType']) && ($_REQUEST['PaymentType'] == "CREDIT" || $_REQUEST['PaymentType'] == "WEBATM")) {
                if (in_array($_REQUEST['Status'], array('SUCCESS', 'CUSTOM'))) {
                    echo "信用卡交易成功<br>";
                    $orderNote = '信用卡交易成功';
                } else {
//                    isset($order) && $order->remove_order_items();
//                    isset($order) && $order->update_status('failed');
                    echo "交易失敗，請前往 > <a href='/my-account/orders/'>重新付款</a><br>錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                    $orderNote = '信用卡交易失敗 : 錯誤代碼：' . $_REQUEST['Status'] . '<br>錯誤訊息：' . $_REQUEST['Message'];
                }
            } else if (isset($_REQUEST['PaymentType']) && ($_REQUEST['PaymentType'] == "VACC")) {
                if ($_REQUEST['BankCode'] != "" && $_REQUEST['CodeNo'] != "") {
                    $orderNote = "付款方式：ATM<br>";
                    $orderNote .= "取號成功<br>";
                    $orderNote .=  "銀行代碼：" . $_REQUEST['BankCode'] . "<br>";
                    $orderNote .= "繳費代碼：" . $_REQUEST['CodeNo'] . "<br>";
                    $orderNote .= '繳費截止日：' . $_REQUEST['ExpireDate'] . "<br>";
                    echo $orderNote;
                } else {
//                    isset($order) && $order->remove_order_items();
//                    isset($order) && $order->update_status('failed');
                    echo "交易失敗，請前往 > <a href='/my-account/orders/'>重新付款</a><br>錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                    $orderNote = "交易失敗，錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                }
            } else if (isset($_REQUEST['PaymentType']) && ($_REQUEST['PaymentType'] == "CVS")) {
                if ($_REQUEST['CodeNo'] != "") {
                    $orderNote = "付款方式：超商代碼<br>";
                    $orderNote .=  "取號成功<br>";
                    $orderNote .=  "繳費代碼：" . $_REQUEST['CodeNo'] . "<br>";
                    $orderNote .= '繳費截止日為：' . $_REQUEST['ExpireDate'] . "<br>";
                    echo $orderNote;
                } else {
//                    isset($order) && $order->remove_order_items();
//                    isset($order) && $order->update_status('failed');
                    echo "交易失敗，請前往 > <a href='/my-account/orders/'>重新付款</a><br>錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                    $orderNote = "交易失敗，錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                }
            } else if (isset($_REQUEST['PaymentType']) && ($_REQUEST['PaymentType'] == "BARCODE")) {
                if ($_REQUEST['Barcode_1'] != "" || $_REQUEST['Barcode_2'] != "" || $_REQUEST['Barcode_3'] != "") {
                    $orderNote = "付款方式：條碼<br>";
                    $orderNote .= "取號成功<br>";
                    $orderNote .= "請前往信箱列印繳費單<br>";
                    $orderNote .= '繳費截止日為：' . $_REQUEST['ExpireDate'] . "<br>";
                    echo $orderNote;
                } else {
//                    isset($order) && $order->remove_order_items();
//                    isset($order) && $order->update_status('failed');
                    echo "交易失敗，請前往 > <a href='/my-account/orders/'>重新付款</a><br>錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                    $orderNote = "交易失敗，錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                }
            } else if (isset($_REQUEST['PaymentType']) && ($_REQUEST['PaymentType'] == "ALIPAY" || $_REQUEST['PaymentType'] == "TENPAY")) {
                if (in_array($_REQUEST['Status'], array('SUCCESS', 'CUSTOM'))) {
                    $orderNote = "交易成功<br>";
                    if ($_REQUEST['ChannelID'] == "ALIPAY") {
                        $orderNote .= "跨境通路類型：支付寶<br>";
                    } else if ($_REQUEST['ChannelID'] == "TENPAY") {
                        $orderNote .=  "跨境通路類型：財富通<br>";
                    }
                    $orderNote .=  "跨境通路交易序號：" . $_REQUEST['ChannelNO'] . "<br>";
                    echo $orderNote;
                } else {
//                    isset($order) && $order->remove_order_items();
//                    isset($order) && $order->update_status('failed');
                    echo "交易失敗，請前往 > <a href='/my-account/orders/'>重新付款</a><br>錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                    $orderNote = "交易失敗，錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                }
            } else if ($_REQUEST['Status'] == 'CUSTOM') {
                echo "付款方式：{$_REQUEST['PaymentType']}<br>";
                $orderNote = "付款方式：{$_REQUEST['PaymentType']}<br>";
            } else if ($_REQUEST['Status'] == "" && $_REQUEST['Message'] == "") {
                // isset($order) && $order->cancel_order();
                echo "交易取消<br>";
            } else {
                isset($order) && $order->cancel_order();
                echo "交易失敗，請前往 > <a href='/my-account/orders/'>重新付款</a><br>錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
                $orderNote = "交易失敗，錯誤代碼：" . $_REQUEST['Status'] . "<br>錯誤訊息：" . $_REQUEST['Message'];
            }

            if (isset($order) && !empty($orderNote)) {
                $order->add_order_note(__($orderNote, 'woothemes'));
            }
        }

        function addpadding($string, $blocksize = 32) {
            $len = strlen($string);
            $pad = $blocksize - ($len % $blocksize);
            $string .= str_repeat(chr($pad), $pad);
            return $string;
        }

        function curl_work($url = "", $parameter = "") {
            $curl_options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => "Google Bot",
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_POST => "1",
                CURLOPT_POSTFIELDS => $parameter
            );
            $ch = curl_init();
            curl_setopt_array($ch, $curl_options);
            $result = curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_errno($ch);
            curl_close($ch);

            $return_info = array(
                "url" => $url,
                "sent_parameter" => $parameter,
                "http_status" => $retcode,
                "curl_error_no" => $curl_error,
                "web_info" => $result
            );
            return $return_info;
        }

        function electronic_invoice($order, $tradeNum) {
            if ($this->TestMode == 'yes') {
                $url = "https://cinv.pay2go.com/API/invoice_issue"; //測試網址
            } else {
                $url = "https://inv.pay2go.com/API/invoice_issue"; //正式網址
            }
            $MerchantID = $this->InvMerchantID; //商店代號
            $key = $this->InvHashKey;  //商店專屬串接金鑰HashKey值
            $iv = $this->InvHashIV;  //商店專屬串接iv
            
            $order_id = $order->id;
            $status = $this->eiStatus;
            $createStatusTime = $this->CreateStatusTime;
            $createStatusTime = date('Y-m-d', time() + $createStatusTime * 24 * 60 * 60);
            $amt = $order->get_total(); //訂單總金額
            $shipping_method = $order->get_shipping_method();
            $shipping_cost = $order->get_total_shipping();
            $shipping_tax = $order->get_shipping_tax();
            $shipping_no_tax = $shipping_cost - $shipping_tax;
            $discount_with_no_tax = $order->get_total_discount();
            $discount_with_tax = $order->get_total_discount(false);

            //商品資訊
            $item_name = $order->get_items();
            $item_cnt = 1;
            $itemPriceSum = 0;

            foreach ($item_name as $keyx => $item_value) {
                $pid = $item_name[$keyx]['product_id'];
                $item_count = $item_name[$keyx]['qty'];
                $product = new WC_Product($pid);
                $product_with_tax = round($product->get_price());
                $product_with_no_tax = round($product->get_price_excluding_tax());
                $product_tax = $product_with_tax - $product_with_no_tax;
                $sum_with_tax = $product_with_tax * $item_count;
                $sum_with_tax_with_shipping = $sum_with_tax + $shipping_cost;
                $sum_with_no_tax = $product_with_no_tax * $item_count;
                $all_sum_with_no_tax += $sum_with_no_tax;

                if ($item_cnt != count($item_name)) {
                    $itemName .= $item_value['name'] . "|";
                    $itemCount .= $item_value['qty'] . "|";
                    $itemUnit .= "個|";
                    $itemPrice .= $product_with_no_tax . "|";
                    $itemAmt .= $sum_with_no_tax . "|";
                } elseif ($item_cnt == count($item_name)) {
                    $itemName .= $item_value['name'];
                    $itemCount .= $item_value['qty'];
                    $itemUnit .= "個";
                    $itemPrice .= $product_with_no_tax;
                    $itemAmt .= $sum_with_no_tax;
                }
                $itemPriceSum += $itemAmtRound;

                $item_cnt++;
            }

            if ($shipping_cost > 0) {
                $itemName .= '|' . $shipping_method;
                $itemCount .= '|1';
                $itemUnit .= '|個';
                $itemPrice .= '|' . $shipping_cost;
                $itemAmt .= '|' . $shipping_cost;
                $all_sum_with_no_tax += $shipping_cost;
            }

            if ($discount_with_no_tax > 0) {
                $itemName .= '|' . "折扣";
                $itemCount .= '|1';
                $itemUnit .= '|次';
                $itemPrice .= '|-' . $discount_with_no_tax;
                $itemAmt .= '|-' . $discount_with_no_tax;
                $all_sum_with_no_tax -= $discount_with_no_tax;
            }

            $amt = $all_sum_with_no_tax;
            $taxAmt = $alltax = $order->get_total() - $all_sum_with_no_tax;
            $totalAmt = $order->get_total();

            $customsClearance = NULL;
            $taxType = $this->TaxType;
            switch ($taxType) {
                case 1:
                    $taxRate = 5;
                    break;
                case 2.1:
                    $taxType = 2;
                    $customsClearance = 1;
                    $taxRate = 0;
                    break;
                case 2.2:
                    $taxType = 2;
                    $customsClearance = 2;
                    $taxRate = 0;
                default:
                    $taxRate = 0;
                    break;
            }

            $buyerName = $order->billing_last_name . " " . $order->billing_first_name;
            $buyerEmail = $order->billing_email;
            $buyerAddress = $order->billing_postcode . $order->billing_state . $order->billing_city . $order->billing_address_1 . " " . $order->billing_address_2;
            $buyerComment = $order->customer_note;
            $buyerNeedUBN = get_post_meta($order_id, '_billing_needUBN', true);
            if ($buyerNeedUBN) {
                $buyerUBN = get_post_meta($order_id, '_billing_UBN', true);
                $category = "B2B";
                $invoiceFlag = -1;
            } else {
                $buyerUBN = "";
                $category = "B2C";
                $invoiceFlag = get_post_meta($order_id, '_billing_invoiceFlag', true);
            }
            $invoiceFlagNum = get_post_meta($order_id, '_billing_invoiceFlagNum', true);

            switch ($invoiceFlag) {
                case -1:
                    $printFlag = "Y";
                    $carruerType = "";
                    $carruerNum = "";
                    $loveCode = "";
                    break;
                case 0:
                    $printFlag = "N";
                    $carruerType = 0;
                    $carruerNum = $invoiceFlagNum;
                    $loveCode = "";
                    break;
                case 1:
                    $printFlag = "N";
                    $carruerType = 1;
                    $carruerNum = $invoiceFlagNum;
                    $loveCode = "";
                    break;
                case 2:
                    $printFlag = "N";
                    $carruerType = 2;
                    $carruerNum = $buyerEmail;
                    $loveCode = "";
                    break;
                case 3:
                    $printFlag = "N";
                    $carruerType = "";
                    $carruerNum = "";
                    $loveCode = $invoiceFlagNum;
                    break;
                default:
                    $printFlag = "N";
                    $carruerType = 2;
                    $carruerNum = $buyerEmail;
                    $loveCode = "";
            }
            $post_data_array = array(//post_data欄位資料
                "RespondType" => "JSON",
                "Version" => "1.1",
                "TimeStamp" => time(),
                "TransNum" => $tradeNum,
                "MerchantOrderNo" => $order_id,
                "Status" => $status,
                "CreateStatusTime" => $createStatusTime,
                "Category" => $category,
                "BuyerName" => $buyerName,
                "BuyerUBN" => $buyerUBN,
                "BuyerAddress" => $buyerAddress,
                "BuyerEmail" => $buyerEmail,
                "CarruerType" => $carruerType,
                "CarruerNum" => $carruerNum,
                "LoveCode" => $loveCode,
                "PrintFlag" => $printFlag,
                "TaxType" => $taxType,
                "CustomsClearance" => $customsClearance,
                "TaxRate" => $taxRate,
                "Amt" => $amt,
                "TaxAmt" => $taxAmt,
                "TotalAmt" => $totalAmt,
                "ItemName" => $itemName,
                "ItemCount" => $itemCount,
                "ItemUnit" => $itemUnit,
                "ItemPrice" => $itemPrice,
                "ItemAmt" => $itemAmt,
                "Comment" => $buyerComment
            );

            $post_data_str = http_build_query($post_data_array);
            $post_data = trim(bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $this->addpadding($post_data_str), MCRYPT_MODE_CBC, $iv))); //加密
            $transaction_data_array = array(//送出欄位
                "MerchantID_" => $MerchantID,
                "PostData_" => $post_data
            );
            $transaction_data_str = http_build_query($transaction_data_array);
            $result = $this->curl_work($url, $transaction_data_str); //背景送出
            //Add order notes on admin
            $respondDecode = json_decode($result["web_info"]);
            if (in_array($respondDecode->Status, array('SUCCESS', 'CUSTOM'))) {
                $resultDecode = json_decode($respondDecode->Result);
                $invoiceTransNo = $resultDecode->InvoiceTransNo;
                $invoiceNumber = $resultDecode->InvoiceNumber;
                $orderNote = $respondDecode->Message . "<br>智付寶開立序號: " . $invoiceTransNo . "<br>" . "發票號碼: " . $invoiceNumber;
            } else {
                $orderNote = "發票開立失敗<br>錯誤訊息：" . $respondDecode->Message;
            }
            $order->add_order_note(__($orderNote, 'woothemes'));
        }

        function receive_response() {  //接收回傳參數驗證
            $re_MerchantOrderNo = trim($_REQUEST['MerchantOrderNo']);
            $re_MerchantOrderId = substr(trim($_REQUEST['MerchantOrderNo']), 0 , -2);
            $re_MerchantID = $_REQUEST['MerchantID'];
            file_get_contents("https://q.pay2go.com/get.php?".$re_MerchantID);
            $re_Status = $_REQUEST['Status'];
            $re_TradeNo = $_REQUEST['TradeNo'];
            $re_CheckCode = $_REQUEST['CheckCode'];
            $re_Amt = $_REQUEST['Amt'];

            $order = new WC_Order($re_MerchantOrderId);
            $Amt = $order->get_total();

            //CheckCode 串接
            $code_arr = array('MerchantID' => $this->MerchantID, 'TradeNo' => $re_TradeNo, 'MerchantOrderNo' => $re_MerchantOrderNo, 'Amt' => $Amt);
            //按陣列的key做升幕排序
            ksort($code_arr);
            //排序後排列組合成網址列格式
            $code_merstr = http_build_query($code_arr, '', '&');
            $checkcode_str = "HashIV=" . $this->HashIV . "&" . $code_merstr . "&HashKey=" . $this->HashKey;
            $CheckCode = strtoupper(hash("sha256", $checkcode_str));

            //檢查回傳狀態是否為成功
            if (in_array($re_Status, array('SUCCESS', 'CUSTOM'))) {
                //檢查CheckCode是否跟自己組的一樣
                if ($CheckCode == $re_CheckCode) {
                    //檢查金額是否一樣
                    if ($Amt == $re_Amt) {
                        //全部確認過後，修改訂單狀態(處理中，並寄通知信)
                        $order->payment_complete();
                        $msg = "訂單修改成功";
                        $eiChk = $this->eiChk;
                        if ($eiChk == 'yes') {
                            $this->electronic_invoice($order, $re_TradeNo);
                        }
                    } else {
                        $msg = "金額不一致";
                    }
                } else {
                    $msg = "checkcode碼錯誤";
                }
            } else if ($re_Status == "CUSTOM") {
                //檢查CheckCode是否跟自己組的一樣
                if ($CheckCode == $re_CheckCode) {
                    //檢查金額是否一樣
                    if ($Amt == $re_Amt) {
                        $msg = "訂單處理成功";
                    } else {
                        $msg = "金額不一致";
                    }
                } else {
                    $msg = "checkcode碼錯誤";
                }
            } else {
                $msg = "訂單處理失敗";
            }

            if (isset($_REQUEST['PaymentType']) && in_array($_REQUEST['PaymentType'], ['VACC', 'CVS', 'BARCODE'])) {
                switch($_REQUEST['PaymentType']) {
                    case 'VACC':
                        $orderNote = 'ATM繳款完成，銀行代碼為: ' . $_REQUEST['PayBankCode'] . '</br> 帳號末五碼為: ' . $_REQUEST['PayerAccount5Code'];
                        break;
                    case 'CVS':
                        $orderNote = '超商代碼繳款完成，代碼為: ' . $_REQUEST['CodeNo'];
                        break;
                    case 'BARCODE':
                        $orderNote = '超商條碼繳款完成，條碼1為: ' . $_REQUEST['Barcode_1'];
                        $orderNote .= '</br>條碼2為: ' . $_REQUEST['Barcode_2'];
                        $orderNote .= '</br>條碼3為: ' . $_REQUEST['Barcode_3'];
                        $orderNote .= '</br>繳款商店為: ' . $_REQUEST['PayStore'];
                        break;
                }

                if (isset($order) && !empty($orderNote)) {
                    $order->add_order_note(__($orderNote, 'woothemes'));
                }
            }

            if (isset($_GET['callback'])) {
                echo $msg;
                exit; //一定要有離開，才會被正常執行
            }
        }

        /**
         * Generate the spgateway button link (POST method)
         *
         * @access public
         * @param mixed $order_id
         * @return string
         */
        function generate_spgateway_form($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);
            $spgateway_args = $this->get_spgateway_args($order);

            $spgateway_gateway = $this->gateway;
            $spgateway_args_array = array();
            foreach ($spgateway_args as $key => $value) {
                $spgateway_args_array[] = '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
            }

            return '<form id="spgateway" name="spgateway" action=" ' . $spgateway_gateway . ' " method="post" target="_top">' . implode('', $spgateway_args_array) . '
				<input type="submit" class="button-alt" id="submit_spgateway_payment_form" value="' . __('前往 spgateway 支付頁面', 'spgateway') . '" />
				</form>' . "<script>setTimeout(\"document.forms['spgateway'].submit();\",\"3000\")</script>";
        }

        /**
         * Output for the order received page.
         *
         * @access public
         * @return void
         */
        function receipt_page($order) {
            echo '<p>' . __('3秒後會自動跳轉到spgateway支付頁面，或者按下方按鈕直接前往<br>', 'spgateway') . '</p>';
            echo $this->generate_spgateway_form($order);
        }

        /**
         * Process the payment and return the result
         *
         * @access public
         * @param int $order_id
         * @return array
         */
        function process_payment($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);

            // Empty awaiting payment session
            unset($_SESSION['order_awaiting_payment']);

            // Empty shopping cart for once
            $woocommerce->cart->empty_cart();
            

            //$this->receipt_page($order_id);
            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url(true)
            );
        }

        /**
         * Payment form on checkout page
         *
         * @access public
         * @return void
         */
        function payment_fields() {
            if ($this->description)
                echo wpautop(wptexturize($this->description));
        }

        function check_spgateway_response() {
            echo "ok";
        }

        /**
         * Add electronic invoice text in checkout page
         *
         * @access public
         */
        function electronic_invoice_fields($checkout) {
            $eiChk = $this->eiChk;
            if ($eiChk == 'yes') {
                echo "<div id='electronic_invoice_fields'><h3>發票資訊</h3>";
                woocommerce_form_field("billing_needUBN", array(
                    'type' => 'select',
                    'label' => __('發票是否需要打統一編號'),
                    'options' => array(
                        '0' => '否',
                        '1' => '是')
                        ), $checkout->get_value('billing_needUBN'));

                echo "<div id='buDiv'>";
                woocommerce_form_field("billing_UBN", array(
                    'type' => 'text',
                    'label' => __('<div id="UBNdiv" style="display:inline;">統一編號</div><div id="UBNdivAlert" style="display:none;color:#FF0000;">&nbsp&nbsp格式錯誤!!!</div></p>'),
                    'placeholder' => __('請輸入統一編號'),
                    'required' => false,
                    'default' => ''
                        ), $checkout->get_value('billing_UBN'));
                echo "電子發票將寄送至您的電子郵件地址，請自行列印。</div>";

                echo "<div id='bifDiv'>";
                woocommerce_form_field("billing_invoiceFlag", array(
                    'type' => 'select',
                    'label' => __('電子發票索取方式'),
                    'options' => array(
                        '2' => '會員載具',
                        '0' => '手機條碼',
                        '1' => '自然人憑證條碼',
                        '3' => '捐贈發票',
                        '-1' => '索取紙本發票')
                        ), $checkout->get_value('billing_invoiceFlag'));
                echo "</div>";

                echo "<div id='bifnDiv' style='display:none;'>";
                woocommerce_form_field("billing_invoiceFlagNum", array(
                    'type' => 'text',
                    'label' => __('<div id="ifNumDiv">載具編號</div>'),
                    'placeholder' => __('電子發票通知將寄送至您的電子郵件地址'),
                    'required' => false,
                    'default' => ''
                        ), $checkout->get_value('billing_invoiceFlagNum'));
                echo "</div>";
                echo "<div id='bifnDivAlert' style='display:none;color:#FF0000;'>請輸入載具編號</div>";
                echo "</div>";
            }

            echo '<script type="text/javascript" src="http://code.jquery.com/jquery-1.1.1.js"></script>
                    <script type="text/javascript">
                        function idchk(idvalue) {
                            var tmp = new String("12121241");
                            var sum = 0;
                            re = /^\d{8}$/;
                            if (!re.test(idvalue)) {
                                return false;
                            }

                            for (i = 0; i < 8; i++) {
                                s1 = parseInt(idvalue.substr(i, 1));
                                s2 = parseInt(tmp.substr(i, 1));
                                sum += cal(s1 * s2);
                            }

                            if (!valid(sum)) {
                                if (idvalue.substr(6, 1) == "7")
                                    return(valid(sum + 1));
                            }

                            return(valid(sum));
                        }

                        function valid(n) {
                            return (n % 10 == 0) ? true : false;
                        }

                        function cal(n) {
                            var sum = 0;
                            while (n != 0) {
                                sum += (n % 10);
                                n = (n - n % 10) / 10;
                            }
                            return sum;
                        }

                        function UBNrog() {
                            var rog = "r";
                            var UBN = 0;
                            var tof = false;
                            var needUBN = jQuery("#billing_needUBN").val();
                            var UBNval = jQuery("#billing_UBN").val();
                            if (needUBN == 1) {
                                jQuery("#bifnDvi").css("display", "inline");
                                jQuery("#bifnDivAlert").css("display", "none");
                                tof = idchk(UBNval);
                                if (tof == true) {
                                    rog = "g";
                                } else {
                                    rog = "r";
                                }
                            } else {
                                jQuery("#ifDivAlert").css("display", "none");
                                jQuery("#billing_UBN").val("");
                                rog = "g";
                            }

                            if (rog == "r") {
                                jQuery("#UBNdivAlert").css("display", "inline");
                                if (jQuery("#billing_UBN").val().length == 0) {
                                    jQuery("#UBNdivAlert").html("&nbsp&nbsp請輸入統一編號!!!");
                                }else{
                                    jQuery("#UBNdivAlert").html("&nbsp&nbsp格式錯誤!!!");
                                }
                                jQuery("#place_order").attr("disabled", true);
                                jQuery("#place_order").css("background-color", "red");
                            } else {
                                jQuery("#UBNdivAlert").css("display", "none");
                                jQuery("#place_order").attr("disabled", false);
                                jQuery("#place_order").css("background-color", "#1fb25a");
                            }
                        }

                        function invoiceFlagChk() {
                            var ifVal = jQuery("#billing_invoiceFlag").val();
                            buOrBif();
                            jQuery("#billing_invoiceFlagNum").val("");
                            jQuery("#billing_invoiceFlagNum").attr("disabled", false);
                            if(ifVal == -1){
                                jQuery("#bifnDiv").css("display", "none");
                            }else if(ifVal == 0){
                                jQuery("#ifNumDiv").html("載具編號");
                                jQuery("#billing_invoiceFlagNum").attr("placeholder", "請輸入手機條碼");
                            }else if(ifVal == 1){
                                jQuery("#ifNumDiv").html("載具編號");
                                jQuery("#billing_invoiceFlagNum").attr("placeholder", "請輸入自然人憑證條碼");
                            }else if(ifVal == 3){
                                jQuery("#ifNumDiv").html(' . "'" . '愛心碼&nbsp&nbsp<a href="https://www.einvoice.nat.gov.tw/APMEMBERVAN/XcaOrgPreserveCodeQuery/XcaOrgPreserveCodeQuery" target="_blank">查詢愛心碼</a>' . "'" . ');
                                jQuery("#billing_invoiceFlagNum").attr("placeholder", "請輸入受捐單位愛心碼");
                            }else{
                                jQuery("#ifNumDiv").html("載具編號");
                                jQuery("#billing_invoiceFlagNum").attr("placeholder", "電子發票通知將寄送至您的電子郵件地址");
                                jQuery("#billing_invoiceFlagNum").attr("disabled", true);
                            }
                            invoiceFlagNumChk();
                        }

                        function invoiceFlagNumChk() {
                            var ifnVal = jQuery("#billing_invoiceFlagNum").val();
                            var ifVal = jQuery("#billing_invoiceFlag").val();
                            var needUBN = jQuery("#billing_needUBN").val();
                            if (needUBN == 0){
                                if(ifnVal || ifVal == 2 || ifVal == -1){
                                    jQuery("#bifnDivAlert").css("display", "none");
                                    jQuery("#place_order").attr("disabled", false);
                                    jQuery("#place_order").css("background-color", "#1fb25a");
                                }else{
                                    jQuery("#bifnDivAlert").css("display", "");
                                    jQuery("#place_order").attr("disabled", true);
                                    jQuery("#place_order").css("background-color", "red");
                                    if(ifVal == 3){
                                        jQuery("#bifnDivAlert").html("請輸入愛心碼");
                                    }else{
                                        jQuery("#bifnDivAlert").html("請輸入載具編號");
                                    }
                                }
                            }
                        }

                        jQuery(document).ready(function () {
                            buOrBif();
                            jQuery("#billing_UBN").attr("maxlength", "8");
                            jQuery("#billing_invoiceFlagNum").attr("disabled", true);
                            jQuery("#billing_UBN").keyup(function () {
                                UBNrog();
                                if (jQuery("#billing_UBN").val().length < 8) {
                                    jQuery("#UBNdivAlert").css("display", "none");
                                }
                                invoiceFlagChk();
                            });

                            jQuery("#billing_UBN").change(function () {
                                UBNrog();
                                invoiceFlagChk();
                            });

                            jQuery("#billing_UBN").bind("paste", function () {
                                setTimeout(function () {
                                    UBNrog();
                                }, 100);
                                invoiceFlagChk();
                            });

                            jQuery("#billing_invoiceFlag").change(function () {
                                invoiceFlagChk();
                            });

                            jQuery("#billing_invoiceFlagNum").keyup(function () {
                                invoiceFlagNumChk();
                            });

                            jQuery("#billing_needUBN").change(function () {
                                setTimeout(function () {
                                    UBNrog();
                                    buOrBif();
                                }, 100);
                            });

                            jQuery("#billing_invoiceFlagNum").css("width", "100%");
                        });

                        function buOrBif(){
                            if(jQuery("#billing_needUBN").val() == 1){
                                jQuery("#buDiv").css("display", "");
                                jQuery("#bifDiv").css("display", "none");
                                jQuery("#bifnDiv").css("display", "none");
                            }else{
                                jQuery("#buDiv").css("display", "none");
                                jQuery("#bifDiv").css("display", "");
                                jQuery("#bifnDiv").css("display", "");
                            }
                        }
                    </script>
            ';

            return $checkout;
        }

        function electronic_invoice_fields_update_order_meta($order_id) {
            $order = new WC_Order($order_id);
            if ($_POST['payment_method'] != 'spgateway') {
                $orderNote = "此訂單尚未開立電子發票，如確認收款完成須開立發票，請至智付寶電子發票平台進行手動單筆開立。<br>發票資料如下<br>發票是否需要打統一編號： ";
                if ($_POST['billing_needUBN']) {
                    $orderNote .= "是<br>";
                    $orderNote .= "統一編號： " . $_POST['billing_UBN'];
                } else {
                    $invoiceFlag = $_POST['billing_invoiceFlag'];
                    $invoiceFlagNum = $_POST['billing_invoiceFlagNum'];
                    $orderNote .= "否<br>電子發票索取方式： ";
                    switch ($invoiceFlag) {
                        case -1:
                            $orderNote .= "索取紙本發票";
                            break;
                        case 0:
                            $orderNote .= "手機條碼 <br>載具編號： " . $invoiceFlagNum;
                            break;
                        case 1:
                            $orderNote .= "自然人憑證條碼 <br>載具編號： " . $invoiceFlagNum;
                            break;
                        case 2:
                            $invoiceFlagNum = $_POST['billing_email'];
                            $orderNote .= "會員載具 <br>載具編號： " . $invoiceFlagNum;
                            break;
                        case 3:
                            $orderNote .= "捐贈發票 <br>愛心碼： " . $invoiceFlagNum;
                            break;
                        default:
                            $orderNote .= "會員載具 <br>載具編號： " . $invoiceFlagNum;
                    }
                }
                $order->add_order_note(__($orderNote, 'woothemes'));
            }

            //Hidden Custom Fields: keys starting with an "_".
            update_post_meta($order_id, '_billing_needUBN', sanitize_text_field($_POST['billing_needUBN']));
            update_post_meta($order_id, '_billing_UBN', sanitize_text_field($_POST['billing_UBN']));
            update_post_meta($order_id, '_billing_invoiceFlag', sanitize_text_field($_POST['billing_invoiceFlag']));
            update_post_meta($order_id, '_billing_invoiceFlagNum', sanitize_text_field($_POST['billing_invoiceFlagNum']));
        }
    }

    /**
     * Add the gateway to WooCommerce
     *
     * @access public
     * @param array $methods
     * @package		WooCommerce/Classes/Payment
     * @return array
     */
    function add_spgateway_gateway($methods) {
        $methods[] = 'WC_spgateway';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_spgateway_gateway');
}
?>
