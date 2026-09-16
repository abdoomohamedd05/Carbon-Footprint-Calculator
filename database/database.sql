CREATE DATABASE IF NOT EXISTS carbon_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE carbon_ai;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin','Analyst') DEFAULT 'Analyst',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200) NOT NULL,
    industry VARCHAR(100) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    employees INT DEFAULT 0,
    annual_revenue DECIMAL(15,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS emission_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    scope ENUM('Scope 1','Scope 2','Scope 3') DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    activity VARCHAR(150) DEFAULT NULL,
    quantity DECIMAL(12,2) DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    emission_factor DECIMAL(12,6) DEFAULT NULL,
    total_emission DECIMAL(14,4) DEFAULT NULL,
    reporting_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT DEFAULT NULL,
    report_name VARCHAR(255) DEFAULT NULL,
    report_type VARCHAR(100) DEFAULT NULL,
    report_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    activity TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS emission_factors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) DEFAULT NULL,
    activity VARCHAR(150) DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    emission_factor DECIMAL(12,6) DEFAULT NULL,
    source VARCHAR(150) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS esg_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT DEFAULT NULL,
    environmental_score DECIMAL(5,2) DEFAULT NULL,
    social_score DECIMAL(5,2) DEFAULT NULL,
    governance_score DECIMAL(5,2) DEFAULT NULL,
    total_score DECIMAL(5,2) DEFAULT NULL,
    rating VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ai_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT DEFAULT NULL,
    recommendation TEXT DEFAULT NULL,
    expected_reduction DECIMAL(10,2) DEFAULT NULL,
    estimated_saving DECIMAL(12,2) DEFAULT NULL,
    priority ENUM('High','Medium','Low') DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo admin password: Admin@123
INSERT INTO users (full_name, email, password, role)
VALUES (
    'Admin User',
    'admin@carbon.ai',
    '$2y$10$jsZi6X6LlUlXlfOcAwiR3.G4BrkVeP8uvMSWzM.WFr7IA5tY6vcnO',
    'Admin'
)
ON DUPLICATE KEY UPDATE
    password = VALUES(password),
    role = 'Admin',
    full_name = VALUES(full_name);

INSERT INTO companies (company_name, industry, country, city, employees, annual_revenue)
SELECT * FROM (
    SELECT 'Green Tech Corp' AS company_name, 'Technology' AS industry, 'Jordan' AS country,
           'Amman' AS city, 250 AS employees, 5200000.00 AS annual_revenue
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM companies WHERE company_name = 'Green Tech Corp');

INSERT INTO companies (company_name, industry, country, city, employees, annual_revenue)
SELECT * FROM (
    SELECT 'EcoEnergy Ltd' AS company_name, 'Energy' AS industry, 'UAE' AS country,
           'Dubai' AS city, 480 AS employees, 12500000.00 AS annual_revenue
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM companies WHERE company_name = 'EcoEnergy Ltd');

INSERT INTO emission_records (company_id, scope, category, activity, quantity, unit, emission_factor, total_emission, reporting_date)
SELECT c.id, 'Scope 1', 'Fuel', 'Diesel combustion', 1000, 'liters', 2.68, 2680.0000, CURDATE()
FROM companies c
WHERE c.company_name = 'Green Tech Corp'
AND NOT EXISTS (SELECT 1 FROM emission_records WHERE company_id = c.id)
LIMIT 1;

INSERT INTO emission_records (company_id, scope, category, activity, quantity, unit, emission_factor, total_emission, reporting_date)
SELECT c.id, 'Scope 2', 'Electricity', 'Grid electricity', 50000, 'kWh', 0.45, 22500.0000, CURDATE()
FROM companies c
WHERE c.company_name = 'EcoEnergy Ltd'
AND NOT EXISTS (SELECT 1 FROM emission_records er WHERE er.company_id = c.id AND er.scope = 'Scope 2')
LIMIT 1;

INSERT INTO reports (company_id, report_name, report_type, report_date)
SELECT c.id, 'Q1 ESG Report 2025', 'ESG', CURDATE()
FROM companies c
WHERE c.company_name = 'Green Tech Corp'
AND NOT EXISTS (SELECT 1 FROM reports WHERE company_id = c.id)
LIMIT 1;

INSERT INTO activity_logs (user_id, activity)
SELECT u.id, 'System ready - sample data loaded'
FROM users u
WHERE u.email = 'admin@carbon.ai'
AND NOT EXISTS (
    SELECT 1 FROM activity_logs WHERE activity = 'System ready - sample data loaded'
)
LIMIT 1;

INSERT INTO emission_factors (category, activity, unit, emission_factor, source)
SELECT * FROM (
    SELECT 'Fuel' AS category, 'Diesel' AS activity, 'liters' AS unit, 2.680000 AS emission_factor, 'DEFRA/IPCC demo' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM emission_factors WHERE activity='Diesel' AND unit='liters');

INSERT INTO emission_factors (category, activity, unit, emission_factor, source)
SELECT * FROM (
    SELECT 'Fuel' AS category, 'Petrol' AS activity, 'liters' AS unit, 2.310000 AS emission_factor, 'DEFRA/IPCC demo' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM emission_factors WHERE activity='Petrol' AND unit='liters');

INSERT INTO emission_factors (category, activity, unit, emission_factor, source)
SELECT * FROM (
    SELECT 'Fuel' AS category, 'Natural Gas' AS activity, 'm3' AS unit, 2.020000 AS emission_factor, 'DEFRA/IPCC demo' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM emission_factors WHERE activity='Natural Gas' AND unit='m3');

INSERT INTO emission_factors (category, activity, unit, emission_factor, source)
SELECT * FROM (
    SELECT 'Electricity' AS category, 'Grid Electricity' AS activity, 'kWh' AS unit, 0.450000 AS emission_factor, 'Grid average demo' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM emission_factors WHERE activity='Grid Electricity' AND unit='kWh');

INSERT INTO emission_factors (category, activity, unit, emission_factor, source)
SELECT * FROM (
    SELECT 'Travel' AS category, 'Short-haul Flight' AS activity, 'passenger-km' AS unit, 0.150000 AS emission_factor, 'DEFRA demo' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM emission_factors WHERE activity='Short-haul Flight');

INSERT INTO emission_factors (category, activity, unit, emission_factor, source)
SELECT * FROM (
    SELECT 'Waste' AS category, 'Landfill Waste' AS activity, 'tonnes' AS unit, 467.000000 AS emission_factor, 'DEFRA demo' AS source
) AS tmp WHERE NOT EXISTS (SELECT 1 FROM emission_factors WHERE activity='Landfill Waste');

INSERT INTO esg_scores (company_id, environmental_score, social_score, governance_score, total_score, rating)
SELECT c.id, 82.0, 77.0, 90.0, 83.0, 'B'
FROM companies c
WHERE c.company_name = 'Green Tech Corp'
AND NOT EXISTS (SELECT 1 FROM esg_scores WHERE company_id = c.id)
LIMIT 1;

INSERT INTO esg_scores (company_id, environmental_score, social_score, governance_score, total_score, rating)
SELECT c.id, 71.0, 68.0, 75.0, 71.3, 'B'
FROM companies c
WHERE c.company_name = 'EcoEnergy Ltd'
AND NOT EXISTS (SELECT 1 FROM esg_scores WHERE company_id = c.id)
LIMIT 1;

INSERT INTO ai_recommendations (company_id, recommendation, expected_reduction, estimated_saving, priority)
SELECT c.id, 'Prioritize renewable electricity procurement to cut Scope 2 emissions.', 12.5, 18000.00, 'High'
FROM companies c
WHERE c.company_name = 'EcoEnergy Ltd'
AND NOT EXISTS (SELECT 1 FROM ai_recommendations WHERE company_id = c.id LIMIT 1)
LIMIT 1;

INSERT INTO ai_recommendations (company_id, recommendation, expected_reduction, estimated_saving, priority)
SELECT c.id, 'Optimize fleet routes and transition high-mileage vehicles to hybrid/EV options.', 8.0, 9500.00, 'Medium'
FROM companies c
WHERE c.company_name = 'Green Tech Corp'
AND NOT EXISTS (
    SELECT 1 FROM ai_recommendations
    WHERE company_id = c.id AND recommendation LIKE 'Optimize fleet%'
)
LIMIT 1;
