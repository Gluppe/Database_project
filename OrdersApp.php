<?php
require_once 'models/OrdersModel.php';

$model = new OrdersModel();

$customer_id = 10;
$skis = array("1" => "100","2" => "30");
$payload = array("customer_id"=> $customer_id, "skis" => $skis);
$int = NULL;
try {

    $model->addOrder($payload);
} catch (Throwable $e) {
    print_r($e);
}

//print_r($array);
//$nice = array("hey", "pog", "Pogchamp");
//$int = $model->addOrder($nice);&
//print($int);
