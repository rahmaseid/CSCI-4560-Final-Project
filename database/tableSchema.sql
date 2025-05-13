-- Users Table: stores personal information
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Income Table: track income sources for financial analysis
CREATE TABLE Income (
    income_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    income_source VARCHAR(100) NOT NULL,
    income_amount DECIMAL(10,2) NOT NULL,
	date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Categories Table: group categories into food, housing, transportation, etc
CREATE TABLE Categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL
);

-- SavingsGoals Table: tracks user's saving goals
CREATE TABLE SavingGoals (
    goal_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    goal_name VARCHAR(100) NOT NULL,
    target_amount DECIMAL(10, 2) NOT NULL,
    current_amount DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Transactions Table: tracks user's spending activity
CREATE TABLE Transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    transaction_amount DECIMAL(10, 2) NOT NULL,
    category_id INT,
    transaction_date DATE NOT NULL,
    transaction_location VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id)
);

-- Bills Table: stores expenses like rent, utilities, etc.
CREATE TABLE Bills (
    bill_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    bill_name VARCHAR(50) NOT NULL,
    bill_amount DECIMAL(10, 2) NOT NULL,
    due_date DATE NOT NULL,
    payment_date DATE,
    payment_status BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Report Table: generates monthly report to let user know
-- about their purchases and budget status
CREATE TABLE Report (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    report_month CHAR(100) NOT NULL,
    total_income DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_transactions DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    budget_status ENUM('under budget', 'on budget', 'over budget') NOT NULL,
    UNIQUE(user_id, report_month),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
