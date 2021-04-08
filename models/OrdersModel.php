<?php
require_once 'dbCredentials.php';

class OrdersModel {
    protected PDO $db;

    /**
     * OrdersModel constructor.
     */
    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * Method will return all orders in the database as an array.
     * @return array
     */
    public function getOrders(): array {
        $res = array();
        $query = '  SELECT * FROM `order`';
        $stmt = $this->db->query($query);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $currentOrderNumber = $row["order_number"];

            $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number like :current_ON");
            $stmt2->bindValue(":current_ON", $currentOrderNumber);
            $stmt2->execute();
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $orderSkisRow[ $row2["ski_type_id"]] = $row2["quantity"];
            }
            //array_push($row, $orderSkisRow);
            $row["skiType_quantity"] = $orderSkisRow;
            $res[] = $row;
        }
        return $res;
    }

    /**
     * Method will return an order that matches the order_number parameter.
     * @param int $order_number Query parameter
     * @return array            Array with all orders and all values of the order
     */
    public function getOrder(int $order_number): array {
        $result = array();
        $stmt = $this->db->prepare('SELECT * FROM `order` INNER JOIN order_skis o ON `order`.order_number = o.order_number WHERE o.order_number LIKE :order_number');
        $stmt->bindValue(':order_number', $order_number);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;

    }

    /**
     * Method will update the order that matches the orderNumber parameter with the new values provided by the other parameters.
     * @param int $orderNumber              Query parameter
     * @param int $totalPrice               Updates the total price of the order.
     * @param string $state                 Updates the state of the order.
     * @param int $referenceToLargerOrder   Updates the reference to larger order of the order.
     * @param int $shipmentNumber           Updates the shipment number of the order.
     * @param int $customerID               Redacted due to security reasons.
     * @return bool
     */
    public function updateOrder(int $orderNumber, int $totalPrice, string $state, int $referenceToLargerOrder, int $shipmentNumber ): bool {
        $success = false;
        try {$stmt = $this->db->prepare('UPDATE `order` SET total_price = :total_price, `state` = `:state`, reference_to_larger_order = :reference_to_larger_order, shipment_number = :shipment_number, customer_id = :customer_id WHERE order_number like :order_number');
            $stmt->bindValue(":order_number", $orderNumber);
            $stmt->bindValue(":total_price", $totalPrice);
            $stmt->bindValue(":state", $state);
            $stmt->bindValue(":reference_to_larger_order", $referenceToLargerOrder);
            $stmt->bindValue(":shipment_number", $shipmentNumber);
            //$stmt->bindValue(":customer_id", $customerID);
            $stmt->execute();
            $success = true;
        }catch (\mysql_xdevapi\Exception){
            echo "Something went wrong with update order";
        }
        return $success;
    }


    /**
     * Method adds an order to the database.
     * TODO: Finish the method - parameters not defined yet
     * @param array $orderedSkis
     * @param int $total_price
     * @param int $reference_to_larger_order
     * @param int $customer_id
     */
    public function addOrder(array $orderedSkis, int $total_price, int $reference_to_larger_order, int $customer_id): void {
        $totalPrice = 0;
        foreach ($orderedSkis as $value){
            $totalPrice += $value[2];
        }
        $stmt = $this->db->prepare(
            'INSERT INTO `order` (total_price, state, reference_to_larger_order, customer_id)'
                .' VALUES(:total_price, :state, :reference_to_larger_order, :customer_id)');
        $stmt->bindValue(':total_price', $total_price);
        $stmt->bindValue(':state', "new");
        $stmt->bindValue(':reference_to_larger_order', $reference_to_larger_order);
        $stmt->bindValue(':customer_id', $customer_id);
        $stmt->execute();
        $lastOrder = $this->db->lastInsertId();

        $stmt2 = $this->db->prepare(
            "INSERT INTO order_skis (ski_type_id, quantity, order_number) VALUES (:skiTypeId, :quantity, :order_number)");
        foreach ($orderedSkis as $value){
            $stmt2->bindValue(":skitypeId", $value[0]);
            $stmt2->bindValue(":quantity", $value[1]);
            $stmt2->bindValue(":order_number", $lastOrder);
            $stmt2->execute();
        }
    }
}