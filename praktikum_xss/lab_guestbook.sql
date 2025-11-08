-- schema_guestbook.sql
CREATE DATABASE IF NOT EXISTS lab_guestbook;
USE lab_guestbook;

-- users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE,
  password VARCHAR(255), -- untuk lab: plaintext (tapi di production gunakan password_hash)
  full_name VARCHAR(150),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- posts
DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  body TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- comments
DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  post_id INT,
  comment TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- dummy data
INSERT INTO users (username, password, full_name) VALUES
('alice', 'alicepass', 'Alice Lab'),
('bob', 'bobpass', 'Bob Lab');

INSERT INTO posts (title, body) VALUES
('Contoh Artikel 1', 'Isi artikel 1 untuk demo.'),
('Contoh Artikel 2', 'Isi artikel 2 untuk demo.');

INSERT INTO comments (user_id, post_id, comment) VALUES
(1, 1, 'Halo, komentar dari Alice'),
(2, 1, 'Komentar Bob');
