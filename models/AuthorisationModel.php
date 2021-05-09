<?php

class AuthorisationModel {
    protected PDO $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    /**
     * A simple authorisation mechanism - just checking that the token matches the one in the database
     * @param string $token the authorization token
     * @param string $endpoint the endpoint used
     * @return bool indicating whether the token was successfully verified
     */
    public function isValid(string $token, string $endpoint): bool {
        if($endpoint == "storekeeper" || $endpoint == "customer-rep" || $endpoint == "planner") {
            $endpoint = "employee";
        }
        $query = "SELECT COUNT(*) FROM auth_token WHERE token = :token AND endpoint = :endpoint";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':endpoint', $endpoint);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM);
        if ($row[0] == 0) {
            return false;
        } else {
            return true;
        }
    }

}