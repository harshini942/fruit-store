-- Create Database
CREATE DATABASE IF NOT EXISTS fruit_store_db;
USE fruit_store_db;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(15),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    expiry_date DATE,
    min_stock_level INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Suppliers Table
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers Table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Invoices Table
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT,
    invoice_date DATE NOT NULL,
    subtotal DECIMAL(10,2) DEFAULT 0,
    tax DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Invoice Items Table
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Stock Alerts Table
CREATE TABLE stock_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    alert_type ENUM('low_stock', 'expiry') NOT NULL,
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Sample Data
INSERT INTO users (username, email, password, full_name, phone, role) VALUES
('admin', 'admin@fruitstore.com', MD5('admin123'), 'Administrator', '0771234567', 'admin'),
('staff1', 'staff@fruitstore.com', MD5('staff123'), 'John Doe', '0712345678', 'staff');

INSERT INTO categories (name, description) VALUES
('Fruits', 'Fresh fruits from local farms'),
('Vegetables', 'Organic vegetables'),
('Juice Items', 'Fresh juices and smoothies'),
('Dried Fruits', 'Premium dried fruits');

INSERT INTO products (name, category_id, price, stock, expiry_date, min_stock_level) VALUES
('Apple', 1, 100.00, 20, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 10),
('Banana', 1, 80.00, 15, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 8),
('Mango', 1, 120.00, 25, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 10),
('Orange', 1, 30.00, 50, DATE_ADD(CURDATE(), INTERVAL 12 DAY), 20),
('Carrot', 2, 90.00, 30, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 15),
('Spinach', 2, 60.00, 20, DATE_ADD(CURDATE(), INTERVAL 4 DAY), 10),
('Orange Juice', 3, 150.00, 40, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 15),
('Apple Juice', 3, 160.00, 35, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 15),
('Dried Apples', 4, 250.00, 20, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 10),
('Dried Mango', 4, 280.00, 15, DATE_ADD(CURDATE(), INTERVAL 45 DAY), 8);

INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES
('Ashif Traders', 'Mohamed Ashif', '0771234567', 'ashif@gmail.com', 'Martiet Complex, Peradeniya, Kandy'),
('Kandy Fruits', 'Ravi Perera', '0712345678', 'kandytrucks@gmail.com', 'Kandy City Center, Kandy'),
('Peradeniya Suppliers', 'Saman Kumara', '0769876543', 'peradeniya@gmail.com', 'Peradeniya, Kandy');

INSERT INTO customers (name, phone, email, address) VALUES
('Nimal Perera', '0712345678', 'kamil@gmail.com', 'Kandy, Sri Lanka'),
('Kamal Silva', '0755555555', 'kamal@gmail.com', 'Peradeniya, Kandy'),
('Sunil Bandara', '0777777777', 'sunil@gmail.com', 'Katugastota, Kandy');

-- Trigger for Stock Alert
DELIMITER //
CREATE TRIGGER check_low_stock
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.stock <= NEW.min_stock_level AND NEW.stock > 0 THEN
        INSERT INTO stock_alerts (product_id, alert_type, message)
        VALUES (NEW.id, 'low_stock', CONCAT('Product ', NEW.name, ' is low on stock. Only ', NEW.stock, ' items left.'));
    END IF;
    
    IF NEW.stock <= 0 THEN
        INSERT INTO stock_alerts (product_id, alert_type, message)
        VALUES (NEW.id, 'low_stock', CONCAT('Product ', NEW.name, ' is out of stock!'));
    END IF;
END//
DELIMITER ;