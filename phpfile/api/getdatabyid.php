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


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or missing 'id' parameter."
    ]);
    exit;
}


$stmt = $conn->prepare("SELECT id, email, name, departmentName, studentID, roleUserLevel, createdAt, updatedAt FROM Student WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Student not found."
    ]);
    exit;
}

$student = $result->fetch_assoc();

echo json_encode([
    "status" => "success",
    "data" => $student
]);

$stmt->close();
$conn->close();
