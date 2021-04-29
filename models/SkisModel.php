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

    /** A method to get a list of skis by model
     * @param array $queries
     * Index 0 = model
     * @return array An array of skis of this model
     */
    public function getSkiTypesByModel(array $queries): array {
        $res = array();

        foreach ($queries as $model) {
            $query = 'SELECT * FROM ski_type WHERE ski_type.model = :model';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':model', $model);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[] = $row;
            }
        }
        return $res;
    }

    /** A method to get a list of skis by grip system
     * @param array $queries grip system in ski_type
     * Index 0 = grip_system
     * @return array Array of skis with this grip system
     */
    public function getSkiTypesByGripSystem(array $queries): array {
        $res = array();
        foreach($queries as $grip) {
            $query = 'SELECT * FROM ski_type WHERE ski_type.grip_system = :grip_system';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':grip_system', $grip);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $res[] = $row;
            }
        }
        return $res;
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
     * @param array $payload is the production number of the ski
     * Index 1 = production_number
     * @return array single ski returned as an array
     */
    public function getSki(array $payload): array {
        $res = array();

        $query = 'SELECT ski.*, ski_type.* FROM ski 
        INNER JOIN ski_type ON ski.ski_type_id = ski_type.ID 
        WHERE production_number = :production_number';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':production_number', $payload[0]);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * A function that changes the values og available and order_no for a ski
     * using an update SQL statement
     * @param array $payload The array we receive with available and order_no parameters
     * index 0 = available
     * index 1 = order_no
     * @return bool Returns if the update function was sucsessful enough
     */
    public function updateSki(array $payload) : bool {

            $success = false;
            try {$stmt = $this->db->prepare('UPDATE ski SET  available = :available, order_no = :order_no WHERE production_number LIKE production_number');
                $stmt->bindValue(":available", $payload[0]);
                $stmt->bindValue(":order_no", $payload[1]);
                $stmt->execute();
                $success = true;
            }catch (\mysql_xdevapi\Exception){
                echo "Something went wrong with update ski";
            }
            return $success;
        }

    /**
     * A function for adding a new ski using an insert into SQL statement
     * @param array $payload
     * index 0 = available
     * index 1 = order_no
     * index 2 = ski_type_id
     */
    public function addSki(array $payload): bool{
        $success = false;
        try {
            $this->db->beginTransaction();
            $query = 'INSERT INTO ski (available, order_no, ski_type_id) VALUES
        (:available, :order_no, :ski_type_id)';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':available', 0);
            $stmt->bindValue(':order_no', null);
            $stmt->bindValue(':ski_type_id', $payload['ski_type_id']);
            $stmt->execute();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
        return $success;
    }

    /** Checks if a ski type exists
     * @param int $ski_type_id the id of the ski type
     */
    public function skiTypeExist(int $ski_type_id): bool {

        $query = 'SELECT COUNT(1) FROM ski_type WHERE ID = :ski_type_id;';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':ski_type_id', $ski_type_id);
        $stmt->execute();

        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res["COUNT(1)"];
    }
}