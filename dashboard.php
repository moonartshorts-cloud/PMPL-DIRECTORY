<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../includes/session.php'; 
?>
<!DOCTYPE html>
<html>
<head>
<title>Employee Directory</title>
<link rel="stylesheet" href="../assets/css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    /* Specific styles for User Search Boxes */
    .user-search-container {
        display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;
        background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .user-search-container input { margin: 0; flex: 1; min-width: 150px; }
    .user-search-container button { 
        width: auto; padding: 0 30px; background: #2563eb; color: white; border-radius: 10px; font-weight: 600;
    }
    .empty-state { text-align: center; color: #9ca3af; padding: 40px; font-size: 14px; }
</style>
</head>
<body>

<header class="header">
 <div class="brand" style="font-weight:700; font-size: 18px;">📒 PMPL Directory</div>
 <div class="user">
    <span style="font-size:14px; opacity:0.9">👤 <?=$_SESSION['user']?> (<?=$_SESSION['role']?>)</span>
    <a href="logout.php"><button class="logout-btn">Logout</button></a>
 </div>
</header>

<div class="app" style="<?php if($_SESSION['role']!=='ADMIN') echo 'grid-template-columns:1fr'; ?>">

<?php if($_SESSION['role']==='ADMIN'): ?>
<aside class="sidebar">
 <button onclick="showEmp()">📘 Book Entry</button>
 <button onclick="showUser()">➕ Add User</button>
 <button onclick="showBulk()">📤 Bulk Upload</button>
</aside>
<?php endif; ?>

<main class="main">
 
 <?php if($_SESSION['role']==='ADMIN'): ?>
     <input id="search" placeholder="🔍 Search by name, dept, email, mobile..." onkeyup="loadAdmin()">
 <?php else: ?>
     <div class="user-search-container">
        <input id="s_name" placeholder="Name">
        <input id="s_code" placeholder="Emp ID">
        <input id="s_dept" placeholder="Department">
        <button onclick="searchUser()">Search</button>
     </div>
 <?php endif; ?>

 <div class="table-wrap">
  <table id="tbl">
   <thead>
    <tr>
     <th>Name</th><th>Code</th><th>Dept</th>
     <th>Desig</th><th>Site</th><th>Mobile</th><th>Email</th>
     <?php if($_SESSION['role']==='ADMIN') echo '<th class="col-action">Action</th>'; ?>
    </tr>
   </thead>
   <tbody></tbody>
  </table>
  <div id="emptyMsg" class="empty-state" style="display:none">No records found</div>
 </div>

 <footer class="footer">
    Copyright &copy; PMPL Contact Book. All rights reserved.
 </footer>
</main>
</div>

<div class="modal" id="empModal"><div class="modal-box">
<h3 style="margin-top:0">📘 New Entry</h3>
<input id="en" placeholder="Name"><input id="ec" placeholder="Emp Code"><input id="ed" placeholder="Department">
<input id="eg" placeholder="Designation"><input id="es" placeholder="Site Name"><input id="em" placeholder="Mobile"><input id="ee" placeholder="Email">
<button onclick="addEmp()">Save Record</button><button onclick="hide()" style="background:#fff;color:#333;border:1px solid #ddd">Cancel</button>
</div></div>

<div class="modal" id="editModal"><div class="modal-box">
<h3 style="margin-top:0">✏️ Edit Employee</h3>
<input type="hidden" id="edit_id">
<input id="edit_name" placeholder="Name"><input id="edit_code" placeholder="Emp Code"><input id="edit_dept" placeholder="Department">
<input id="edit_desig" placeholder="Designation"><input id="edit_site" placeholder="Site Name"><input id="edit_mobile" placeholder="Mobile"><input id="edit_email" placeholder="Email">
<button onclick="saveEdit()">Update Record</button><button onclick="hide()" style="background:#fff;color:#333;border:1px solid #ddd">Cancel</button>
</div></div>

<div class="modal" id="userModal"><div class="modal-box">
<h3 style="margin-top:0">➕ Add User</h3>
<input id="nu" placeholder="Username"><input id="np" placeholder="Password"><select id="nr"><option>USER</option><option>ADMIN</option></select>
<button onclick="addUser()">Create User</button><button onclick="hide()" style="background:#fff;color:#333;border:1px solid #ddd">Cancel</button>
</div></div>

<div class="modal" id="bulkModal"><div class="modal-box">
<h3 style="margin-top:0">📤 Bulk Upload</h3>
<a href="../templates/employee_template.csv" style="display:block;margin-bottom:10px;color:#2563eb" download>⬇ Download Template</a>
<form method="post" enctype="multipart/form-data" action="../api/bulk_upload.php">
<input type="file" name="file" accept=".csv" required style="border:1px dashed #ccc">
<button type="submit">Upload CSV</button><button type="button" onclick="hide()" style="background:#fff;color:#333;border:1px solid #ddd">Cancel</button>
</form>
</div></div>

<script>
const role = "<?php echo $_SESSION['role']; ?>";
const isAdmin = (role === 'ADMIN');

// --- ADMIN LOGIC (Auto Load) ---
function loadAdmin(){
 if(!isAdmin) return; // Safety check
 fetch('../api/employees.php?search='+(document.getElementById('search').value || ''))
 .then(r=>r.json()).then(renderTable);
}

// --- USER LOGIC (Manual Search) ---
function searchUser(){
 let n = document.getElementById('s_name').value.trim();
 let c = document.getElementById('s_code').value.trim();
 let d = document.getElementById('s_dept').value.trim();

 if(!n && !c && !d) {
    alert("Please fill at least one box to search.");
    return;
 }

 // Call API with specific filters
 fetch(`../api/employees.php?name=${encodeURIComponent(n)}&code=${encodeURIComponent(c)}&dept=${encodeURIComponent(d)}`)
 .then(r=>r.json()).then(renderTable);
}

// --- SHARED RENDER LOGIC ---
function renderTable(data){
  let b = document.querySelector('#tbl tbody'); 
  let msg = document.getElementById('emptyMsg');
  b.innerHTML='';
  
  if(data.length === 0){
      msg.style.display = 'block';
      msg.innerText = isAdmin ? "No employees found" : "No results. Try searching.";
      return;
  }
  msg.style.display = 'none';

  data.forEach(e=>{
   let rowContent = `
       <td><strong>${e.name||''}</strong></td>
       <td><span style="background:#eff6ff;padding:2px 6px;border-radius:4px;font-size:12px;color:#2563eb">${e.emp_code||''}</span></td>
       <td>${e.department||''}</td>
       <td>${e.designation||''}</td>
       <td>${e.site_name||''}</td>
       <td>${e.mobile||''}</td>
       <td>${e.email||''}</td>`;

   if(isAdmin) {
       let safeName = e.name ? e.name.replace(/'/g, "\\'") : ''; 
       rowContent += `
       <td class="col-action">
        <button class="btn-action btn-edit" onclick="openEdit(${e.id}, '${safeName}', '${e.emp_code}', '${e.department}', '${e.designation}', '${e.site_name}', '${e.mobile}', '${e.email}')">Edit</button>
        <button class="btn-action btn-del" onclick="delEmp(${e.id})">Del</button>
       </td>`;
   }
   b.innerHTML += `<tr>${rowContent}</tr>`;
  });
}

// Initial Load: Only for Admin
if(isAdmin) loadAdmin();
else {
    document.getElementById('emptyMsg').style.display='block';
    document.getElementById('emptyMsg').innerText='Enter details above to search.';
}

// --- MODAL FUNCTIONS (Admin Only) ---
function showEmp(){empModal.style.display='flex';}
function showUser(){userModal.style.display='flex';}
function showBulk(){bulkModal.style.display='flex';}
function hide(){empModal.style.display='none';userModal.style.display='none';bulkModal.style.display='none';editModal.style.display='none';}

function addEmp(){
 fetch('../api/add_employee.php',{method:'POST',headers:{'Content-Type':'application/json'},
 body:JSON.stringify({
  name:en.value, emp_code:ec.value, department:ed.value,
  designation:eg.value, site_name:es.value, mobile:em.value, email:ee.value
 })}).then(r=>r.json()).then(d=>{if(d.success){hide();loadAdmin();en.value='';}});
}

function openEdit(id, name, code, dept, desig, site, mob, email){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_dept').value = dept;
    document.getElementById('edit_desig').value = desig;
    document.getElementById('edit_site').value = site;
    document.getElementById('edit_mobile').value = mob;
    document.getElementById('edit_email').value = email;
    editModal.style.display = 'flex';
}

function saveEdit(){
 fetch('../api/update_employee.php',{method:'POST',headers:{'Content-Type':'application/json'},
 body:JSON.stringify({
  id: document.getElementById('edit_id').value,
  name: document.getElementById('edit_name').value,
  emp_code: document.getElementById('edit_code').value,
  department: document.getElementById('edit_dept').value,
  designation: document.getElementById('edit_desig').value,
  site_name: document.getElementById('edit_site').value,
  mobile: document.getElementById('edit_mobile').value,
  email: document.getElementById('edit_email').value
 })}).then(r=>r.json()).then(d=>{if(d.success){hide();loadAdmin();} else {alert(d.msg)}});
}

function delEmp(id){
 if(!confirm('Are you sure you want to delete this employee?')) return;
 fetch('../api/delete_employee.php',{method:'POST',headers:{'Content-Type':'application/json'},
 body:JSON.stringify({id:id})}).then(r=>r.json()).then(d=>{if(d.success) loadAdmin(); else alert('Error deleting');});
}

function addUser(){
 fetch('../api/add_user.php',{method:'POST',headers:{'Content-Type':'application/json'},
 body:JSON.stringify({username:nu.value,password:np.value,role:nr.value})}).then(r=>r.json()).then(d=>{alert(d.success?'User added':d.msg);hide();});
}
</script>
</body>
</html>