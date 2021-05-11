<?php
require_once 'dbCredentials.php';

class SkisModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /** A method to get a list of all the ski types
     * @return array An array of all the ski types in the database
     */
    public function getSkiTypes(): array {
        $res = array();
        $query = 'SELECT * FROM ski_type';

        $stmt = $this->db->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }
    /** A method to get a list of ski types by model
     * @param array $queries
     * Index 0 = model
     * @return array An array of ski types of this model
     */
    public function getSkiTypesByModel(array $queries): array
    {
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

    /** A method to get a list of ski types by grip system
     * @param array $queries grip system in ski_type
     * Index 0 = grip_system
     * @return array Array of ski types with this grip system
     */
    public function getSkiTypesByGripSystem(array $queries): array
    {
        $res = array();
        foreach ($queries as $grip) {
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
    public function getSkis(): array
    {
        $res = array();

        $query = 'SELECT ski.*, ski_type.* FROM ski INNER JOIN ski_type ON ski.ski_type_id = ski_type.ID';

        $stmt = $this->db->query($query);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * Gets an individual ski, based on the order number of that specific ski
     * @param string $production_number is the production number of the ski
     * @return array single ski returned as an array
     */
    public function getSki(string $production_number): array
    {
        $res = array();

        $query = 'SELECT ski.*, ski_type.* FROM ski 
        INNER JOIN ski_type ON ski.ski_type_id = ski_type.ID 
        WHERE production_number = :production_number';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':production_number', $production_number);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * A function that changes the values of available and order_no for a ski
     * using an update SQL statement
     * @param array $queries the user requested queries including the order number
     * @return bool Returns if the update function was successful enough
     */
    public function addSkiToOrder(array $queries): bool
    {

        $success = false;
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('UPDATE ski SET  available = :available, order_no = :order_no WHERE production_number LIKE production_number');
            $stmt->bindValue(":available", 0);
            $stmt->bindValue(":order_no", $queries['order_number']);
            $stmt->execute();
            $success = true;
        } catch (Exception $e) {
            $this->db->rollBack();
            print($e);
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
    public function addSki(array $payload): bool
    {
        $success = false;
        try {
            $this->db->beginTransaction();
            $query = 'INSERT INTO ski (available, order_no, ski_type_id) VALUES
        (:available, :order_no, :ski_type_id)';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':available', 1);
            $stmt->bindValue(':order_no', null);
            $stmt->bindValue(':ski_type_id', $payload['ski_type_id']);
            $stmt->execute();
            $this->db->commit();
            $success = true;
        } catch (Exception $e) {
            $this->db->rollBack();
            print($e);
        }
        return $success;
    }

    /** Checks if a ski type exists
     * @param string $ski_type_id the id of the ski type
     * @return bool true if ski type exist, false otherwise
     */
    public function skiTypeExist(string $ski_type_id): bool
    {

        $query = 'SELECT COUNT(1) FROM ski_type WHERE ID = :ski_type_id;';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':ski_type_id', $ski_type_id);
        $stmt->execute();

        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res["COUNT(1)"];
    }

    /**
     * Finds the last inserted ski
     * It is an auto increment so the last descending production_number
     * will always be the latest added
     * @return array the ski as an array
     */
    public function getLastInsertedSki(): array {
        $res = array();

        $query = 'SELECT * FROM ski ORDER BY production_number DESC LIMIT 1';

        $stmt = $this->db->query($query);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;

    }
}