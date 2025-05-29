/* 
 * ======================================================
 *                    DATABASE YARATISH              
 * ======================================================
 */

-- 1. Avvalgi versiyani tozalash (agar mavjud bo'lsa)
DROP DATABASE IF EXISTS moda_db;

-- 2. Yangi database yaratish
CREATE DATABASE moda_db;

-- 3. Database aktivlashtirish
USE moda_db;

-- ==================== Users ====================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================== Categories ====================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==================== Products ====================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ==================== Cards ====================
CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ====================
INSERT INTO users (name, username, password, role)
VALUES ('Toxtamurodova Sabrina', 'sabrina', '$2y$10$osogG6WW0sE4U5INkTwDROOOMqSohBkl16FTiW8k4KL0ZS88zaAZe', 'admin');

-- ==================== Categories ga 3 ta ma'lumot ====================
INSERT INTO categories (name, description)
VALUES 
('Kiyim-kechak', 'Erkaklar va ayollar uchun zamonaviy kiyimlar'),
('Poyabzallar', 'Sport, klassik va kundalik poyabzallar'),
('Aksessuarlar', 'Soatlar, sumkalar, ko‘zoynaklar va boshqalar');

-- ==================== Products ga 3 ta mahsulot ====================
INSERT INTO products (category_id, name, description, image, price)
VALUES 
(1, 'Oversize hoodie', 'Issiq va qulay hoodie, turli ranglarda mavjud', 'hoodie.jpg', 249.99),
(2, 'Nike Air Max 270', 'Yengil va qulay sport poyabzali', 'airmax270.jpg', 699.00),
(3, 'Ko‘zoynak Ray-Ban', 'Yozgi kolleksiya uchun ideal tanlov', 'rayban.jpg', 159.50);

-- ==================== Cards ga ma'lumotlar (foydalanuvchi sabrina uchun) ====================
-- sabrina foydalanuvchi idsi 1 deb faraz qilamiz, chunki bu birinchi user bo‘lib kiritilgan
INSERT INTO cards (user_id, product_id, quantity)
VALUES 
(1, 1, 2), -- 2 ta hoodie
(1, 2, 1), -- 1 juft Nike Air Max
(1, 3, 1); -- 1 dona Ray-Ban ko‘zoynak
