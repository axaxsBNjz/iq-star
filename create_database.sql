-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS iraq_star_system;
USE iraq_star_system;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    lastLogin DATETIME NULL,
    ipAddress VARCHAR(45),
    isActive BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المشتركين
CREATE TABLE IF NOT EXISTS subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    service VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    status ENUM('active', 'pending', 'inactive') DEFAULT 'pending',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    notes TEXT,
    INDEX idx_status (status),
    INDEX idx_created (created),
    FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الخدمات
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    minPrice INT,
    maxPrice INT,
    priceRange VARCHAR(100),
    description TEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    isActive BOOLEAN DEFAULT TRUE,
    INDEX idx_category (category),
    FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول سجل النشاطات
CREATE TABLE IF NOT EXISTS activity_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    userId INT,
    userName VARCHAR(100),
    userRole VARCHAR(50),
    type VARCHAR(50),
    action TEXT NOT NULL,
    details JSON,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    browser TEXT,
    page VARCHAR(255),
    INDEX idx_userId (userId),
    INDEX idx_timestamp (timestamp),
    INDEX idx_type (type),
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المستخدمين المتصلين
CREATE TABLE IF NOT EXISTS online_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL UNIQUE,
    name VARCHAR(100),
    role VARCHAR(50),
    loginTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    lastActivity DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_lastActivity (lastActivity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول البيانات المالية
CREATE TABLE IF NOT EXISTS financial_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscriberId INT,
    month VARCHAR(20),
    revenue INT,
    expenses INT,
    profit INT,
    notes TEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriberId) REFERENCES subscribers(id) ON DELETE CASCADE,
    INDEX idx_month (month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الإعدادات
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    settingKey VARCHAR(100) UNIQUE NOT NULL,
    settingValue JSON,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج بيانات افتراضية للمستخدمين
INSERT INTO users (username, password, fullName, email, role, created) VALUES
('admin', '$2y$10$YIjlrPnoJ8tzQo.0iB7H5OPST9/PgBkqquzi.Ee7q7dYVVi/vPl5m', 'الأسـتاذ حسين الزبيدي', 'admin@iraqstar.iq', 'admin', NOW());

-- إدراج بيانات افتراضية للخدمات
INSERT INTO services (name, category, minPrice, maxPrice, priceRange, description, createdBy, isActive) VALUES
('برمجة مواقع', 'تطوير', 200000, 1000000, '200,000 - 1,000,000', 'تصميم وبرمجة مواقع احترافية', 1, TRUE),
('تصميم جرافيك', 'تصميم', 50000, 200000, '50,000 - 200,000', 'تصميم شعارات ومواد دعائية', 1, TRUE),
('استضافة مواقع', 'استضافة', 50000, 500000, '50,000 - 500,000', 'استضافة مواقع مع دعم فني', 1, TRUE),
('تطوير تطبيقات', 'تطوير', 500000, 5000000, '500,000 - 5,000,000', 'تطوير تطبيقات موبايل وسطح مكتب', 1, TRUE);

-- إدراج الإعدادات الافتراضية
INSERT INTO settings (settingKey, settingValue) VALUES
('theme', '"blue"'),
('autoRefresh', 'true'),
('notifications', 'true'),
('logActivities', 'true');
