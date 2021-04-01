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

    public function addOrder(array $skis): int {
        foreach ($skis as $key => $value) {
            echo "{$key} => {$value} ";
        }
        $total_price = 1;
        $state = "new";
        $reference_to_larger_order = null;
        $customer_id = 10;
        $stmt = $this->db->prepare(
            'INSERT INTO `order` (total_price, state, reference_to_larger_order, customer_id)'
            . ' VALUES(:total_price, :state, :reference_to_larger_order, :customer_id)');
        $stmt->bindValue(':total_price', $total_price);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':reference_to_larger_order', $reference_to_larger_order);
        $stmt->bindValue(':customer_id', $customer_id);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
}