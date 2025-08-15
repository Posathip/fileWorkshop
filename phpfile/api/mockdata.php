<?php
header('Content-Type: application/json');
require 'connectdb.php'; 

$mockData = [
    ['alice@example.com', 'Alice Johnson', 'ABC University', 'S001', 'student'],
    ['bob@example.com', 'Bob Smith', 'XYZ College', 'S002', 'student'],
    ['carol@example.com', 'Carol White', 'ABC University', 'S003', 'student'],
    ['david@example.com', 'David Brown', 'LMN Institute', 'S004', 'student'],
    ['eve@example.com', 'Eve Black', 'XYZ College', 'S005', 'student'],
    ['frank@example.com', 'Frank Harris', 'ABC University', 'S006', 'student'],
    ['grace@example.com', 'Grace Lee', 'LMN Institute', 'S007', 'student'],
    ['henry@example.com', 'Henry Clark', 'XYZ College', 'S008', 'student'],
    ['irene@example.com', 'Irene Scott', 'ABC University', 'S009', 'student'],
    ['jack@example.com', 'Jack Turner', 'LMN Institute', 'S010', 'student']
];

$inserted = 0;
$errors = [];

foreach ($mockData as $row) {
    [$email, $name, $departmentName, $studentID, $roleUserLevel] = $row;

    $stmt = $conn->prepare("
        INSERT INTO Student (email, name, departmentName, studentID, roleUserLevel)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $email, $name, $departmentName, $studentID, $roleUserLevel);

    if ($stmt->execute()) {
        $inserted++;
    } else {
        $errors[] = [
            "email" => $email,
            "error" => $stmt->error
        ];
    }

    $stmt->close();
}

$conn->close();

if (empty($errors)) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "inserted" => $inserted,
        "errors" => []
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "partial_error",
        "inserted" => $inserted,
        "errors" => $errors
    ]);
}
