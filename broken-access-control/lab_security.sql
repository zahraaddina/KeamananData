CREATE DATABASE lab_security CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE lab_security;

-- =======================
-- TABEL USERS
-- =======================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat user default
-- Gunakan hash hasil PHP: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, role)
VALUES ('alice', '12345', 'user');

-- =======================
-- TABEL ITEMS_VULN (rentan)
-- =======================
CREATE TABLE items_vuln (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255),
  content TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =======================
-- TABEL ITEMS_SAFE (aman)
-- =======================
CREATE TABLE items_safe (
  id INT AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  token_hash CHAR(64) NOT NULL,
  token_expires_at DATETIME NULL,
  user_id INT NOT NULL,
  title VARCHAR(255),
  content TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
