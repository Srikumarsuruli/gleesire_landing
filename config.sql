CREATE DATABASE dubai_analytics;
USE dubai_analytics;

CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45),
    city VARCHAR(100),
    page TEXT,
    timestamp INT
);

CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20),
    attraction VARCHAR(100),
    adults INT,
    children INT,
    timestamp INT
);