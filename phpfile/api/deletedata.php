<?php
header('Content-Type: application/json');
require 'connectdb.php';


if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed. Use DELETE."
    ]);
    exit;
}


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    // fallback กรณี id อยู่ใน body ของ DELETE
    parse_str(file_get_contents("php://input"), $delete_vars);
    if (isset($delete_vars['id'])) {
        $id = intval($delete_vars['id']);
    }
}
if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or missing 'id' parameter."
    ]);
    exit;
}


$stmt = $conn->prepare("DELETE FROM Student WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Student record deleted."
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Student not found."
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
