<?php
session_start(); if(!isset($_SESSION['user_id'])){ header('Location: login.php'); exit; }
if($_SERVER['REQUEST_METHOD']=='POST'){
 $data=json_decode(file_get_contents(__DIR__.'/fake_db.json'),true);
 $newId=count($data['complaints'])+1;
 $data['complaints'][]=['id'=>$newId,'user_id'=>$_SESSION['user_id'],'user_name'=>$_SESSION['user_name'],'category'=>$_POST['category'],'subject'=>$_POST['subject'],'location'=>$_POST['location'],'description'=>$_POST['description'],'status'=>'Pending','admin_response'=>'','history'=>[],'created_at'=>date('Y-m-d H:i:s')];
 file_put_contents(__DIR__.'/fake_db.json',json_encode($data,JSON_PRETTY_PRINT));
 header('Location: my_complaints.php'); exit;
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Submit Complaint</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"><style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:Inter,sans-serif;background:#f1f4f9;padding:20px}.card{max-width:500px;margin:20px auto;background:#fff;border-radius:14px;padding:20px;border:1px solid #eef0f4}input,select,textarea{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:10px;margin-top:6px}label{font-size:12px;font-weight:600;margin-top:12px;display:block}.btn{width:100%;padding:12px;background:#5a4cf1;color:#fff;border:0;border-radius:10px;font-weight:600;margin-top:16px}</style></head><body><div class="card"><h3>Submit New Complaint</h3><form method="POST"><label>Category</label><select name="category" required><option>Hostel</option><option>Academics</option><option>Food</option><option>Transport</option></select><label>Subject</label><input name="subject" required><label>Location</label><input name="location"><label>Description</label><textarea name="description" required rows="4"></textarea><button class="btn">Submit Complaint</button></form><div style="margin-top:10px"><a href="dashboard.php" style="font-size:12px;color:#4f46e5;text-decoration:none">← Back to Dashboard</a></div></div></body></html>
