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
     * Method will return an order that matches the order_number parameter.
     * @param array $query      Array query:
     *                          Index 0: Customer number.
     *                          Index 1: Order Number.
     * @return array            Array with all orders and all values of the order
     */
    public function getOrder(array $uri, array $query): array {
        $result = array();
        $statement = "
            SELECT * FROM `order` 
            WHERE (order_number LIKE :orderNumber) 
              AND (customer_id LIKE :customerNumber)
              AND (`order`.date >= :date ) 
              AND (`order`.state LIKE :status)
              ";
        try {
            $stmt = $this->db->prepare($statement);
            $stmt = $this->db->prepare($statement);
            if(!empty($uri[2])) {
                $stmt->bindValue(':orderNumber',  $uri[2]);
            } else {
                $stmt->bindValue(':orderNumber',  "%");
            }

            if(!empty($query['customer_id'])) {
                $stmt->bindValue(":customerNumber", $query["customer_id"]);
            } else {
                $stmt->bindValue(":customerNumber", "%");
            }

            if(!empty($query['since'])) {
                $stmt->bindValue(":date", date("Y-m-d", strtotime($query["since"])));
            } else {
                $stmt->bindValue(":date", "");
            }

            if(!empty($query['state'])) {
                $stmt->bindValue(":status", $query["state"]);
            } else {
                $stmt->bindValue(":status", "%");
            }
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $currentOrderNumber = $row["order_number"];

                $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number = :current_ON");
                $stmt2->bindValue(":current_ON", $currentOrderNumber);
                $stmt2->execute();
                $orderSkisRow = array();
                while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $orderSkisRow[$row2["ski_type_id"]] = $row2["quantity"];
                }
                $row["skiType_quantity"] = $orderSkisRow;
                $result[] = $row;
            }
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $result;
    }

    /** Gets all orders by customer id with optional since filter
     * @param array $queries includes the customer_id at key 'customer_id' and date at the 'since' key.
     * @return array
     */
    public function getOrdersByCustomerId(array $queries): array {
        $result = array();
        $statement = "
            SELECT * FROM `order` 
            WHERE customer_id = :customer_id";
        if(!empty($queries['since'])) {
            $statement = $statement . " AND date >= :date";
        }
        try {
            $stmt = $this->db->prepare($statement);
            $stmt->bindValue(':customer_id',  $queries['customer_id']);
            if(!empty($queries['since'])) {
                $date = $queries['since'];
                $date = date($date, strtotime($date));
                $stmt->bindValue(':date',  $date);
            }
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $currentOrderNumber = $row["order_number"];

                $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number = :current_ON");
                $stmt2->bindValue(":current_ON", $currentOrderNumber);
                $stmt2->execute();
                $orderSkisRow = array();
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $orderSkisRow[$row2["ski_type_id"]] = $row2["quantity"];
                    $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number = :current_ON");
                    $stmt2->bindValue(":current_ON", $currentOrderNumber);
                    $stmt2->execute();
                    $orderSkisRow = array();
                    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $orderSkisRow[$row2["ski_type_id"]] = $row2["quantity"];
                    }
                    $row["skiType_quantity"] = $orderSkisRow;
                    $result[] = $row;
                }
            }
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $result;
    }

    /*
     * Får inn orderenummer
     * finn antall ski som bestilles innen skityper.
     *  finn antall ski som er gitt ordrenummer
     *  opprett nytt ordrenummer på dette antallet.
     */

    public function splitOrder(array $uri, array $query): bool
    {
        $countedSkis = 0;
        $skis = array();
        $success = false;
        if(!$this->orderNumberCustomerIdMatch($uri[2], $query['customer_id'])) {
            return false;
        }
        try {
            $oldSkis = $this->getOrderedSkis($uri);
            $this->db->beginTransaction();
            $newSkis = array();
            $stmt = $this->db->prepare(
                "select count(order_no) 
                        from ski
                        where (ski_type_id = :stid) 
                          and (order_no = :order_no)
                        ");
            foreach ($oldSkis as $id => $oldQuantity){
                $stmt->bindValue(":stid", $id);
                $stmt->bindValue(":order_no", $uri[2]);
                $stmt->execute();
                $readySkis = $stmt->fetch(PDO::FETCH_ASSOC);
                $countedSkis += $readySkis["count(order_no)"];
                $newQuantity = $oldQuantity - $readySkis["count(order_no)"];
                if ($newQuantity > 0) {
                    $newSkis += [$id => $newQuantity];
                }
            }
            $skis = ["newSkis" => $newSkis, "oldSkis" => $oldSkis];
            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e);
        }
        if ($countedSkis != 0) {
            if ($this->updateOrDeleteOrder_skis($uri, $skis)) {
                $uri = array( "", "" , $uri[2]);
                $payload = array("state"=>"ready-for-shipping", "shipment_number"=>"");
                if($this->updateOrder($uri, $payload)){
                    $this->addOrder(["skis" => $skis["newSkis"]], $query);
                    $success = true;
                }
            }
        }else {
            print_r("No split executed cause no skis were counted.");
        }
        return $success;
    }

    private function getOrderedSkis(array $uri): array {
        $stmt = $this->db->prepare(
            'select * from order_skis
                    where order_skis.order_number = :orderNumber');
        $stmt->bindValue(":orderNumber", $uri[2]);
        $stmt->execute();
        $skis = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $skis += [$row["ski_type_id"] => $row["quantity"]];
        }
        return $skis;
    }

    private function updateOrDeleteOrder_skis(array $uri, array $payload): bool {
        try {
            $this->db->beginTransaction();
            foreach ($payload["newSkis"] as $id => $splitQuantity) {
                $newQuantity = $payload["oldSkis"][$id] - $splitQuantity;
                if ($payload["oldSkis"][$id] == $splitQuantity){
                    $this->deleteFromOrder_skis($id, $uri[2]);
                }else{
                    $this->updateOrder_skis($newQuantity, $id, $uri[2]);
                }
            }
            return $this->db->commit();
        }catch (Exception $e){
            error_log($e);
            return $this->db->rollBack();
        }
    }

    private function updateOrder_skis(String $quantity, String $skiTypeID, String $orderNumber): bool {
        $stmt = $this->db->prepare('
UPDATE order_skis 
SET quantity = :quantity
WHERE order_number = :order_number 
AND ski_type_id = :skitype');
        $stmt->bindValue(":quantity", $quantity);
        $stmt->bindValue(":order_number", $orderNumber);
        $stmt->bindValue(":skitype", $skiTypeID);
        return $stmt->execute();
    }

    private function deleteFromOrder_skis(String $skiTypeID, String $orderNumber): bool{
        $stmt = $this->db->prepare('
DELETE FROM order_skis 
WHERE order_number = :order_number 
AND ski_type_id = :skitype');
        $stmt->bindValue(":order_number", $orderNumber);
        $stmt->bindValue(":skitype", $skiTypeID);
        return $stmt->execute();
    }


    /**
     * Method will update the order that matches the orderNumber parameter with the new values provided by the other parameters.
     * @param array $uri
     * @param array $payload     Indexes:
     *                          "state"   -> new status of the order.
     *                          "shipment_number"  -> new shipment number.
     * @return bool
     */
    public function updateOrder(array $uri, array $payload): bool {
        try {
            //print_r($uri);
            //print_r($payload);
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('
UPDATE `order` 
SET `state` = :state, shipment_number = :shipment_number, total_price = :total_price
WHERE order_number = :order_number');

            $stmt->bindValue(":order_number", $uri[2]);
            $stmt->bindValue(":state", $payload['state']);
            if(!empty($payload['shipment_number']) && $uri[0] == RESTConstants::ENDPOINT_SHIPPER) {
                $stmt->bindValue(":shipment_number", $payload['shipment_number']);
            } else {
                $stmt->bindValue(":shipment_number", null);
            }
            $getSkis = array( "skis" => $this->getOrderedSkis($uri));
            //print_r("Getskis: \n");
            //print_r($getSkis);
            $totalPrice = $this->getTotalPrice($getSkis);
            //print_r("total price: \n");
            //print_r($totalPrice);
            $stmt->bindValue(":total_price", $totalPrice);
            $stmt->execute();
            return $this->db->commit();
        }catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return false;
    }


    /**
     * Cancels the order of a customer.
     * @param array $uri
     * @param array $queries
     * @return bool
     */
    public function cancelOrder(array $uri, array $queries): bool {
        $success = false;
        if(!$this->orderNumberCustomerIdMatch($uri[2], $queries['customer_id'])) {
            return false;
        }
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                'UPDATE `order` 
SET `state` = :state 
WHERE order_number = :order_number 
AND customer_id = :customer_id');
            $stmt->bindValue(":state", "canceled");
            $stmt->bindValue(":order_number", $uri[2]);
            $stmt->bindValue(":customer_id", $queries["customer_id"]);
            $stmt->execute();

            $stmt2 = $this->db->prepare('
SELECT `production_number` 
FROM ski 
WHERE order_no = :order_number');
            $stmt2->bindValue(":order_number", $uri[2]);
            $stmt2->execute();

            while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $stmt3 = $this->db->prepare(
                    '
UPDATE `ski` 
SET `available` = 1, `order_no` = NULL 
WHERE production_number = :production_number');
                $stmt3->bindValue(":production_number", $row['production_number']);
                $stmt3->execute();
            }

            $this->db->commit();
            $success = true;
        }
        catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $success;
    }


    private function getTotalPrice(array $orderedSkis): int {
        $total_price = 0;
        try {
            foreach ($orderedSkis["skis"] as $id => $id_value) {
                $stmt = $this->db->prepare("
                                    select MSRP 
                                    from ski_type
                                    where ID = :id");
                $stmt->bindValue(":id", $id);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $total_price += $row["MSRP"] * $id_value;
                }
            }
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $total_price;
    }


    /**
     * Method adds an order to the database.
     * @param array $orderedSkis should look like this:
     *      Array
     *       (
     *           [customer_id] => <a_customerID>
     *           [skis] => Array
     *           (
     *               [<ski_type_id>] => <quantity>
     *           )
     *       )
     */
    public function addOrder(array $orderedSkis, array $queries): bool {
        $success = false;
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                'INSERT INTO `order` (total_price, state,  customer_id, date)'
                .' VALUES(:total_price, :state, :customer_id, :date)');
            $stmt->bindValue(':total_price', $this->getTotalPrice(["skis"=> $orderedSkis["skis"]]));
            $stmt->bindValue(':state', "new");
            $stmt->bindValue(':customer_id', $queries["customer_id"]);
            $stmt->bindValue(":date", date("Y-m-d"));
            $stmt->execute();
            $lastOrder = $this->db->lastInsertId();
            $stmt2 = $this->db->prepare(
                "INSERT INTO order_skis (ski_type_id, quantity, order_number) 
                        VALUES (:skiTypeId, :quantity, :order_number)");
            foreach ($orderedSkis["skis"] as $id => $id_value){
                $stmt2->bindValue(":skiTypeId", $id);
                $stmt2->bindValue(":quantity", $id_value);
                $stmt2->bindValue(":order_number", $lastOrder);
                $stmt2->execute();
            }
            $this->db->commit();
            $success = true;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $success;
    }

    public function orderNumberCustomerIdMatch(string $orderNumber, string $customer_id): bool {
        $stmt = $this->db->prepare('SELECT customer_id FROM `order` WHERE order_number LIKE :order_number');
        $stmt->bindValue(':order_number', $orderNumber);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $customer_id_res = $res['customer_id'];
        return $customer_id_res == $customer_id;
    }
}