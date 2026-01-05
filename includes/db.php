
<?php
$db = new PDO("sqlite:" . __DIR__ . '/../data/contact_book.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS users (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 username TEXT UNIQUE,
 password TEXT,
 role TEXT
)");
$db->exec("CREATE TABLE IF NOT EXISTS employees (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 name TEXT, emp_code TEXT, department TEXT,
 designation TEXT, site_name TEXT, mobile TEXT, email TEXT
)");

$c=$db->query("SELECT COUNT(*) FROM users WHERE username='ADMIN'")->fetchColumn();
if($c==0){
 $db->prepare("INSERT INTO users(username,password,role) VALUES (?,?,?)")
    ->execute(["ADMIN",password_hash("ADMIN",PASSWORD_DEFAULT),"ADMIN"]);
}
?>
