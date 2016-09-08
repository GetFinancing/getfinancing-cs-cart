<?php
//Auth : Senthil.R (senthil1975@gmail.com)
use Tygh\Registry;

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'notify') {
	$order_id = $_REQUEST['order_id'];

    fn_order_placement_routines('route', $order_id);
}
}
else{

    $order_id = $order_info['order_id'];
    if ( $processor_data['processor_params']['getfinancing_environment']){
      $gateway_url = 'https://api.getfinancing.com/merchant/' . $processor_data['processor_params']['getfinancing_merchant_id'] . '/requests';
    }else{
      $gateway_url = 'https://api-test.getfinancing.com/merchant/' . $processor_data['processor_params']['getfinancing_merchant_id'] . '/requests';
    }

		$state = $order_info['b_state_descr'];
		if (empty($state)) $state_val = '';
		else $state_val = $state;

	    $nok_url = fn_url('checkout.checkout');
      $ok_url = fn_url("payment_notification.notify&payment=getfinancing&order_id=$order_id&transmode=success", AREA, 'current');
	    $callback_url = Registry::get('config.http_location') . '/app/payments/getfinancing_callback.php';

    $products=array();
    if (!empty($order_info['products'])) {
      foreach ($order_info['products'] as $k => $v) {
        $price = fn_format_price($v['price'] - (fn_external_discounts($v) / $v['amount']));
        if ($price <= 0) continue;
        $product_name = ($v['extra']['product']). " (".$v['amount'].")";
        $amount = $v['amount'];
        $products[]=$product_name;
      }
    }
    $merchant_loan_id = md5(time() . $processor_data['processor_params']['getfinancing_merchant_id'] .
                        $order_info['b_firstname'] . $order_info['total']);

      $gf_data = array(
          'amount'           => round($order_info['total'], 2),
          'product_info'     => implode(",",$products),
          'first_name'       => $order_info['b_firstname'],
          'last_name'        => $order_info['b_lastname'],
          'shipping_address' => array(
              'street1'  => $order_info['s_address']. " ".$order_info['s_address_2'],
              'city'    => $order_info['s_city'],
              'state'   => $order_info['s_state'],
              'zipcode' => $order_info['s_zipcode']
          ),
          'billing_address' => array(
              'street1'  => $order_info['b_address']. " ".$order_info['b_address_2'],
              'city'    => $order_info['b_city'],
              'state'   => $order_info['b_state'],
              'zipcode' => $order_info['b_zipcode']
          ),
          'version'          => '1.9',
          'email'            => $order_info['email'],
          'phone'            => $order_info['b_phone'],
          'postback_url' => $callback_url,
          'success_url' => $ok_url,
          'failure_url' => $nok_url,
          'merchant_loan_id' => $merchant_loan_id,
          'software_name' => 'cs-cart',
          'software_version' => 'cscart 4'
      );

      $body_json_data = json_encode($gf_data);
      $header_auth = base64_encode($processor_data['processor_params']['getfinancing_username'] . ":" . $processor_data['processor_params']['getfinancing_password']);


      $post_args = array(
          'body' => $body_json_data,
          'timeout' => 60,     // 60 seconds
          'blocking' => true,  // Forces PHP wait until get a response
          'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $header_auth,
            'Accept' => 'application/json'
           )
      );

      $gfResponse = _remote_post( $gateway_url, $post_args );

      $gfResponse = json_decode($gfResponse);

      //security implementation.
      // we use the usless fax field to store the merchant_loan_id
      // this will be used to recover the order_id later on the postback

      $data = array (
          'fax' => $merchant_loan_id
          );
      db_query('UPDATE ?:orders SET ?u WHERE order_id = ?i', $data, $order_id);

		$locale = CART_LANGUAGE;
		$currency = $order_info['secondary_currency'];
		$amount = $order_info['total']*100;
		$description = 'Order #'.$order_id;
    $full_name=$order_info[b_firstname].' '.$order_info[b_lastname];

    echo <<<EOT
    <html>
    <head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.getfinancing.com/libs/1.0/getfinancing.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        var onComplete = function() {
            window.location.href="{$ok_url}";
        };

        var onAbort = function() {
            window.location.href="{$nok_url}";
        };
        jQuery( document ).ready(function() {
          new GetFinancing("{$gfResponse->href}", onComplete, onAbort);
        });
    </script>
EOT;


    $msg = fn_get_lang_var('text_cc_processor_connection');
    $msg = str_replace('[processor]', 'getfinancing', $msg);
    echo <<<EOT
    	<div align=center>{$msg}</div>
     </body>
    </html>
EOT;
exit;
}

/**
 * Set up RemotePost / Curl.
 */
function _remote_post($url,$args=array()) {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
    curl_setopt($curl, CURLOPT_USERAGENT, 'CS-Cart - GetFinancing Payment Module ');
    if (defined('CURLOPT_POSTFIELDSIZE')) {
        curl_setopt($curl, CURLOPT_POSTFIELDSIZE, 0);
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, $args['timeout']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    $array_headers = array();
    foreach ($args['headers'] as $k => $v) {
        $array_headers[] = $k . ": " . $v;
    }
    if (sizeof($array_headers)>0) {
      curl_setopt($curl, CURLOPT_HTTPHEADER, $array_headers);
    }

    if (strtoupper(substr(@php_uname('s'), 0, 3)) === 'WIN') {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $resp = curl_exec($curl);
    curl_close($curl);

    if (!$resp) {
      return false;
    } else {
      return $resp;
    }
}

?>
