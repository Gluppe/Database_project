<?php
require_once 'dbCredentials.php';

class TransitionHistoryModel{
    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * A function for adding a new transition order
     * @param int $orderNumber The order number of the order changing state
     * @param string $state The new state of the order
     */
    public function addTransitionHistory(int $orderNumber, String $state) {
        try {
            $this->db->beginTransaction();
            $query1 = 'INSERT INTO transition_history(state_change) VALUES (:state_change)
                    WHERE order_number = :order_number';

            $previousState = $this->getCurrentOrderState($orderNumber);

            $stmt = $this->db->prepare($query1);
            $stmt->bindValue("state_change", $previousState . " -> " . $state);
            $stmt->bindValue("order_number", $orderNumber);
            $stmt->execute();
        } catch (Throwable $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getCurrentOrderState(int $orderNumber): array {
        $res = array();

        $query = 'SELECT `state` FROM `order` WHERE order_number = :order_number';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue("order_number", $orderNumber);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

}