<?php
header('Content-Type: application/json');
require 'connectdb.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed. Use POST."
    ]);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input."
    ]);
    exit;
}

$requiredFields = ['email', 'name', 'departmentName', 'studentID', 'roleUserLevel'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Missing required field: $field"
        ]);
        exit;
    }
}


$email = $input['email'];
$name = $input['name'];
$departmentName = $input['departmentName'];
$studentID = $input['studentID'];
$roleUserLevel = $input['roleUserLevel'];


$stmt = $conn->prepare("INSERT INTO Student (email, name, departmentName, studentID, roleUserLevel) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $email, $name, $departmentName, $studentID, $roleUserLevel);

if ($stmt->execute()) {
    http_response_code(201); 
    echo json_encode([
        "status" => "success",
        "message" => "Student record created.",
        "studentId" => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Insert failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
