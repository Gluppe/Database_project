<?php
require_once "dbCredentials.php";
$servername = DB_HOST;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PWD;

// Name of the file
$filename = 'tests/_data/testdb.sql';

/* Create connection */
$conn = new mysqli($servername, $username, $password);
/* Check connection */
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
/* Create database */
$sql = "CREATE DATABASE " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "Database " . DB_NAME . " created successfully\n";

} else {
    echo "Error creating database: " . $conn->error;
}
$sql = file_get_contents($filename);
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->multi_query($sql)) {
    echo "Successfully added tables to the database";
} else {
    echo "Failed adding tables to the database: " . $conn->error;
}

$conn->close();
