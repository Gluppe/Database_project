<?php
require_once 'OrdersModel.php';

$model = new OrdersModel();
$array = $model->getOrders();
print_r($array);