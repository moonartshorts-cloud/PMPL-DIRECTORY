
<?php
header('Content-Type: application/json');
include '../includes/session.php';
if($_SESSION['role']!=='ADMIN'){echo json_encode(["success"=>false]);exit;}
include '../includes/db.php';
$d=json_decode(file_get_contents("php://input"),true);
try{
 $db->prepare("INSERT INTO users(username,password,role) VALUES (?,?,?)")
    ->execute([$d['username'],password_hash($d['password'],PASSWORD_DEFAULT),$d['role']]);
 echo json_encode(["success"=>true]);
}catch(Exception $e){ echo json_encode(["success"=>false,"msg"=>"User exists"]); }
?>
