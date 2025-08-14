<?php
header('Content-Type: application/json');

require 'connectdb.php'; // must create $conn (mysqli object)

// SQL to create the sensors table
$sql = "
CREATE TABLE IF NOT EXISTS sensors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensorID VARCHAR(255) NOT NULL,
    temperature FLOAT NOT NULL,
    timestamp TIMESTAMP NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Try executing query
if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "status" => "success",
        "message" => "Table 'sensors' created successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
}

$conn->close();
?>
