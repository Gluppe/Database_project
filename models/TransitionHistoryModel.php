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
            $query1 = 'INSERT INTO transition_history(state_change) VALUES (:state_change)';

            $previousState = $this->getCurrentOrderState($orderNumber);

            $stmt = $this->db->prepare($query1);
            $stmt->bindValue("state_change", $previousState . " -> " . $state);
            $stmt->bindValue("order_number", $orderNumber);
            $stmt->execute();
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e);
        }
    }

    /**
     * Finds the current state of an order
     * @param int $orderNumber Number of the order
     * @return array An array wher Index 0 = the state of the order
     */
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

    /**
     * Finds the last inserted transition history
     * It is an auto increment so the last descending transition_history_id
     * will always be the latest added
     * @return array the ski as an array
     */
    public function getLastInsertedTransitionHistory(): array {
        $res = array();

        $query = 'SELECT * FROM transition_history ORDER BY transition_history_id DESC LIMIT 1';

        $stmt = $this->db->query($query);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;

    }

}