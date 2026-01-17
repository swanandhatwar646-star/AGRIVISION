CREATE DATABASE IF NOT EXISTS agrivision_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE agrivision_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    language VARCHAR(20) DEFAULT 'en',
    address TEXT,
    state VARCHAR(50),
    district VARCHAR(50),
    region VARCHAR(50),
    role ENUM('farmer', 'admin', 'analyst') DEFAULT 'farmer',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    area DECIMAL(10, 2),
    soil_type VARCHAR(50),
    crop_type VARCHAR(100),
    planting_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS field_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_id INT NOT NULL,
    zone_name VARCHAR(50) NOT NULL,
    healthy_percentage DECIMAL(5, 2) DEFAULT 0,
    stressed_percentage DECIMAL(5, 2) DEFAULT 0,
    deficient_percentage DECIMAL(5, 2) DEFAULT 0,
    water_requirement DECIMAL(5, 2) DEFAULT 0,
    pest_risk ENUM('low', 'medium', 'high') DEFAULT 'low',
    nutrition_requirement DECIMAL(5, 2) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS crop_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_id INT NOT NULL,
    analysis_date DATE NOT NULL,
    water_risk_high DECIMAL(5, 2) DEFAULT 0,
    water_risk_medium DECIMAL(5, 2) DEFAULT 0,
    pest_risk_high DECIMAL(5, 2) DEFAULT 0,
    pest_risk_medium DECIMAL(5, 2) DEFAULT 0,
    nutrition_risk_high DECIMAL(5, 2) DEFAULT 0,
    nutrition_risk_medium DECIMAL(5, 2) DEFAULT 0,
    FOREIGN KEY (zone_id) REFERENCES field_zones(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS farm_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    field_id INT,
    report_type VARCHAR(50),
    file_path VARCHAR(255),
    description TEXT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    location VARCHAR(100),
    temperature DECIMAL(5, 2),
    rainfall DECIMAL(5, 2),
    humidity DECIMAL(5, 2),
    wind_speed DECIMAL(5, 2),
    recorded_date DATE NOT NULL,
    recorded_time TIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS government_schemes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    eligibility TEXT,
    benefits TEXT,
    application_link VARCHAR(255),
    last_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ai_queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    query TEXT NOT NULL,
    response TEXT,
    query_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS crops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sunlight_requirement VARCHAR(50),
    water_requirement VARCHAR(50),
    soil_type VARCHAR(50),
    growing_season VARCHAR(50),
    description TEXT
);

CREATE TABLE IF NOT EXISTS pesticides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    manufacturer VARCHAR(100),
    `usage` TEXT,
    target_crops TEXT,
    safety_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(100),
    state VARCHAR(50),
    district VARCHAR(50),
    region VARCHAR(50),
    contact_number VARCHAR(15),
    email VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    price DECIMAL(10, 2),
    stock_quantity INT DEFAULT 0,
    image_path VARCHAR(255),
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS greenhouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    temperature DECIMAL(5, 2),
    humidity DECIMAL(5, 2),
    soil_moisture DECIMAL(5, 2),
    irrigation_system ENUM('on', 'off') DEFAULT 'off',
    ventilation_system ENUM('on', 'off') DEFAULT 'off',
    cooling_system ENUM('on', 'off') DEFAULT 'off',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS greenhouse_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    greenhouse_id INT NOT NULL,
    alert_type VARCHAR(50) NOT NULL,
    message TEXT,
    severity ENUM('low', 'medium', 'high') DEFAULT 'medium',
    is_resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (greenhouse_id) REFERENCES greenhouses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    appointment_date DATE NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    reason TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO government_schemes (title, description, eligibility, benefits, last_date) VALUES
('Pradhan Mantri Fasal Bima Yojana (PMFBY)', 'Comprehensive crop insurance scheme providing coverage against crop failure due to natural calamities, pests & diseases.', 'All farmers growing notified crops in notified areas.', 'Premium subsidy up to 50%, coverage for pre-sowing to post-harvest losses.', '2024-12-31'),
('PM-Kisan Samman Nidhi', 'Direct income support of Rs. 6000 per year to eligible farmer families.', 'Small and marginal farmers with cultivable land up to 2 hectares.', 'Rs. 6000 per year in three equal installments.', '2024-12-31'),
('Soil Health Card Scheme', 'Provides soil health cards to farmers to check nutrient status and get recommendations.', 'All farmers can apply.', 'Free soil testing, fertilizer recommendations, improved crop yields.', '2024-12-31'),
('National Agriculture Market (e-NAM)', 'Online trading platform for agricultural commodities to ensure better price realization.', 'All farmers, traders, and buyers.', 'Better prices, transparent trading, wider market access.', '2024-12-31');

INSERT INTO crops (name, sunlight_requirement, water_requirement, soil_type, growing_season, description) VALUES
('Wheat', 'Full Sun', 'Medium', 'Loamy', 'Winter', 'Major cereal crop, rich in carbohydrates and protein.'),
('Rice', 'Full Sun', 'High', 'Clay/Loamy', 'Monsoon', 'Staple food crop, requires abundant water.'),
('Cotton', 'Full Sun', 'Medium', 'Black Soil', 'Summer', 'Fiber crop, important for textile industry.'),
('Sugarcane', 'Full Sun', 'High', 'Well-drained', 'Year-round', 'Cash crop, used for sugar and jaggery production.'),
('Maize', 'Full Sun', 'Medium', 'Loamy/Sandy', 'Monsoon', 'Versatile crop used for food, feed, and industrial purposes.'),
('Potato', 'Partial Sun', 'Medium', 'Sandy Loam', 'Winter', 'Tuber crop, rich in carbohydrates and vitamins.'),
('Tomato', 'Full Sun', 'Medium', 'Well-drained', 'Year-round', 'Vegetable crop, high in vitamins and antioxidants.'),
('Onion', 'Full Sun', 'Medium', 'Sandy Loam', 'Winter', 'Bulb crop, essential ingredient in cooking.');

INSERT INTO pesticides (name, company, manufacturer, `usage`, target_crops, safety_info) VALUES
('Neem Oil', 'Various', 'Organic', 'Natural pesticide for pest control', 'All crops', 'Safe, organic, no residue'),
('Imidacloprid', 'Bayer', 'Bayer CropScience', 'Systemic insecticide for sucking pests', 'Cotton, Rice, Vegetables', 'Use protective equipment, follow dosage'),
('Mancozeb', 'UPL', 'UPL Limited', 'Fungicide for fungal diseases', 'Potato, Tomato, Grapes', 'Avoid inhalation, use mask'),
('Glyphosate', 'Bayer', 'Bayer CropScience', 'Herbicide for weed control', 'All crops', 'Pre-harvest interval: 7 days'),
('Chlorpyrifos', 'Dow', 'Corteva Agriscience', 'Insecticide for soil and foliar pests', 'Cotton, Vegetables', 'Toxic, use with caution'),
('Carbendazim', 'BASF', 'BASF India', 'Fungicide for various fungal diseases', 'Cereals, Fruits, Vegetables', 'Avoid skin contact'),
('Spinosad', 'Dow', 'Corteva Agriscience', 'Insecticide for caterpillars and thrips', 'Vegetables, Fruits', 'Low toxicity to mammals'),
('Thiamethoxam', 'Syngenta', 'Syngenta India', 'Systemic insecticide', 'Cotton, Rice, Vegetables', 'Use recommended dosage');
