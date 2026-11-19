USE idor_lab;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    notes VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (username, password) VALUES
('alice', 'alicepass'),
('bob', 'bobpass'),
('admin', 'adminpass');

INSERT INTO invoices (user_id, title, amount, notes) VALUES
(1, 'Alice - Web hosting', 120.00, 'Paid in full'),
(1, 'Alice - Domain renewal', 15.50, 'Due next month'),
(2, 'Bob - Consulting', 800.00, 'Confidential client project'),
(2, 'Bob - Software license', 249.99, 'Internal use only'),
(3, 'Admin - Secret audit', 5000.00, 'FLAG{idor_found_admin_invoice}');