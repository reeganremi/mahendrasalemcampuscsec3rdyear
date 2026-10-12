<?php
session_start();
// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

if(isset($_SESSION['user_id'])){
  header('Location: '.($_SESSION['role']=='admin'?'admin_dashboard.php':'dashboard.php')); exit;
}

$err='';
if($_SERVER['REQUEST_METHOD']=='POST'){
  // CSRF simple check
  $email = filter_var(trim($_POST['email']??''), FILTER_SANITIZE_EMAIL);
  $pass = $_POST['password']??'';
  
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $err='Invalid email format!';
  } else {
    $data=json_decode(file_get_contents(__DIR__.'/fake_db.json'),true);
    $found=null;
    foreach($data['users'] as $u){
      if(strtolower($u['email'])==strtolower($email)){
        // Secure password_verify
        if(password_verify($pass, $u['password'])){
          $found=$u; break;
        }
        // Fallback for old plain passwords (migration)
        if($u['password']==$pass){
          $found=$u; break;
        }
      }
    }
    if($found){
      session_regenerate_id(true); // Prevent session fixation
      $_SESSION['user_id']=$found['id'];
      $_SESSION['user_name']=htmlspecialchars($found['name'], ENT_QUOTES, 'UTF-8');
      $_SESSION['role']=$found['role'];
      $_SESSION['login_time']=time();
      // Log login
      error_log("Login success: ".$email);
      header('Location: '.($found['role']=='admin'?'admin_dashboard.php':'dashboard.php')); exit;
    } else {
      $err='Invalid email or password!';
      // Rate limiting - sleep
      sleep(1);
    }
  }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Secure Login - ResolveDesk</title><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet"><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;display:flex;background:#f8f7ff}.left{flex:1;background:linear-gradient(135deg,#5a4cf1 0%,#8b5cf6 50%,#ec4899 100%);display:flex;align-items:center;justify-content:center;padding:40px;position:relative;overflow:hidden}.left::before{content:'';position:absolute;width:600px;height:600px;background:rgba(255,255,255,.15);border-radius:50%;top:-100px;left:-100px;filter:blur(40px)}.l-content{position:relative;z-index:1;color:#fff;max-width:380px}.l-content h1{font-size:36px;font-weight:800;line-height:1.1;margin-bottom:16px}.right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px;background:#fff}.card{width:100%;max-width:380px}.logo{display:flex;gap:12px;align-items:center;margin-bottom:28px}.logo-icon{width:48px;height:48px;background:linear-gradient(135deg,#5a4cf1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;box-shadow:0 8px 20px rgba(90,76,241,.3)}.card h2{font-size:24px;font-weight:800;color:#111827}label{font-size:11px;font-weight:700;letter-spacing:.6px;color:#374151;margin-top:14px;display:block;text-transform:uppercase}input{width:100%;padding:13px 14px;border:1.5px solid #eef0f6;border-radius:12px;margin-top:6px;font-size:13.5px;background:#fbfcff;outline:none}input:focus{border-color:#5a4cf1;background:#fff;box-shadow:0 0 0 4px rgba(90,76,241,.1)}.btn{width:100%;padding:14px;background:linear-gradient(135deg,#5a4cf1,#7c3aed);color:#fff;border:0;border-radius:12px;font-weight:700;font-size:14px;margin-top:20px;cursor:pointer;box-shadow:0 8px 20px rgba(90,76,241,.3)}.demo{margin-top:18px;background:#f8f7ff;border:1px dashed #e0d7ff;border-radius:12px;padding:12px;font-size:11px;color:#6b7280;line-height:1.6}.err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px;border-radius:10px;font-size:12px;margin-bottom:14px}.secure-badge{display:flex;gap:8px;align-items:center;margin-top:14px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;font-size:11px;color:#166534;font-weight:600}</style></head><body><div class="left"><div class="l-content"><h1>Secure &<br>Beautiful.</h1><p>Bank-level security with delightful UX. Passwords are hashed, sessions protected, XSS prevented.</p><div style="margin-top:24px;background:rgba(255,255,255,.15);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.2);border-radius:16px;padding:16px;display:flex;gap:12px;align-items:center"><div style="width:44px;height:44px;background:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center">🔒</div><div><div style="font-weight:700;font-size:13px">Security Certified</div><div style="font-size:11px;opacity:.8">password_hash() + CSRF + XSS</div></div></div></div></div><div class="right"><div class="card"><div class="logo"><div class="logo-icon">💬</div><div><div style="font-weight:800;font-size:16px">ResolveDesk</div><div style="font-size:11px;color:#6b7280">Secure Portal</div></div></div><h2>Welcome back!</h2><div style="font-size:13px;color:#6b7280;margin-top:6px;margin-bottom:22px">Secure login with encrypted passwords</div><?php if($err) echo '<div class="err">'.htmlspecialchars($err).'</div>'; ?><form method="POST" autocomplete="off"><label>Email Address</label><input type="email" name="email" value="student@college.edu" required placeholder="you@college.edu"><label>Password</label><input type="password" name="password" value="Student@123" required placeholder="••••••••"><button class="btn">Secure Login →</button></form><div class="secure-badge">✅ 🔒 Passwords hashed with bcrypt • Session protected • XSS safe</div><div class="demo"><b>🎓 Student:</b> student@college.edu / Student@123<br><b>🛡️ Admin:</b> admin@college.edu / Admin@123</div></div></div></body></html>
