<?php

define('AREA', 'C');
define('SKIP_SESSION_VALIDATION', true);
require './../../init.php';

$rawPOSTBody = file_get_contents("php://input");
$parsed_data = json_decode($rawPOSTBody);

$request_token = (int)$parsed_data->request_token;
$version = $parsed_data->version;
$updates = $parsed_data->updates;

$order_id = db_get_field('SELECT order_id FROM ?:orders WHERE fax = ?s', $parsed_data->merchant_transaction_id);
$order_info = fn_get_order_info($order_id);
$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
$processor_data = fn_get_payment_method_data($payment_id);


switch ($updates->status) {
    case 'approved':
        $pp_response['order_status'] = 'P';
  			$pp_response["reason_text"] = 'GetFinancing payment received';
  			$pp_response["transaction_id"] = $order_id;
  		  break;
    case 'preapproved':
        $pp_response['order_status'] = 'O';
  			$pp_response["reason_text"] = 'GetFinancing pre-approved call, waiting for payment';
  			$pp_response["transaction_id"] = $order_id;
  		  break;
    case 'void':
        $pp_response['order_status'] = 'F';
        $pp_response["reason_text"] = 'Callback received. Payment Failed. Order ID : ' . $order_id;
        $pp_response["transaction_id"] = $order_id;
        break;
    default:
        $pp_response['order_status'] = 'O';
        $pp_response["reason_text"] = 'Callback received. Waiting for payment for Order ID : ' . $order_id;
        $pp_response["transaction_id"] = $order_id;
        break;
}

fn_finish_payment($order_id, $pp_response);
echo "OK";
?>
