<?php
require_once 'models/OrdersModel.php';

$model = new OrdersModel();

$customer_id = 10;
$order_number = 1;
$payload = array("customer_id"=> $customer_id);

try {
    print_r($model->getOrderByCustomerId($payload));
} catch (Throwable $e) {
    print_r($e);
}

//print_r($array);
//$nice = array("hey", "pog", "Pogchamp");
//$int = $model->addOrder($nice);&
//print($int);
