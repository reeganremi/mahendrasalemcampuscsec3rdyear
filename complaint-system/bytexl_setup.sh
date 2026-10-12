#!/bin/bash
echo "🚀 ByteXL Setup Starting..."
echo "Folder: $(pwd)"

# Create DB
cat > fake_db.json << 'JSON'
{
  "users": [
    {"id": 1, "name": "Gill", "email": "gill@college.edu", "password": "Student@123", "role": "student"},
    {"id": 2, "name": "Student User", "email": "student@college.edu", "password": "Student@123", "role": "student"},
    {"id": 999, "name": "Administrator", "email": "admin@college.edu", "password": "Admin@123", "role": "admin"}
  ],
  "complaints": [
    {"id": 1, "user_id": 2, "user_name": "Student User", "category": "Academics", "subject": "zddfgty", "location": "Block A", "description": "Academic test", "status": "Pending", "admin_response": "", "history": [], "created_at": "2026-10-09 22:36:00"},
    {"id": 2, "user_id": 2, "user_name": "Student User", "category": "Hostel", "subject": "sapadu problem", "location": "Hostel Block", "description": "Food issue", "status": "Closed", "admin_response": "Fixed", "history": [], "created_at": "2026-10-09 22:37:00"},
    {"id": 3, "user_id": 2, "user_name": "Student User", "category": "Hostel", "subject": "tyklkjhuioplakdjisd", "location": "jkdfijsd/f/sdlf/", "description": "jhsdkfi;iskfjsdof", "status": "Pending", "admin_response": "", "history": [], "created_at": "2026-10-09 22:38:00"}
  ]
}
JSON

mkdir -p config
echo "<?php ?>" > config/database.php
echo "<?php header('Location: login.php'); ?>" > index.php
echo "<?php session_start(); session_destroy(); header('Location: login.php'); ?>" > logout.php

# MySQL SQL for your db_455fprc7h
cat > bytexl_mysql.sql << 'SQL'
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), email VARCHAR(150) UNIQUE, password VARCHAR(255), role ENUM('student','admin') DEFAULT 'student', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
TRUNCATE users;
INSERT INTO users VALUES (1,'Gill','gill@college.edu','Student@123','student',NOW()),(2,'Student User','student@college.edu','Student@123','student',NOW()),(999,'Administrator','admin@college.edu','Admin@123','admin',NOW());
CREATE TABLE IF NOT EXISTS complaints (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, category VARCHAR(100), subject VARCHAR(200), description TEXT, location VARCHAR(150), status ENUM('Pending','In Progress','Resolved','Closed') DEFAULT 'Pending', admin_response TEXT NULL, is_deleted TINYINT(1) DEFAULT 0, deleted_at TIMESTAMP NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
TRUNCATE complaints;
INSERT INTO complaints (id,user_id,category,subject,description,location,status) VALUES (1,2,'Academics','zddfgty','Academic test','Block A','Pending'),(2,2,'Hostel','sapadu problem','Food issue','Hostel','Closed'),(3,2,'Hostel','tyklkjhuioplakdjisd','jhsdkfi;iskfjsdof','jkdfijsd/f/sdlf/','Pending');
CREATE TABLE IF NOT EXISTS complaint_history (id INT AUTO_INCREMENT PRIMARY KEY, complaint_id INT, status VARCHAR(50), comment TEXT, created_by VARCHAR(100), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
TRUNCATE complaint_history;
INSERT INTO complaint_history (complaint_id,status,comment,created_by) VALUES (1,'Pending','Submitted','Student'),(2,'Closed','Resolved','Admin'),(3,'Pending','Submitted','Student');
SQL

pkill -9 php 2>/dev/null; sleep 1
nohup php -S 0.0.0.0:8080 -t . > /tmp/bytexl.log 2>&1 &
sleep 2
echo "✅ SETUP DONE!"
echo "Student: student@college.edu / Student@123"
echo "Admin: admin@college.edu / Admin@123"
echo "URL: http://localhost:8080/login.php"
ls -lh *.php *.json *.sql 2>/dev/null | head -20
