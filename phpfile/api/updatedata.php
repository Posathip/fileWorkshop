<?php
header('Content-Type: application/json');
require 'connectdb.php';

// เช็คว่า method ต้องเป็น PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed. Use PUT."
    ]);
    exit;
}

// รับ id จาก query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or missing 'id' parameter."
    ]);
    exit;
}

// รับข้อมูล JSON body ที่จะใช้ update
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input."
    ]);
    exit;
}

// กำหนด fields ที่อนุญาตให้อัปเดต (ตามโครงสร้างตาราง)
$allowedFields = ['email', 'name', 'departmentName', 'studentID', 'roleUserLevel'];

// เตรียมส่วนของ SQL SET statement และค่าที่จะ bind
$setParts = [];
$values = [];

foreach ($allowedFields as $field) {
    if (isset($input[$field])) {
        $setParts[] = "$field = ?";
        $values[] = $input[$field];
    }
}

if (count($setParts) === 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "No valid fields to update."
    ]);
    exit;
}

$setSql = implode(", ", $setParts);

// เตรียม SQL update statement
$sql = "UPDATE Student SET $setSql WHERE id = ?";
$values[] = $id;  // id เป็นพารามิเตอร์ตัวสุดท้าย

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

// สร้าง string types สำหรับ bind_param เช่น "ssssi"
$types = str_repeat("s", count($values) - 1) . "i";

// bind params แบบ dynamic
$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Student updated."
        ]);
    } else {
        // แถวไม่มีการเปลี่ยนแปลง (id ไม่เจอ หรือข้อมูลเหมือนเดิม)
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Student not found or no changes made."
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Update failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
