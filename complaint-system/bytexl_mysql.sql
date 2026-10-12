CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), email VARCHAR(150) UNIQUE, password VARCHAR(255), role ENUM('student','admin') DEFAULT 'student', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
TRUNCATE users;
INSERT INTO users VALUES (1,'Gill','gill@college.edu','Student@123','student',NOW()),(2,'Student User','student@college.edu','Student@123','student',NOW()),(999,'Administrator','admin@college.edu','Admin@123','admin',NOW());
CREATE TABLE IF NOT EXISTS complaints (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, category VARCHAR(100), subject VARCHAR(200), description TEXT, location VARCHAR(150), status ENUM('Pending','In Progress','Resolved','Closed') DEFAULT 'Pending', admin_response TEXT NULL, is_deleted TINYINT(1) DEFAULT 0, deleted_at TIMESTAMP NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
TRUNCATE complaints;
INSERT INTO complaints (id,user_id,category,subject,description,location,status) VALUES (1,2,'Academics','zddfgty','Academic test','Block A','Pending'),(2,2,'Hostel','sapadu problem','Food issue','Hostel','Closed'),(3,2,'Hostel','tyklkjhuioplakdjisd','jhsdkfi;iskfjsdof','jkdfijsd/f/sdlf/','Pending');
CREATE TABLE IF NOT EXISTS complaint_history (id INT AUTO_INCREMENT PRIMARY KEY, complaint_id INT, status VARCHAR(50), comment TEXT, created_by VARCHAR(100), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
TRUNCATE complaint_history;
INSERT INTO complaint_history (complaint_id,status,comment,created_by) VALUES (1,'Pending','Submitted','Student'),(2,'Closed','Resolved','Admin'),(3,'Pending','Submitted','Student');
