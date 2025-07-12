-- Military Bestiary Database Creation Script
-- This script creates the database and users table for the military bestiary system

-- Create the database
CREATE DATABASE IF NOT EXISTS bestiary;
USE bestiary;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_fName VARCHAR(50) NOT NULL,
    user_lName VARCHAR(50) NOT NULL,
    user_username VARCHAR(50) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    user_desig VARCHAR(100) NOT NULL,
    user_last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create creatures table
CREATE TABLE IF NOT EXISTS creatures (
    crtr_id VARCHAR(10) PRIMARY KEY,
    crtr_name VARCHAR(100) NOT NULL,
    crtr_size VARCHAR(20) NOT NULL,
    crtr_progeny VARCHAR(100) NULL,
    crtr_type VARCHAR(50) NOT NULL,
    crtr_environment VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes for better performance
CREATE INDEX idx_user_email ON users(user_fName, user_lName);
CREATE INDEX idx_user_username ON users(user_username);
CREATE INDEX idx_user_desig ON users(user_desig);
CREATE INDEX idx_user_last_login ON users(user_last_login);

-- Add indexes for creatures table
CREATE INDEX idx_crtr_name ON creatures(crtr_name);
CREATE INDEX idx_crtr_type ON creatures(crtr_type);
CREATE INDEX idx_crtr_environment ON creatures(crtr_environment);

-- Display table structures
DESCRIBE users;
DESCRIBE creatures;

ALTER TABLE creatures
    ADD COLUMN crtr_description TEXT,
  ADD COLUMN crtr_image VARCHAR(255);
