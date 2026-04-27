-- Nexus Gear Database Schema
CREATE DATABASE IF NOT EXISTS nexus_gear CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexus_gear;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT 'default.png',
    role ENUM('customer','admin') DEFAULT 'customer',
    address_line TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    zip_code VARCHAR(20),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(100),
    description TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Brands Table
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    logo VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    category_id INT,
    brand_id INT,
    is_featured TINYINT(1) DEFAULT 0,
    is_best_seller TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    total_sold INT DEFAULT 0,
    rating_avg DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    specifications JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);

-- Product Images
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist Table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart Table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Discount Codes
CREATE TABLE discount_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('percentage','fixed') DEFAULT 'percentage',
    value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0,
    max_uses INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    expires_at DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    discount_code_id INT,
    payment_method ENUM('cod','gcash','credit_card') NOT NULL,
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    order_status ENUM('pending','processing','approved','shipped','delivered','cancelled') DEFAULT 'pending',
    notes TEXT,
    shipping_name VARCHAR(100),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_province VARCHAR(100),
    shipping_zip VARCHAR(20),
    gcash_ref VARCHAR(100),
    card_last4 VARCHAR(4),
    approved_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_code_id) REFERENCES discount_codes(id) ON DELETE SET NULL
);

-- Order Items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    body TEXT,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===== SEED DATA =====

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role, phone) VALUES
('Nexus Admin', 'admin@nexusgear.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '09000000000');

-- Categories
INSERT INTO categories (name, slug, icon, sort_order) VALUES
('Smartphones', 'smartphones', 'smartphone.png', 1),
('Laptops', 'laptops', 'laptop.png', 2),
('Desktop PCs', 'desktop-pcs', 'desktop.png', 3),
('PC Parts', 'pc-parts', 'pcparts.png', 4),
('Tablets', 'tablets', 'tablet.png', 5),
('Gaming Peripherals', 'gaming-peripherals', 'gaming.png', 6),
('Audio', 'audio', 'audio.png', 7),
('Cameras', 'cameras', 'camera.png', 8),
('Wearables', 'wearables', 'wearable.png', 9),
('Networking', 'networking', 'network.png', 10);

-- Brands
INSERT INTO brands (name, slug, logo) VALUES
('Apple', 'apple', 'apple.png'),
('Samsung', 'samsung', 'samsung.png'),
('ASUS', 'asus', 'asus.png'),
('Lenovo', 'lenovo', 'lenovo.png'),
('MSI', 'msi', 'msi.png'),
('Logitech', 'logitech', 'logitech.png'),
('Sony', 'sony', 'sony.png'),
('Xiaomi', 'xiaomi', 'xiaomi.png'),
('Acer', 'acer', 'acer.png'),
('Razer', 'razer', 'razer.png'),
('Intel', 'intel', 'intel.png'),
('NVIDIA', 'nvidia', 'nvidia.png');

-- Discount Codes
INSERT INTO discount_codes (code, type, value, min_order, max_uses) VALUES
('NEXUS10', 'percentage', 10, 500, 100),
('NEXUS20', 'percentage', 20, 2000, 50),
('SAVE500', 'fixed', 500, 5000, 30),
('WELCOME', 'percentage', 15, 0, 200);

-- Sample Products
INSERT INTO products (name, slug, description, short_description, price, original_price, stock, category_id, brand_id, is_featured, is_best_seller, rating_avg, rating_count, total_sold) VALUES
('iPhone 15 Pro Max 256GB Natural Titanium', 'iphone-15-pro-max-256gb', 'Experience the pinnacle of smartphone technology with the iPhone 15 Pro Max. Featuring the powerful A17 Pro chip, a stunning Super Retina XDR display, and a revolutionary camera system.', 'Apple A17 Pro, 6.7" Super Retina XDR, 48MP ProRes Camera', 79990.00, 84990.00, 45, 1, 1, 1, 1, 4.8, 234, 189),
('Samsung Galaxy S24 Ultra 512GB', 'samsung-galaxy-s24-ultra', 'The Galaxy S24 Ultra redefines what a smartphone can do. Featuring the integrated S Pen, AI-powered camera, and Galaxy AI features.', 'Snapdragon 8 Gen 3, 6.8" Dynamic AMOLED, 200MP Camera + S Pen', 74990.00, 79990.00, 32, 1, 2, 1, 1, 4.7, 198, 156),
('ASUS ROG Zephyrus G14 2024', 'asus-rog-zephyrus-g14-2024', 'The ultimate gaming laptop. AMD Ryzen 9, RTX 4070, and a stunning OLED display in an ultra-portable form factor.', 'Ryzen 9 8945HS, RTX 4070, 14" 2.8K OLED 120Hz, 32GB RAM', 109990.00, 119990.00, 18, 2, 3, 1, 1, 4.9, 87, 63),
('MacBook Pro 16" M3 Pro', 'macbook-pro-16-m3-pro', 'Supercharged by M3 Pro chip, the MacBook Pro 16 delivers exceptional performance for creative professionals.', 'Apple M3 Pro, 16" Liquid Retina XDR, 18GB Unified Memory, 512GB SSD', 139990.00, 144990.00, 22, 2, 1, 1, 0, 4.9, 112, 78),
('Logitech G Pro X Superlight 2', 'logitech-g-pro-x-superlight-2', 'The lightest wireless gaming mouse ever built for pro gamers. HERO 2 sensor, 95-hour battery life.', 'HERO 2 Sensor, 25600 DPI, <60g Weight, 95hr Battery', 7990.00, 8990.00, 89, 6, 6, 0, 1, 4.8, 445, 389),
('Razer BlackShark V2 Pro 2023', 'razer-blackshark-v2-pro-2023', 'Professional wireless gaming headset with THX Spatial Audio and Razer HyperSpeed wireless technology.', 'THX Spatial Audio, HyperSpeed Wireless, 70hr Battery', 11990.00, 12990.00, 54, 6, 10, 0, 1, 4.7, 321, 267),
('NVIDIA RTX 4080 Super 16GB', 'nvidia-rtx-4080-super-16gb', 'Dominate every game with the RTX 4080 Super. Ada Lovelace architecture, DLSS 3.5, and ray tracing excellence.', 'Ada Lovelace, 16GB GDDR6X, DLSS 3.5, Ray Tracing', 64990.00, 69990.00, 12, 4, 12, 1, 0, 4.8, 67, 43),
('Samsung 49" Odyssey G9 OLED', 'samsung-odyssey-g9-oled-49', 'The ultimate ultra-wide gaming monitor. Dual QHD OLED panel with 240Hz refresh rate and 0.03ms response time.', '49" Dual QHD OLED, 240Hz, 0.03ms, HDR2000', 89990.00, 94990.00, 8, 6, 2, 1, 0, 4.8, 43, 28),
('Xiaomi 14 Ultra 512GB', 'xiaomi-14-ultra-512gb', 'Photography powerhouse with Leica Summilux lenses and Snapdragon 8 Gen 3 performance.', 'SD 8 Gen 3, 50MP Leica Quad Camera, 6.73" LTPO AMOLED', 54990.00, 59990.00, 28, 1, 8, 0, 1, 4.6, 156, 98),
('ASUS ROG Maximus Z790 Hero', 'asus-rog-maximus-z790-hero', 'Premium Intel Z790 motherboard designed for extreme overclocking and maximum performance.', 'Intel Z790, DDR5, PCIe 5.0, WiFi 6E, Thunderbolt 4', 34990.00, 37990.00, 15, 4, 3, 0, 0, 4.7, 89, 52),
('Sony WH-1000XM5', 'sony-wh-1000xm5', 'Industry-leading noise canceling with Auto NC Optimizer, crystal clear hands-free calling, and up to 30-hour battery.', '30hr Battery, LDAC Hi-Res, Multipoint Connection, ANC', 18990.00, 19990.00, 67, 7, 7, 0, 1, 4.8, 512, 423),
('Apple iPad Pro 12.9" M4', 'apple-ipad-pro-129-m4', 'Impossibly thin. Impossibly powerful. The new iPad Pro with M4 chip and Ultra Retina XDR display.', 'Apple M4, 12.9" Ultra Retina XDR OLED, 256GB, WiFi + Cellular', 84990.00, 89990.00, 19, 5, 1, 1, 0, 4.9, 134, 89);

-- Product Images (using placeholder references)
INSERT INTO product_images (product_id, image_path, is_primary, sort_order) VALUES
(1, 'iphone15promax_1.jpg', 1, 1), (1, 'iphone15promax_2.jpg', 0, 2),
(2, 'galaxys24ultra_1.jpg', 1, 1), (2, 'galaxys24ultra_2.jpg', 0, 2),
(3, 'rogzephyrus_1.jpg', 1, 1), (3, 'rogzephyrus_2.jpg', 0, 2),
(4, 'macbookpro_1.jpg', 1, 1),
(5, 'gprox_1.jpg', 1, 1),
(6, 'blacksharkv2_1.jpg', 1, 1),
(7, 'rtx4080s_1.jpg', 1, 1),
(8, 'odysseyg9_1.jpg', 1, 1),
(9, 'xiaomi14ultra_1.jpg', 1, 1),
(10, 'rogmaximus_1.jpg', 1, 1),
(11, 'sonywh1000xm5_1.jpg', 1, 1),
(12, 'ipadprom4_1.jpg', 1, 1);
