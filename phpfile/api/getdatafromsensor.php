<?php
require_once(__DIR__ . "/phpMQTT.php"); // Load your MQTT library
require_once(__DIR__ . "/connectdb.php"); // Connect to your MySQL (provides $conn)

// MQTT broker settings
$server = "localhost"; // broker IP or domain
$port = 1883;
$client_id = "php-subscriber-" . uniqid();

use Bluerhinos\phpMQTT;

$mqtt = new phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, '', '')) {
    exit("Failed to connect to broker\n");
}

// Subscribe to topic 'test' with QoS 0 and callback function
$topics['test'] = ["qos" => 0, "function" => "handleMessage"];
$mqtt->subscribe($topics, 0);

while ($mqtt->proc()) {
    // Looping until manually stopped
}

$mqtt->close();

// Callback function to handle incoming messages
function handleMessage($topic, $msg) {
    global $conn; // use your DB connection

    echo "Received message on topic [$topic]: $msg\n";

    $data = json_decode($msg, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Invalid JSON received\n";
        return;
    }

   
    if (!isset($data['sensorID'], $data['temperature'], $data['timestamp'])) {
        echo "Missing required fields\n";
        return;
    }


    $stmt = $conn->prepare("INSERT INTO sensors (sensorID, temperature, timestamp) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error . "\n";
        return;
    }

    $sensorID = $data['sensorID'];
    $temperature = floatval($data['temperature']);
    $timestamp_mysql = date('Y-m-d H:i:s', strtotime($timestamp_iso)); // Expecting a valid datetime string

    $stmt->bind_param("sds", $sensorID, $temperature, $timestamp);

    if ($stmt->execute()) {
        echo "Data inserted successfully\n";
    } else {
        echo "Insert error: " . $stmt->error . "\n";
    }

    $stmt->close();
}
?>
