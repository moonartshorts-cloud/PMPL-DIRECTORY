
<?php
header('Content-Type: application/json');
include '../includes/session.php';
include '../includes/db.php';
$s=$_GET['search']??''; $t="%$s%";
$q=$db->prepare("SELECT * FROM employees WHERE
 name LIKE ? OR emp_code LIKE ? OR department LIKE ? OR designation LIKE ?
 OR site_name LIKE ? OR mobile LIKE ? OR email LIKE ?");
$q->execute([$t,$t,$t,$t,$t,$t,$t]);
echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
?>
