<?php
require_once(__DIR__ . "/phpMQTT.php"); // MQTT library
require_once(__DIR__ . "/connectdb.php"); // MySQL connection ($conn)

$server = "localhost"; 
$port = 1883;
$username = "";
$password = "";
$client_id = "php-subscriber-" . uniqid();

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, $username, $password)) {
    exit("Failed to connect to broker\n");
}
echo "MQTT connected successfully to {$server}:{$port}\n";

$topics['test'] = array("qos" => 0, "function" => "saveToDatabase");
$mqtt->subscribe($topics, 0);

while ($mqtt->proc()) {}

$mqtt->close();
$conn->close();


function saveToDatabase($topic, $msg) {
    global $conn;

    echo "Message on topic {$topic}: {$msg}\n";


    $data = json_decode($msg, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Invalid JSON received\n";
        return;
    }


    if (!isset($data['sensorID'], $data['temperature'], $data['timestamp'])) {
        echo "Missing required fields in message\n";
        return;
    }

    
    $timestamp_mysql = date("Y-m-d H:i:s", strtotime($data['timestamp']));

    // Prepare and insert
    $stmt = $conn->prepare("
        INSERT INTO sensors (sensorID, temperature, timestamp)
        VALUES (?, ?, ?)
    ");

    if ($stmt === false) {
        echo "Prepare failed: " . $conn->error . "\n";
        return;
    }

    $stmt->bind_param(
        "sds", 
        $data['sensorID'], 
        $data['temperature'], 
        $timestamp_mysql
    );

    if ($stmt->execute()) {
        echo "Data inserted successfully\n";
    } else {
        echo "Insert failed: " . $stmt->error . "\n";
    }

    $stmt->close();
 
}
