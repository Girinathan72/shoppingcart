CREATE DATABASE IF NOT EXISTS fresh_mart;
USE fresh_mart;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Categories data
INSERT INTO categories (name, description) VALUES
('Grains & Rice', 'Rice, wheat flour, and other grains'),
('Oils & Ghee', 'Cooking oils and ghee'),
('Sweeteners', 'Sugar, honey, and other sweeteners'),
('Spices & Salt', 'Salt and cooking spices'),
('Dairy', 'Milk, butter, cheese, and dairy products');

-- Products data
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
(1, 'Basmati Rice', 'Premium quality basmati rice', 3.99, 100, 'images/rice.jpeg'),
(1, 'Wheat Flour', 'Fresh whole wheat atta', 2.49, 80, 'images/wheat.jpeg'),
(2, 'Cooking Oil', 'Healthy refined cooking oil', 4.99, 60, 'images/oil.jpeg'),
(3, 'Sugar', 'Pure white sugar', 1.99, 120, 'images/sugar.jpeg'),
(4, 'Salt', 'Iodized table salt', 0.99, 200, 'images/salt.jpeg'),
(3, 'Honey', 'Natural pure honey', 6.99, 40, 'images/honey.jpeg');
