<?php
require_once 'models/OrdersModel.php';
require_once 'models/SkisModel.php';

$model = new OrdersModel();
$model = new SkisModel();

$customer_id = "";
$order_number = "";
$state = "";
$date = "";
$payload = array("customer_id"=> $customer_id, "since"=>$date, "state"=>$state);
$uri = array("", "", $order_number);

try {
    print_r($model->getOrder($uri,$payload));
} catch (Throwable $e) {
    print_r($e);
}

//print_r($array);
//$nice = array("hey", "pog", "Pogchamp");
//$int = $model->addOrder($nice);&
//print($int);
