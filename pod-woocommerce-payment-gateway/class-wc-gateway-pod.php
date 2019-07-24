<?php

if (!defined('ABSPATH'))
    exit;

function Load_Pod_Gateway()
{

    if (class_exists('WC_Payment_Gateway') && !class_exists('WC_PodWallet') && !function_exists('Woocommerce_Add_Pod_Gateway')) {

        add_filter('woocommerce_payment_gateways', 'Woocommerce_Add_Pod_Gateway');

        if (!session_id())
            session_start();

        function Woocommerce_Add_Pod_Gateway($methods)
        {
            $methods[] = 'WC_PodWallet';

            return $methods;
        }

        class WC_PodWallet extends WC_Payment_Gateway
        {
            private $api_token; // Business Token (POD panel)
            private $config; // store address services 
            private $env; // environment [Production / Sandbox] 
            private $pod_option; // setting pod option
            private $guild_code; // Guild Code (POD panel)

            public function __construct()
            {
                $this->env = 'Production';
                $this->config  = (require_once(__DIR__ . '/config.php'))[$this->env];

                $this->id = 'WC_PodWallet';
                $this->method_title = __('پرداخت با پیپاد', 'woocommerce');
                $this->method_description = __('تنظیمات درگاه پرداخت پاد برای افزونه فروشگاه ساز ووکامرس', 'woocommerce');
                $this->icon = apply_filters('WC_PodWallet_logo', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/images/podlogo.png?1');
                $this->has_fields = false;
                $this->pod_option = $this->get_pod_otions();
                $this->api_token = $this->pod_option['api_token']; // در صورتی که قبلا پلاگین SSO نصب شده باشد.

                $this->init_form_fields();
                $this->init_settings();

                $this->guild_code = $this->settings['guild_code'];
                $this->title = $this->settings['title'];
                $this->description = $this->settings['description'];

                $this->success_massage = $this->settings['success_massage'];
                $this->failed_massage = $this->settings['failed_massage'];

                if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>='))
                    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                else
                    add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));

                add_action('woocommerce_receipt_' . $this->id . '', array($this, 'send_to_pod_gateway'));
                add_action('woocommerce_api_' . strtolower(get_class($this)) . '', array($this, 'return_from_pod_gateway'));
            }

            public function get_pod_otions($key = null)
            {
                $options = get_option('pod_options');
                if (is_null($key)) {
                    return $options;
                } elseif ($options) {
                    return $options[$key];
                }
                return null;
            }

            public function admin_options()
            {
                parent::admin_options();
            }

            public function init_form_fields()
            {
                $this->form_fields =
                    array(
                        'guild_code' => array(
                            'title' => __('صنف کسب و کار شما (Guild Code):', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('نام صنف خود را از پنل کسب و کار خود در منو کسب و کار قسمت اصناف بیابید.', 'woocommerce'),
                            'default' => ''
                        ),

                        'enabled' => array(
                            'title' => __('فعالسازی/غیرفعالسازی', 'woocommerce'),
                            'type' => 'checkbox',
                            'label' => __('فعالسازی درگاه پاد', 'woocommerce'),
                            'description' => __('برای فعالسازی درگاه پرداخت پاد باید چک باکس را تیک بزنید', 'woocommerce'),
                            'default' => 'yes',
                            'desc_tip' => true,
                        ),
                        'title' => array(
                            'title' => __('عنوان درگاه', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('عنوان درگاه که در طی خرید به مشتری نمایش داده میشود', 'woocommerce'),
                            'default' => __('پرداخت با پیپاد', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'description' => array(
                            'title' => __('توضیحات درگاه', 'woocommerce'),
                            'type' => 'text',
                            'desc_tip' => true,
                            'description' => __('توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد', 'woocommerce'),
                            'default' => __('پرداخت امن به وسیله کلیه کارت های عضو شتاب از طریق درگاه پاد', 'woocommerce')
                        ),
                        'success_massage' => array(
                            'title' => __('پیام پرداخت موفق', 'woocommerce'),
                            'type' => 'textarea',
                            'description' => __('متن پیامی که میخواهید بعد از پرداخت موفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {transaction_id} برای نمایش کد رهگیری (توکن) پاد استفاده نمایید .', 'woocommerce'),
                            'default' => __('با تشکر از شما . سفارش شما با موفقیت پرداخت شد .', 'woocommerce'),
                        ),
                        'failed_massage' => array(
                            'title' => __('پیام پرداخت ناموفق', 'woocommerce'),
                            'type' => 'textarea',
                            // 'description' => __('متن پیامی که میخواهید بعد از پرداخت ناموفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {fault} برای نمایش دلیل خطای رخ داده استفاده نمایید . این دلیل خطا از سایت پاد ارسال میگردد .', 'woocommerce'),
                            'default' => __('پرداخت شما ناموفق بوده است . لطفا مجددا تلاش نمایید یا در صورت بروز اشکال با مدیر سایت تماس بگیرید .', 'woocommerce'),
                        ),

                    );
            }

            public function process_payment($order_id)
            {
                $order = new WC_Order($order_id);
                return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
            }

            /**
             * تبدیل تمام واحدهای پولی ایران به ریال
             * 
             * @param string $amount  مبلغ
             * @param string $currency واحد پولی
             */
            private function convert_to_rail($amount, $currency)
            {
                if (
                    strtolower($currency) == strtolower('IRT') || strtolower($currency) == strtolower('TOMAN')
                    || strtolower($currency) == strtolower('Iran TOMAN') || strtolower($currency) == strtolower('Iranian TOMAN')
                    || strtolower($currency) == strtolower('Iran-TOMAN') || strtolower($currency) == strtolower('Iranian-TOMAN')
                    || strtolower($currency) == strtolower('Iran_TOMAN') || strtolower($currency) == strtolower('Iranian_TOMAN')
                    || strtolower($currency) == strtolower('تومان') || strtolower($currency) == strtolower('تومان ایران')
                )
                    $amount = $amount * 10;
                else if (strtolower($currency) == strtolower('IRHT')) // هزار تومان
                    $amount = $amount * 10000;
                else if (strtolower($currency) == strtolower('IRHR')) // هزار ریال
                    $amount = $amount * 1000;
                else if (strtolower($currency) == strtolower('IRR')) // ریال
                    $amount = $amount / 1;
                return $amount;
            }

            /**
             * prepare and redirect customer to payment gateway
             * @param string $order_id 
             */
            public function send_to_pod_gateway($order_id)
            {
                global $woocommerce;

                $woocommerce->session->order_id_pod = $order_id;
                $order = new WC_Order($order_id);
                $currency = $order->get_order_currency();

                $amount = intval($order->order_total);

                $amount = $this->convert_to_rail($amount, $currency);

                $callbackUrl = add_query_arg('wc_order', $order_id, WC()->api_request_url('WC_PodWallet'));

                $products = array();
                $order_items = $order->get_items();

                foreach ((array) $order_items as $product) {
                    $products[] = $product['name'] . ' (' . $product['qty'] . ') ';
                }
                $products = implode(' - ', $products);

                $description = 'خرید به شماره سفارش : ' . $order->get_order_number() . ' | خریدار : ' . $order->billing_first_name . ' ' . $order->billing_last_name . ' | محصولات : ' . $products;

                try {

                    $server_url = $this->config['PLATFORM_ADDRESS'] . '/nzh/ott'; // دریافت توکن یکبار مصرف
                    $requestArray = array(
                        'method'      => 'POST',
                        'timeout'     => 20,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array(
                            '_token_' => $this->api_token,
                            '_token_issuer_' => '1'
                        ),
                        'body'        => array(),
                        'cookies'     => array(),
                        'sslverify'   => false
                    );

                    $response   = wp_remote_post($server_url, $requestArray);

                    $res_info = json_decode($response['body']);
                    if (isset($res_info->error)) {
                        wp_die($res_info->error_description);
                    }

                    $ott = $res_info->ott;

                    $server_url = $this->config['PLATFORM_ADDRESS'] . '/nzh/biz/issueInvoice/'; // ‌صدور فاکتور توسط کسب و کار

                    $body = array(
                        'productId[]'     => 0,
                        'price[]'     => $amount,
                        'quantity[]' => 1,
                        'productDescription[]' => $description,
                        'guildCode' => $this->guild_code,
                        'verificationNeeded' => 'true',
                        'preferredTaxRate' => 0
                    );
                    if (get_user_meta(get_current_user_id(), 'pod_user_id', true)) {
                        $body['userId'] = get_user_meta(get_current_user_id(), 'pod_user_id', true);
                    }

                    $requestArray = array(
                        'method'      => 'POST',
                        'timeout'     => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array(
                            '_token_' => $this->api_token,
                            '_token_issuer_' => '1',
                            '_ott_' => $ott
                        ),
                        'body'        => $body,
                        'cookies'     => array(),
                        'sslverify'   => false
                    );
                    $response   = wp_remote_post($server_url, $requestArray);

                    $res_info = json_decode($response['body']);

                    // ارسال به درگاه
                    if (isset($res_info->hasError) && $res_info->hasError) {
                        wp_die("Error code: " . $res_info->errorCode . " refrenceNumber: " . $res_info->referenceNumber . " message: " . $res_info->message);
                    }

                    if (get_user_meta(get_current_user_id(), 'pod_user_id')) { //logged in by pod
                        $redirectUrl = $this->config['PRIVATE_CALL_ADDRESS'] . "/v1/pbc/payinvoice/?invoiceId=" . $res_info->result->id . "&redirectUri=$callbackUrl";
                    } else {
                        // $new_url = str_replace("payinvoice", "payInvoiceByUniqueNumber", $options["pay_invoice_url"]); //For backward compatibility url is changed like this
                        $redirectUrl = $this->config['PRIVATE_CALL_ADDRESS'] . "/v1/pbc/payInvoiceByUniqueNumber/?uniqueNumber=" . $res_info->result->uniqueNumber . "&redirectUri=$callbackUrl";
                    }

                    wp_redirect($redirectUrl);
                } catch (Exception $ex) {
                    $notice_wc = 'خطا در عملیات، لطفا مجددا تلاش نمایید.';
                    wc_add_notice($notice_wc, 'error');
                    wp_redirect($this->get_checkout_url());
                    // $Message = $ex->getMessage();
                    // wp_die("Contact Admin, Error");
                }
            }

            /**
             * Return From GateWay
             */
            public function return_from_pod_gateway()
            {
                global $woocommerce;

                $options       = $this->pod_option;

                $order_id = !empty($_GET['wc_order']) ? $_GET['wc_order'] : '';

                // در صورتی که شماره سفارش موجود نباشد.
                if (empty($order_id)) {
                    wp_redirect($this->get_checkout_url());
                }

                $order = new WC_Order($order_id);

                // Completed Buy
                if ($order->status === 'completed' || $order->status === 'processing') {
                    $notice_wc = __('وضعیت تراکنش قبلا مشخص شده است.');
                    wc_add_notice($notice_wc, 'success');

                    $order->payment_complete();
                    $woocommerce->cart->empty_cart();
                    wp_redirect(add_query_arg('', 'success', $this->get_return_url($order)));
                    exit;
                }  // Cancel payment 
                elseif ($order->status === 'cancelled' || strtolower($_GET['paid']) === 'false') {
                    $notice_wc = __('کاربر از ادامه تراکنش انصراف داده');
                    $note_order = __('انصراف از ادامه تراکنش');
                    $order->add_order_note($note_order, 1);
                    wc_add_notice($notice_wc, 'error');
                    wp_redirect($this->get_checkout_url());
                }

                /**
                 * when order status Not completed and Value paid set true (return from gateway)
                 */
                if ($order->status != 'completed'  && strtolower($_GET['paid']) === 'true') {

                    /* verify and close payment (POD Service)*/
                    $res_info = $this->verifyAndCloseInvoice($options['api_token'], $_GET['invoiceId']);

                    if ($res_info->hasError) {
                        $status = 'failed';
                    } else {
                        $status = 'completed';
                        $result = $res_info->result;
                        $transactionId = $result->id; // شماره فاکتور دریافتی از POD یا همان invoiceId

                        $note_order = __('پرداخت موفق-جزییات پرداخت:');
                        $note_order .= '<br>';
                        $note_order .= __('شماره فاکتور پاد: ') . $result->id; // شماره فاکتور دریافتی از POD یا همان invoiceId
                        $note_order .= !empty($result->referenceNumber) ?
                            '<br>' . __('شماره پیگیری :') . $result->referenceNumber : ''; // کد رهگیری

                        if (!empty($result->lastFourDigitOfCardNumber)) {
                            $note_order .= '<br>' . __('روش پرداخت: درگاه بانک');
                            $note_order .= '<br>' . __('چهار رقم آخر کارت: ') .  $result->lastFourDigitOfCardNumber; // چهار رقم آخر شماه کارت پرداخت کننده
                        } else {
                            $note_order .= '<br>' . __('روش پرداخت: کیف پول');
                        }
                    }

                    if ($status === 'completed' && isset($transactionId) && $transactionId != 0) {

                        $order->payment_complete($transactionId);
                        $woocommerce->cart->empty_cart();

                        $notice_wc = $this->success_massage;
                        $notice_wc = str_replace("{transaction_id}", $order_id, $notice_wc);

                        /* add note to order */
                        $order->add_order_note($note_order, 1);

                        $notice_wc = wpautop(wptexturize($notice_wc));

                        if ($notice_wc) {
                            wc_add_notice($notice_wc, 'success');
                        }

                        wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
                        exit;
                    } else {
                        $notice_wc = $this->failed_massage;
                        if ($notice_wc) {
                            $order->add_order_note($notice_wc, 1);

                            $notice_wc = wpautop(wptexturize($this->failed_massage));

                            wc_add_notice($notice_wc, 'error');
                        }

                        wp_redirect($woocommerce->cart->get_checkout_url());
                        exit;
                    }
                }
            }

            /**
             * Verify and close invoice (call Pod API)
             * تایید و بستن فاکتور در یک مرحله
             * @param string $apiToken 
             * @param string $invoiceId
             */
            public function verifyAndCloseInvoice($apiToken, $invoiceId)
            {
                $server_url = $this->config['PLATFORM_ADDRESS'] . '/nzh/biz/verifyAndCloseInvoice/?id=' . $invoiceId;

                $requestArray = array(
                    'method'      => 'GET',
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        '_token_' => $apiToken,
                        '_token_issuer_' => '1',
                    ),
                    'cookies'     => array(),
                    'sslverify'   => false
                );
                $response   = wp_remote_post($server_url, $requestArray);
                $res_info = json_decode($response['body']);

                if (isset($res_info->error)) {
                    wp_die($res_info->error_description);
                }
                if ($res_info->hasError) {
                    wp_die("Contact Admin, Error Code: " . $res_info->errorCode);
                }
                return $res_info;
            }

            public function get_checkout_url()
            {
                if (function_exists('wc_get_checkout_url')) {
                    return wc_get_checkout_url();
                } else {
                    global $woocommerce;

                    return $woocommerce->cart->get_checkout_url();
                }
            }
        }
    }
}

add_action('plugins_loaded', 'Load_Pod_Gateway', 0);
