<!DOCTYPE html>
<html>
<head>
<title>Contact Book Login</title>
<link rel="stylesheet" href="../assets/css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="login-page">
<div class="login-card">
<h2>📒 Contact Book</h2>
<input id="u" placeholder="Username">
<input id="p" type="password" placeholder="Password">
<button onclick="login()">Login</button>
<p id="msg"></p>
</div>

<footer class="footer login-footer">
    Copyright @PMPL Contact Book. All right reserved.
</footer>

<script>
function login(){
 fetch('../api/auth.php',{method:'POST',headers:{'Content-Type':'application/json'},
 body:JSON.stringify({username:u.value,password:p.value})})
 .then(r=>r.json()).then(d=>{
  if(d.success) location.href='dashboard.php';
  else msg.innerText=d.msg;
 });
}
</script>
</body>
</html>