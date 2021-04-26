<?php
require_once 'dbCredentials.php';

class ProductionPlanModel
{
    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * A function for adding a new production plan. the month is represented
     * with an int så it has to be between 1 and 12
     * @param array $payload
     * Index 0 = month
     */
    public function addProductionPLan(array $payload)
    {
        if ($payload < 13 && $payload > 0 ) {
            $query = 'INSERT INTO production_plan (`month`) VALUE (`:month`)';

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':month', $payload);
        } else {
            error_log("A month has to be between 1 and 12");
        }
    }

    /**
     * @return array
     */
    public function getProductionPlan(): array {
        $res = array();
        $query = 'SELECT * FROM production_plan';
        $stmt = $this->db->query($query);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $currentPlan = $row["ID"];

            $stmt2 = $this->db->prepare("SELECT ski_type_id, daily_amount FROM production_skis WHERE production_plan_id LIKE :current_ON");
            $stmt2->bindValue(":current_ON,", $currentPlan);
            $stmt2->execute();
            $productionSkisRow = array();
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $productionSkisRow[$row2["ski_type_id"]] = $row2["daily_amount"];
            }
            $row["daily_amount"] = $productionSkisRow;
            $res[] = $row;
        }
        return $res;
    }
    

}