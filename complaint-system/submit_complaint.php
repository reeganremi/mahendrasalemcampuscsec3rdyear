<?php
session_start();
header("X-Frame-Options: DENY");
if(!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])){ header('Location: login.php'); exit; }
if(time() - $_SESSION['login_time'] > 3600){ session_destroy(); header('Location: login.php'); exit; }
$_SESSION['login_time']=time();
if($_SERVER['REQUEST_METHOD']=='POST'){
  $category = htmlspecialchars(trim($_POST['category']??''), ENT_QUOTES);
  $subject = htmlspecialchars(trim($_POST['subject']??''), ENT_QUOTES);
  $location = htmlspecialchars(trim($_POST['location']??''), ENT_QUOTES);
  $desc = htmlspecialchars(trim($_POST['description']??''), ENT_QUOTES);
  if($subject && $desc){
    $data=json_decode(file_get_contents(__DIR__.'/fake_db.json'),true);
    $newId=count($data['complaints'])+1;
    $data['complaints'][]=['id'=>$newId,'user_id'=>$_SESSION['user_id'],'user_name'=>$_SESSION['user_name'],'category'=>$category,'subject'=>$subject,'location'=>$location,'description'=>$desc,'status'=>'Pending','admin_response'=>'','history'=>[],'created_at'=>date('Y-m-d H:i:s')];
    file_put_contents(__DIR__.'/fake_db.json',json_encode($data,JSON_PRETTY_PRINT));
    header('Location: my_complaints.php?success=1'); exit;
  }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>New Complaint - ResolveDesk</title><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet"><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8f7ff;display:flex;min-height:100vh}.sidebar{width:260px;background:#0a0a1a;color:#8b8da3;padding:16px 12px}.m-item{display:flex;gap:10px;padding:11px 12px;border-radius:12px;color:#8b8da3;text-decoration:none;font-size:13px;margin-bottom:4px}.m-item.active{background:#5a4cf1;color:#fff}.main{flex:1;padding:24px;display:flex;justify-content:center}.card{width:100%;max-width:560px;background:#fff;border-radius:20px;border:1px solid #f0eaff;padding:24px}label{font-size:11px;font-weight:700;letter-spacing:.6px;color:#374151;margin-top:16px;display:block;text-transform:uppercase}input,select,textarea{width:100%;padding:12px 14px;border:1.5px solid #eef0f6;border-radius:12px;margin-top:6px;font-size:13px;background:#fbfcff;outline:none}input:focus,select:focus,textarea:focus{border-color:#5a4cf1;background:#fff;box-shadow:0 0 0 4px rgba(90,76,241,.1)}.btn{width:100%;padding:14px;background:linear-gradient(135deg,#5a4cf1,#7c3aed);color:#fff;border:0;border-radius:12px;font-weight:700;margin-top:20px;cursor:pointer;box-shadow:0 8px 20px rgba(90,76,241,.3)}</style></head><body><div class="sidebar"><div style="padding:8px;color:#fff;font-weight:800">ResolveDesk 🔒</div><a class="m-item" href="dashboard.php">◎ Dashboard</a><a class="m-item" href="my_complaints.php">☰ My Complaints</a><a class="m-item active" href="submit_complaint.php">✦ New Complaint</a></div><div class="main"><div class="card"><div style="font-weight:800;font-size:20px">Submit New Complaint ✨</div><div style="font-size:12px;color:#6b7280;margin-top:4px">All details are encrypted and secure • 🔒</div><form method="POST" style="margin-top:18px"><label>Category *</label><select name="category" required><option>Hostel</option><option>Academics</option><option>Food</option><option>Transport</option><option>Facilities</option></select><label>Subject *</label><input name="subject" required placeholder="e.g. Water issue in Block A"><label>Location</label><input name="location" placeholder="e.g. Block A, Room 203"><label>Description *</label><textarea name="description" required rows="4" placeholder="Describe your issue in detail..."></textarea><button class="btn">Submit Complaint →</button></form><div style="margin-top:14px;text-align:center"><a href="dashboard.php" style="font-size:12px;color:#5a4cf1;text-decoration:none;font-weight:700">← Back to Dashboard</a></div></div></div></body></html>
