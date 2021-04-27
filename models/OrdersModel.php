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
        $query = 'SELECT * FROM `order`';
        $stmt = $this->db->query($query);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $currentOrderNumber = $row["order_number"];

            $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number like :current_ON");
            $stmt2->bindValue(":current_ON", $currentOrderNumber);
            $stmt2->execute();
            $orderSkisRow = array();
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $orderSkisRow[$row2["ski_type_id"]] = $row2["quantity"];
            }
            //array_push($row, $orderSkisRow);
            $row["skiType_quantity"] = $orderSkisRow;
            $res[] = $row;
        }
        return $res;
    }

    /**
     * Method will return an order that matches the order_number parameter.
     * @param array $query      Array query:
     *                          Index 0: Customer number.
     *                          Index 1: Order Number.
     * @return array            Array with all orders and all values of the order
     */
    public function getOrder(array $query): array {
        $result = array();
        $statement = "SELECT * FROM `order` INNER JOIN order_skis o ON `order`.order_number = o.order_number WHERE o.order_number LIKE :order_number";
        if ($query[0] != null){
            $statement = $statement . " AND customer_number LIKE :customerNumber";
        }
        $stmt = $this->db->prepare($statement);
        $stmt->bindValue(':order_number', $query[1]);
        if ($query[0] != null){
            $stmt->bindValue(":customerNumber", $query[0]);
        }
            $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Method will update the order that matches the orderNumber parameter with the new values provided by the other parameters.
     * @param array $payload    Array payload:
     *                          Index 0: Order number.
     *                          Index 1: Total price.
     *                          Index 2: State
     *                          Index 3: Reference to larger order.
     *                          Index 4: Shipment number.
     * @return bool
     */
    public function updateOrder(array $payload): bool {
        $success = false;
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('UPDATE `order` SET total_price = :total_price, `state` = :state, reference_to_larger_order = :reference_to_larger_order, shipment_number = :shipment_number WHERE order_number like :order_number');
            $stmt->bindValue(":order_number", $payload[0]);
            $stmt->bindValue(":total_price", $payload[1]);
            $stmt->bindValue(":state", $payload[2]);
            $stmt->bindValue(":reference_to_larger_order", $payload[3]);
            $stmt->bindValue(":shipment_number", $payload[4]);
            //$stmt->bindValue(":customer_id", $payload[5]);
            $stmt->execute();
            $this->db->commit();
            $success = true;
        }catch (Throwable $e){
            $this->db->rollBack();
            throw $e;
        }
        return $success;
    }

    /**
     * @param array $query      Query array:
     *                          Index 0: Order number.
     *                          Index 1: Date.
     *                          Index 2: State.
     * @return array            Array of orders that fit the filter.
     * @throws Throwable        Error message if query fails.
     */
    public function getOrdersFiltered(array $query) : array{
        $result[] = array();

        $statement = 'SELECT * FROM `order` INNER JOIN order_skis o ON `order`.order_number = o.order_number WHERE o.order_number LIKE :order_number ';

        //Searches DB with filter for both date and state
        if ($query[1] != null && $query[2] != null){ $query = $query . 'AND date AFTER :date AND state LIKE :state';}
        //Only date filter is applied
        elseif ($query[1]!=null){$statement = $statement . 'AND date AFTER :date';}
        //only state is applied
        else {$statement = $statement . 'AND state LIKE :state';}

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($statement);
            $stmt->bindValue(":order_number", "%" . $query[0] . "%");
            if ($query[1] != null){
                $stmt->bindValue(":date", $query[1]);
            }
            if ($query[2] != null){
                $stmt->bindValue(":state", $query[2]);
            }
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
            $this->db->commit();
        } catch (Throwable $e){
            $this->db->rollBack();
            throw $e;
        }
        return $result;
    }

    public function deleteOrder(String $orderNumber): bool {
        $success = false;
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('DELETE FROM `order` WHERE `order`.order_number = :order_number');
            $stmt->bindValue(":order_number", $orderNumber);
            $stmt->execute();
            $this->db->commit();
            $success = true;
        }
        catch (Throwable $e){
            $this->db->rollBack();
            throw $e;
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
        try {
            $this->db->beginTransaction();
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
            $this->db->commit();
        } catch (Throwable $e){
            $this->db->rollBack();
            throw $e;
        }
    }
}