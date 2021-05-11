<?php
require_once 'models/OrdersModel.php';
require_once 'models/SkisModel.php';

$model = new OrdersModel();
$model = new SkisModel();

$text = $model->getLastInsertedSki();

foreach($text[0] as $try) {
    print $try ;
}

//print_r($array);
//$nice = array("hey", "pog", "Pogchamp");
//$int = $model->addOrder($nice);&
//print($int);
