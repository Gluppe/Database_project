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
     * @param string $stateChange The new state of the order
     */
    public function addTransitionHistory(int $orderNumber, string $stateChange) {
        $query = 'INSERT INTO transition_history(state_change) VALUES (:state_change) UPDATE table SET date = GETDATE()
                    WHERE order_number = :order_number';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue("state_change", $stateChange);
        $stmt->bindValue("order_number", $orderNumber);
        $stmt->execute();

    }

}