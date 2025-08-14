<?php
header('Content-Type: application/json');

require 'connectdb.php'; 


$sql = "
CREATE TABLE IF NOT EXISTS Student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    departmentName VARCHAR(255) NOT NULL,
    studentID VARCHAR(255) NOT NULL,
    roleUserLevel VARCHAR(255) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Try executing query
if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "status" => "success",
        "message" => "Table 'Student' created successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
}

$conn->close();
?>
