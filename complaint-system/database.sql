-- =============================================================
-- College Complaint Management System
-- Database: college_complaints
-- Import via phpMyAdmin (http://localhost/phpmyadmin) or mysql CLI.
-- =============================================================

CREATE DATABASE IF NOT EXISTS `college_complaints`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `college_complaints`;

-- -------------------------------------------------------------
-- Table: users
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL COMMENT 'bcrypt hash via password_hash() — never plain text',
  `role`       ENUM('student','admin') NOT NULL DEFAULT 'student',
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = deactivated by admin, cannot log in',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table: complaints
-- Soft-delete via is_deleted (never hard-delete from UI except admin purge).
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaints` (
  `id`             INT NOT NULL AUTO_INCREMENT,
  `user_id`        INT NOT NULL COMMENT 'student who filed the complaint',
  `category`       VARCHAR(100) NOT NULL,
  `subject`        VARCHAR(200) NOT NULL,
  `description`    TEXT NOT NULL,
  `location`       VARCHAR(150) NULL COMMENT 'optional: block / room / department',
  `status`         ENUM('Pending','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Pending',
  `admin_response` TEXT NULL,
  `is_deleted`     TINYINT(1) NOT NULL DEFAULT 0,
  `deleted_at`     TIMESTAMP NULL DEFAULT NULL,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_complaints_user` (`user_id`),
  KEY `idx_complaints_status` (`status`),
  KEY `idx_complaints_category` (`category`),
  KEY `idx_complaints_created` (`created_at`),
  CONSTRAINT `fk_complaints_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table: complaint_history (audit trail of every status change)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaint_history` (
  `id`           INT NOT NULL AUTO_INCREMENT,
  `complaint_id` INT NOT NULL,
  `admin_id`     INT NULL COMMENT 'admin who made the change',
  `old_status`   VARCHAR(30) NULL,
  `new_status`   VARCHAR(30) NOT NULL,
  `comment`      TEXT NULL,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_history_complaint` (`complaint_id`),
  KEY `idx_history_admin` (`admin_id`),
  CONSTRAINT `fk_history_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_admin`
    FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Seed: initial administrator (local use only).
-- Email: admin@college.edu | Password: Admin@123
-- Hash below is bcrypt for "Admin@123" (compatible with password_verify).
-- CHANGE the password immediately after first login, then DELETE seed file.
-- To create more admins safely, use admin_seed.php (CLI) — never a public form.
-- =============================================================
INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES (
  'Administrator',
  'admin@college.edu',
  '$2b$12$sAkv/Am5KWAQAEN9rfS5ye4BrzORI5VbExZ.ruQg3gnwjZ09nalOO',
  'admin'
)
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- Optional demo student (password: Student@123) — delete before production/demo if desired.
-- Hash is bcrypt for "Student@123".
INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES (
  'Demo Student',
  'student@college.edu',
  '$2b$12$11PX51HAYOJw36t9ySTRQ.tQklql0YPcKadW3iQjm56avhTAY4MGC',
  'student'
)
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);
