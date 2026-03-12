CREATE DATABASE user_auth_system;
USE user_auth_system;
CREATE TABLE users (
    user_id VARCHAR(10) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('customer', 'staff', 'doctor') NOT NULL DEFAULT 'customer',
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customer_details (
    user_id VARCHAR(10) PRIMARY KEY,
    gender ENUM('male', 'female', 'other') NOT NULL,
    dob DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE staff_details (
    user_id VARCHAR(10) PRIMARY KEY,
    accessible_pages TEXT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE doctor_details (
    user_id VARCHAR(10) PRIMARY KEY,
    work_from VARCHAR(50) DEFAULT 'retired',
    medical_type VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
