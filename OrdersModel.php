<?php
require_once  'dbCredentials.php';

class OrdersModel {
    protected $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    public function getOrders(): array {
        $res = array();

        $query = 'SELECT `order`.*, o.ski_type_id, o.quantity FROM `order` INNER JOIN order_skis o';

        $stmt = $this->db->query($query);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

}