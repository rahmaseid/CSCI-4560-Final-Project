-- DROP TABLES in reverse order of table creation due to how contraints are figured
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS Income;
DROP TABLE IF EXISTS Categories;
DROP TABLE IF EXISTS Savings_goals;
DROP TABLE IF EXISTS Transactions;
DROP TABLE IF EXISTS Bills;
DROP TABLE IF EXISTS Report;