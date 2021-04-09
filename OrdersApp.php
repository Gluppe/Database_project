<?php
require_once 'models/OrdersModel.php';

$model = new OrdersModel();
$array = $model->getOrders();
print_r($array);
//$nice = array("hey", "pog", "Pogchamp");
//$int = $model->addOrder($nice);&
//print($int);
