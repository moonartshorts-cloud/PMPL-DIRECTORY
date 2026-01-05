
<?php
include '../includes/session.php';
if($_SESSION['role']!=='ADMIN'){die('Unauthorized');}
include '../includes/db.php';
$f=$_FILES['file']['tmp_name'];
$h=fopen($f,"r"); fgetcsv($h);
$stmt=$db->prepare("INSERT INTO employees
(name,emp_code,department,designation,site_name,mobile,email)
VALUES (?,?,?,?,?,?,?)");
while(($r=fgetcsv($h))!==false){$stmt->execute($r);}
fclose($h);
header("Location: ../pages/dashboard.php");
?>
