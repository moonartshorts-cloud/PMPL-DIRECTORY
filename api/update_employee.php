<?php
header('Content-Type: application/json');
include '../includes/session.php';

// Security: Only ADMIN can edit
if($_SESSION['role'] !== 'ADMIN'){
    echo json_encode(["success"=>false, "msg"=>"Unauthorized"]);
    exit;
}

include '../includes/db.php';
$d = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $db->prepare("UPDATE employees SET 
        name=?, emp_code=?, department=?, designation=?, site_name=?, mobile=?, email=? 
        WHERE id=?");
    $stmt->execute([
        $d['name'], $d['emp_code'], $d['department'], $d['designation'], 
        $d['site_name'], $d['mobile'], $d['email'], $d['id']
    ]);
    echo json_encode(["success"=>true]);
} catch(Exception $e) {
    echo json_encode(["success"=>false, "msg"=>"Error updating record"]);
}
?>