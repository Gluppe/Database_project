<?php
require_once 'dbCredentials.php';

class SkisModel
{

    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * Creates an array of all the skis by preforming an inner join on ski and ski_type
     * Then ski with the matching ski type id gets all the information from the their ski_type
     * table
     * @return array array of all the skis
     */
    public function getSkis(): array {
        $res = array();

        $query = 'SELECT ski.*, ski_type.* FROM ski INNER JOIN ski_type ON ski.ski_type_id = ski_type.ID';

        $stmt = $this->db->query($query);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * Gets an individual ski, based on the order number of that specific ski
     * @param $production_number is the production number of the ski
     * @return array single ski returned as an array
     */
    public function getSki(int $production_number): array {
        $res = array();

        $query = 'SELECT ski.*, ski_type.* FROM ski 
        INNER JOIN ski_type ON ski.ski_type_id = ski_type.ID 
        WHERE production_number = :production_number';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':production_number', $production_number);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * A function that changes the values og available and order_no for a ski
     * using an update SQL statement
     * @param bool $available What state the ski is in
     * @param int $order_no Which order this ski belongs to
     * @return bool Returns if the update function was sucsessful enough
     */
    public function updateSki(bool $available, int $order_no) : bool {

            $success = false;
            try {$stmt = $this->db->prepare('UPDATE ski SET  available = :available, order_no = :order_no WHERE production_number LIKE production_number');
                $stmt->bindValue(":available", $available);
                $stmt->bindValue(":order_no", $order_no);
                $stmt->execute();
                $success = true;
            }catch (\mysql_xdevapi\Exception){
                echo "Something went wrong with update ski";
            }
            return $success;
        }

    /**
     * A function for adding a new ski using an insert into SQL statement
     * @param int $production_number Production number of a specific ski
     * @param bool $available If it is available or not
     * @param int $order_no Order number of a spesific ski
     * @param int $ski_type_id Id of the ski type this ski belongs to
     */
    public function addSki(int $production_number, bool $available, int $order_no, int $ski_type_id) {

        $query = 'INSERT INTO ski (production_number, available, order_no, ski_type_id) VALUES
        (:production_number, :available, :order_no, :ski_type_id)';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':production_number', $production_number);
        $stmt->bindValue(':available', $available);
        $stmt->bindValue(':order_no', $order_no);
        $stmt->bindValue(':ski_type_id', $ski_type_id);
        $stmt->execute();

    }




}