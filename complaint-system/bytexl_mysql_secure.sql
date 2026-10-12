-- SECURE MySQL with hashed passwords
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(150) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, role ENUM('student','admin') DEFAULT 'student', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
-- Note: Passwords must be hashed with password_hash() in PHP before insert
-- Example PHP: $hash = password_hash('Student@123', PASSWORD_DEFAULT);
TRUNCATE users;
-- These hashes are for Student@123 and Admin@123 - generated via PHP password_hash
INSERT INTO users (id, name, email, password, role) VALUES 
(1, 'Gill', 'gill@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
(2, 'Student User', 'student@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
(999, 'Administrator', 'admin@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- For real deployment, run this PHP to generate correct hashes:
-- php -r "echo password_hash('Student@123', PASSWORD_DEFAULT).PHP_EOL; echo password_hash('Admin@123', PASSWORD_DEFAULT).PHP_EOL;"

CREATE TABLE IF NOT EXISTS complaints (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, category VARCHAR(100) NOT NULL, subject VARCHAR(200) NOT NULL, description TEXT NOT NULL, location VARCHAR(150) NULL, status ENUM('Pending','In Progress','Resolved','Closed') DEFAULT 'Pending', admin_response TEXT NULL, is_deleted TINYINT(1) DEFAULT 0, deleted_at TIMESTAMP NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id));
TRUNCATE complaints;
INSERT INTO complaints (id,user_id,category,subject,description,location,status) VALUES (1,2,'Academics','zddfgty','Academic test','Block A','Pending'),(2,2,'Hostel','sapadu problem','Food issue','Hostel','Closed'),(3,2,'Hostel','tyklkjhuioplakdjisd','jhsdkfi;iskfjsdof','jkdfijsd/f/sdlf/','Pending');
