<?php
header('Content-Type: application/json');
include '../includes/session.php';
include '../includes/db.php';

// Check if this is a "Specific Filter" search (For Users)
$name = $_GET['name'] ?? null;
$code = $_GET['code'] ?? null;
$dept = $_GET['dept'] ?? null;

// Check if this is a "Generic Wildcard" search (For Admins)
$generic = $_GET['search'] ?? null;

if ($name !== null || $code !== null || $dept !== null) {
    // --- USER MODE: Specific Filters ---
    $sql = "SELECT * FROM employees WHERE 1=1";
    $params = [];
    
    // Build query dynamically based on filled boxes
    if (!empty($name)) { 
        $sql .= " AND name LIKE ?"; 
        $params[] = "%$name%"; 
    }
    if (!empty($code)) { 
        $sql .= " AND emp_code LIKE ?"; 
        $params[] = "%$code%"; 
    }
    if (!empty($dept)) { 
        $sql .= " AND department LIKE ?"; 
        $params[] = "%$dept%"; 
    }

    // Security: If user clicked search but left all boxes empty, show NOTHING.
    if (empty($params)) {
        echo json_encode([]); 
        exit;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} else {
    // --- ADMIN MODE: Generic Search (Existing Logic) ---
    $s = $generic ?? ''; 
    $t = "%$s%";
    $q = $db->prepare("SELECT * FROM employees WHERE
     name LIKE ? OR emp_code LIKE ? OR department LIKE ? OR designation LIKE ?
     OR site_name LIKE ? OR mobile LIKE ? OR email LIKE ?");
    $q->execute([$t,$t,$t,$t,$t,$t,$t]);
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
}
?>