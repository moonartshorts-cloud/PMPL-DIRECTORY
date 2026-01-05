
<?php
header('Content-Type: application/json');
include '../includes/session.php';
if($_SESSION['role']!=='ADMIN'){echo json_encode(["success"=>false]);exit;}
include '../includes/db.php';
$d=json_decode(file_get_contents("php://input"),true);
$db->prepare("INSERT INTO employees
(name,emp_code,department,designation,site_name,mobile,email)
VALUES (?,?,?,?,?,?,?)")
->execute([$d['name'],$d['emp_code'],$d['department'],$d['designation'],$d['site_name'],$d['mobile'],$d['email']]);
echo json_encode(["success"=>true]);
?>
