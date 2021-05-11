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


    public function getOrdersFiltered(array $query) : array{
        $result[] = array();

        $statement = 'SELECT * FROM `order` 
    INNER JOIN order_skis o ON `order`.order_number = o.order_number 
WHERE o.order_number LIKE :order_number ';

        //Searches DB with filter for both date and state
        if ($query[1] != null && $query[2] != null){ $query = $query . '';}
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
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $result;
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
              AND (`order`.date > :dato ) 
              AND (`order`.state LIKE :status)
              ";

        $stmt = $this->db->prepare($statement);
        $stmt->bindValue(':orderNumber',  $uri[2] . "%");
        $stmt->bindValue(":customerNumber", "%" . $query["customer_id"] . "%");
        $stmt->bindValue(":dato", date("Y-m-d", strtotime($query["since"])));
        $stmt->bindValue(":status", "%" . $query["state"] . "%");
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
        return $result;
    }

    /*
     * Får inn orderenummer
     * finn antall ski som bestilles innen skityper.
     *  finn antall ski som er gitt ordrenummer
     *  opprett nytt ordrenummer på dette antallet.
     */

    public function splitOrder(array $query){
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                'select order_skis.*, `order`.customer_id from order_skis
                        LEFT JOIN `order`
                        on order_skis.order_number = `order`.order_number as 
                        where order_number LIKE :orderNumber and customer_id like :customerID');
            $stmt->bindValue(':customerID', $query["customer_id"]);
            $stmt->bindValue(":orderNumber", $query["order_number"]);
            $stmt->execute();
            $stmt2 = $this->db->prepare(
                "select count(order_no) 
                        from ski
                        where (available like 1) 
                          and (ski_type_id like :stid) 
                          and (order_no like :order_no)
                        ");
            foreach ($query["skis"] as $id => $id_value){
                $stmt2->bindValue(":skiTypeId", $id);
                $stmt2->bindValue(":quantity", $id_value);
                $stmt2->bindValue(":order_number", $lastOrder);
                $stmt2->execute();
            }
            $this->db->commit();
        } catch (Exception $e){
            $this->db->rollBack();
            throw $e;
        }
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
            $stmt = $this->db->prepare(
                'UPDATE `order` SET total_price = :total_price, `state` = :state, 
                   reference_to_larger_order = :reference_to_larger_order, shipment_number = :shipment_number, 
                   customer_id = :customer_id WHERE order_number like :order_number');
            $stmt->bindValue(":order_number", $payload[0]);
            $stmt->bindValue(":total_price", $payload[1]);
            $stmt->bindValue(":state", $payload[2]);
            $stmt->bindValue(":reference_to_larger_order", $payload[3]);
            $stmt->bindValue(":shipment_number", $payload[4]);
            $stmt->execute();
            $this->db->commit();
            $success = true;
        }catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
        return $success;
    }


    /**
     * Cancels the order of a customer.
     * @param array $payload
     * @return bool
     * @throws Exception
     */
    public function cancelOrder(array $payload): bool {
        $success = false;
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(
                'UPDATE `order` SET `state` = :state WHERE order_number like :order_number and customer_id like :customer_id');
            $stmt->bindValue(":state", "canceled");
            $stmt->bindValue(":order_number", $payload["order_number"]);
            $stmt->bindValue(":customer_id", $payload["customer_id"]);
            $stmt->execute();
            $this->db->commit();
            $success = true;
        }
        catch (Exception $e){
            $this->db->rollBack();
            throw $e;
        }
        return $success;
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
     * @throws Exception
     */
    public function addOrder(array $orderedSkis): void {
        $total_price = 0;
        try {
            $this->db->beginTransaction();
            foreach ($orderedSkis["skis"] as $id => $id_value){
                $query = $this->db->prepare("select MSRP from ski_type where ID LIKE :id");
                $query->bindValue(":id", $id);
                $query->execute();
                $price = $query->fetch();
                $total_price += $price[0] * $id_value;
            };
            $stmt = $this->db->prepare(
                'INSERT INTO `order` (total_price, state,  customer_id, date)'
                .' VALUES(:total_price, :state, :customer_id, :date)');
            $stmt->bindValue(':total_price', $total_price);
            $stmt->bindValue(':state', "new");
            $stmt->bindValue(':customer_id', $orderedSkis["customer_id"]);
            $stmt->bindValue(":date", date("Y-m-d"));
            $stmt->execute();
            $lastOrder = $this->db->lastInsertId();
            $stmt2 = $this->db->prepare(
                "INSERT INTO order_skis (ski_type_id, quantity, order_number) VALUES (:skiTypeId, :quantity, :order_number)");
            foreach ($orderedSkis["skis"] as $id => $id_value){
                $stmt2->bindValue(":skiTypeId", $id);
                $stmt2->bindValue(":quantity", $id_value);
                $stmt2->bindValue(":order_number", $lastOrder);
                $stmt2->execute();
            }
            $this->db->commit();
        } catch (Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }
}