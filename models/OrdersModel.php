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

                $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number like :current_ON");
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
            WHERE customer_id LIKE :customer_id";
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

                $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number like :current_ON");
                $stmt2->bindValue(":current_ON", $currentOrderNumber);
                $stmt2->execute();
                $orderSkisRow = array();
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $orderSkisRow[$row2["ski_type_id"]] = $row2["quantity"];
                    $stmt2 = $this->db->prepare("select ski_type_id, quantity from order_skis where order_number like :current_ON");
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

    public function splitOrder(array $query)
    {
        print_r($query);
        $skis = array();
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                'select order_skis.*, `order`.customer_id from order_skis
                        LEFT JOIN `order`
                        on order_skis.order_number = `order`.order_number 
                        where order_skis.order_number LIKE :orderNumber and `order`.customer_id like :customerID');
            $stmt->bindValue(':customerID', $query["customer_id"]);
            $stmt->bindValue(":orderNumber", $query["order_number"]);
            $stmt->execute();
            $stmt2 = $this->db->prepare(
                "select count(order_no) 
                        from ski
                        where (ski_type_id like :stid) 
                          and (order_no like :order_no)
                        ");
            $newSkis = array();
            $oldSkis = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                print_r($row);
                $stmt2->bindValue(":stid", $row["ski_type_id"]);
                $stmt2->bindValue(":order_no", $row["order_number"]);
                $stmt2->execute();

                $readySkis = $stmt2->fetch(PDO::FETCH_ASSOC);

                $quantity = $row["quantity"] - $readySkis["count(order_no)"];
                if ($quantity > 0) {
                    $newSkis += [$row["ski_type_id"] => $quantity];
                    $oldSkis += [$row["ski_type_id"] => $row["quantity"]];
                }
            }
            $skis = ["newSkis" => $newSkis, "oldSkis" => $oldSkis];
            print_r($skis);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e);
        }
        if ($this->updateOrDeleteOrder_skis($query, $skis)) {
            //$newOrder = ["customer_id" => $query["customer_id"], "skis" => $skis["newSkis"]];
            $this->addOrder(["skis"=>$skis["newSkis"]], $query);
        }

    }

    private function updateOrDeleteOrder_skis(array $query, array $payload): bool {
        try {
            $this->db->beginTransaction();
            foreach ($payload["newSkis"] as $id => $splitQuantity) {
                print_r("From old order: " . $payload["oldSkis"][$id] . "\n");
                print_r("To new order: " . $splitQuantity . "\n");
                print_r("ski type id: " . $id ."\n");
                $newQuantity = $payload["oldSkis"][$id] - $splitQuantity;
                if ($payload["oldSkis"][$id] == $splitQuantity){
                    print_r("Run delete\n");
                    $this->deleteFromOrder_skis($id, $query["order_number"]);
                }else{
                    print_r("run update");
                    $this->updateOrder_skis($newQuantity, $id, $query["order_number"]);
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
WHERE order_number like :order_number 
AND ski_type_id like :skitype');
        print_r("SHould update skitype: " . $skiTypeID . "\tAnd order number: " . $orderNumber . "\n");
        $stmt->bindValue(":quantity", $quantity);
        $stmt->bindValue(":order_number", $orderNumber);
        $stmt->bindValue(":skitype", $skiTypeID);
        return $stmt->execute();
    }

    private function deleteFromOrder_skis(String $skiTypeID, String $orderNumber): bool{
        $stmt = $this->db->prepare('
DELETE FROM order_skis 
WHERE order_number like :order_number 
AND ski_type_id like :skitype');
        print_r("SHould delete skitype: " . $skiTypeID . "\tAnd order number: " . $orderNumber . "\n");
        $stmt->bindValue(":order_number", $orderNumber);
        $stmt->bindValue(":skitype", $skiTypeID);
        return $stmt->execute();
    }


    /**
     * Method will update the order that matches the orderNumber parameter with the new values provided by the other parameters.
     * @param array $payload    Array payload:
     *                          Index 0: State
     *                          Index 1: Reference to larger order.
     *                          Index 2: Shipment number.
     * @param array $query      Indexes:
     *                          "customer_id"   -> customer number.
     *                          "order_number"  -> order number.
     * @return bool
     */
    public function updateOrder(array $uri, array $payload): bool {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('
UPDATE `order` 
SET `state` = :state, shipment_number = :shipment_number 
WHERE order_number like :order_number');
            $stmt->bindValue(":order_number", $uri[2]);
            $stmt->bindValue(":state", $payload['state']);
            if(!empty($payload['shipment_number']) && $uri[0] == RESTConstants::ENDPOINT_SHIPPER) {
                $stmt->bindValue(":shipment_number", $payload['shipment_number']);
            } else {
                $stmt->bindValue(":shipment_number", null);
            }
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
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                'UPDATE `order` 
SET `state` = :state 
WHERE order_number like :order_number 
AND customer_id like :customer_id');
            $stmt->bindValue(":state", "canceled");
            $stmt->bindValue(":order_number", $uri[2]);
            $stmt->bindValue(":customer_id", $queries["customer_id"]);
            $stmt->execute();

            $stmt2 = $this->db->prepare('
SELECT `production_number` 
FROM ski 
WHERE order_no LIKE :order_number');
            $stmt2->bindValue(":order_number", $uri[2]);
            $stmt2->execute();

            while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $stmt3 = $this->db->prepare(
                    '
UPDATE `ski` 
SET `available` = 1, `order_no` = NULL 
WHERE production_number LIKE :production_number');
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
            $this->db->beginTransaction();
            print_r($orderedSkis);
            foreach ($orderedSkis["skis"] as $id => $id_value) {
                print_r("id = " . $id . "\n");
                print_r("quantity = " .  $id_value . "\n");
                $stmt = $this->db->prepare("
                                    select MSRP 
                                    from ski_type
                                    where ID = :id");
                $stmt->bindValue(":id", $id);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print_r("price = " . $row["MSRP"] . "\n");
                    $total_price += $row["MSRP"] * $id_value;
                    print_r( "total price = " . $total_price . "\n");
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
    public function addOrder(array $orderedSkis, array $queries): void {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO `order` (total_price, state,  customer_id, date)'
                .' VALUES(:total_price, :state, :customer_id, :date)');
            $stmt->bindValue(':total_price', $this->getTotalPrice($orderedSkis));
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
            print("Added order with id: " . $lastOrder);
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
    }
}