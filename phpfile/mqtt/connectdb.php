<?php

define('DB_HOST', 'localhost');  // Host Database
define('DB_USER', 'testconso_student');  //  Database Username
define('DB_PASS', 'AhsjLu3Le3tSKDvWdtGk');  //  Database password
define('DB_NAME', 'testconso_student');  //  Database name


$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
