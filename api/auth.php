
<?php
header('Content-Type: application/json');
session_start();
include '../includes/db.php';
$d=json_decode(file_get_contents("php://input"),true);
$stmt=$db->prepare("SELECT password,role FROM users WHERE username=?");
$stmt->execute([$d['username']]);
$r=$stmt->fetch(PDO::FETCH_ASSOC);
if($r && password_verify($d['password'],$r['password'])){
 $_SESSION['user']=$d['username'];
 $_SESSION['role']=$r['role'];
 echo json_encode(["success"=>true]);
}else echo json_encode(["success"=>false,"msg"=>"Invalid credentials"]);
?>
