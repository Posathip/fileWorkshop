<?php
header('Content-Type: application/json');
require 'connectdb.php'; 


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); 
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed. Use GET."
    ]);
    exit;
}


$sql = "SELECT id, email, name, departmentName, studentID, roleUserLevel, createdAt, updatedAt FROM Student";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database query failed: " . $conn->error
    ]);
    exit;
}

$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $students
]);

$conn->close();
?>
