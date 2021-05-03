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
    public function addProductionPlan(array $payload): bool
    {
        $success = false;
        $month = $payload['month'];
        if ($month < 13 && $month > 0 ) {
            try {
                $this->db->beginTransaction();
                $query = 'INSERT INTO production_plan (`month`) VALUE (:month)';

                $stmt = $this->db->prepare($query);
                $date = date("Y") . "-" . $month . "-01";
                $date = date($date, strtotime($date));
                $stmt->bindvalue(':month', $date);
                $stmt->execute();

                $lastId = $this->db->lastInsertId();
                echo $lastId;
                $stmt2 = $this->db->prepare(
                    "INSERT INTO production_skis (ski_type_id, daily_amount, production_plan_month) VALUES (:ski_type_id, :daily_amount, :production_plan_month)");
                foreach ($payload['skis'] as $ski_type_id => $daily_amount) {
                    $stmt2->bindValue(":ski_type_id", $ski_type_id);
                    $stmt2->bindValue(":daily_amount", $daily_amount);
                    $stmt2->bindValue(":production_plan_month", $date);
                    $stmt2->execute();
                }
                $this->db->commit();
                $success = true;
            } catch (Exception $e) {
                $this->db->rollBack();
            }
            return $success;
        } else {
            error_log("A month has to be between 1 and 12");
            return false;
        }
    }

    /** gets the production plan and all skis that are to be produced.
     * @param string $production_plan_id the production plan id
     * @return array consists of the production plan id and an array of the daily amount of skis to be produced
     */
    public function getProductionPlan(string $production_plan_id): array {
        $res = array();

        $query = 'SELECT production_plan.ID FROM production_plan WHERE ID = :production_plan_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":production_plan_id", $production_plan_id);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $currentPlan = $row["ID"];

            $stmt2 = $this->db->prepare("SELECT ski_type_id, daily_amount FROM production_skis WHERE production_plan_id LIKE :current_ON");
            $stmt2->bindValue(":current_ON", $currentPlan);
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