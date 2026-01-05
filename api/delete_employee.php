<?php
header('Content-Type: application/json');
include '../includes/session.php';

// Security: Only ADMIN can delete
if($_SESSION['role'] !== 'ADMIN'){
    echo json_encode(["success"=>false, "msg"=>"Unauthorized"]);
    exit;
}

include '../includes/db.php';
$d = json_decode(file_get_contents("php://input"), true);

if(isset($d['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$d['id']]);
        echo json_encode(["success"=>true]);
    } catch(Exception $e) {
        echo json_encode(["success"=>false, "msg"=>"Database error"]);
    }
} else {
    echo json_encode(["success"=>false, "msg"=>"ID missing"]);
}
?>