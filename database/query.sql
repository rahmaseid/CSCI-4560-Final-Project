show databases;

CREATE DATABASE budgeting_db;

USE budgeting_db;

SHOW TABLES;

SELECT *
FROM users;

SELECT * FROM Income
WHERE user_id = 1;

SELECT * FROM Transactions
WHERE user_id = 1;

SELECT * FROM Bills
WHERE user_id = 1;


SELECT * FROM SavingGoals
WHERE user_id = 1;

SELECT * FROM Report
WHERE user_id = 1;





DROP DATABASE budgeting_db;