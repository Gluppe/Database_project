<?php

class ShipmentsModel
{

    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * @param array $payload
     * Index 0 = shipment_number
     * @return array
     */
    public function getShipment(array $payload): array {
        $res = array();

        $query = 'SELECT * FROM shipments WHERE shipment_number = :shipment_number';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':shipment_number', $payload[0]);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $row;
        }
        return $res;
    }
}