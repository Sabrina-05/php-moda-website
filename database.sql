/* ======================================================
 *       🎯 MODA_DB DATABASE - AYOLLAR KIYIMLARI 🎯
 * ====================================================== */

/* 1. 🔄 Eski database’ni o‘chirib tashlash */
DROP DATABASE IF EXISTS moda_db;

/* 2. 🆕 Yangi database yaratish */
CREATE DATABASE moda_db;

/* 3. 🎯 Yaratilgan database’ni tanlash */
USE moda_db;

/* ====================== 👥 USERS TABLE ======================
 * Foydalanuvchilar: admin va oddiy userlar
 */
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/* ====================== 📂 CATEGORIES TABLE ======================
 * Kategoriya: ayollar kiyimlari moddalari uchun kategoriya jadvali 
 */
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* ====================== 🛍️ PRODUCTS TABLE ======================
 * Ayollar kiyimlari mahsulotlari ro'yxati
 * Har bir mahsulot o‘z kategoriya ID bilan bog‘langan
 */
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

/* ====================== 🛒 CARDS TABLE ======================
 * Savatcha jadvali: foydalanuvchi va mahsulot IDlari bilan
 * miqdori ko‘rsatiladi
 */
CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

/* ====================== 🔐 USERS MA'LUMOTLARI ======================
 * 1 admin va 1 oddiy user (bcrypt parol bilan)
 */
INSERT INTO users (name, username, password, role) VALUES
  ('Toxtamurodova Sabrina', 'sabrina', '$2y$10$osogG6WW0sE4U5INkTwDROOOMqSohBkl16FTiW8k4KL0ZS88zaAZe', 'admin'),
  ('User', 'user', '$2y$10$osogG6WW0sE4U5INkTwDROOOMqSohBkl16FTiW8k4KL0ZS88zaAZe', 'user');

/* ====================== 🗂️ KATEGORIYALAR ======================
 * 3 ta kategoriya yaratildi:
 * 1 - Ko‘ylaklar
 * 2 - Shimlar
 * 3 - Yuqori kiyimlar
 */
INSERT INTO categories (name, description) VALUES
  ('Ko‘ylaklar', 'Yozgi, bahorgi va maxsus tadbirlar uchun zamonaviy ko‘ylaklar'),
  ('Shimlar', 'Har kuni kiyish uchun qulay va zamonaviy jinslar, shimlar'),
  ('Yuqori kiyimlar', 'Sviterlar, futbolkalar va boshqa yuqori kiyim turlari');

/* ====================== 🛒 MAHSULOTLAR ======================
 * Har bir mahsulot o‘z kategoriyasiga tegishli
 */
INSERT INTO products (category_id, name, description, image, price) VALUES
  (1, 'Yozgi ko‘ylak', 'Yengil va rang-barang yozgi ko‘ylak', 'summer_dress.jpg', 79.99),
  (1, 'Maxsus tadbir ko‘ylagi', 'Elegant va nafis maxsus tadbir uchun ko‘ylak', 'party_dress.jpg', 149.99),
  (2, 'Jeans shimlari', 'Quyuq ko‘k, zamonaviy ayollar jinslari', 'jeans_pants.jpg', 99.99),
  (2, 'Ofis shimlari', 'Klassik va rasmiy ofis uchun shimlar', 'office_pants.jpg', 89.99),
  (3, 'Trikotaj sviter', 'Yumshoq va iliq trikotaj sviter', 'knit_sweater.jpg', 59.99),
  (3, 'Yozgi futbolka', 'Oddiy va qulay yozgi futbolka', 'summer_tshirt.jpg', 39.99);

/* ====================== 🛍️ SAVATCHA (CARDS) ======================
 * User (id=2) savatchasi:
 * - 1 ta Yozgi ko‘ylak
 * - 2 ta Maxsus tadbir ko‘ylagi
 */
INSERT INTO cards (user_id, product_id, quantity) VALUES
  (2, 1, 1),
  (2, 2, 2);
