<?php
session_start();

// After successful login
$_SESSION['user_id'] = $row['user_id'];
$_SESSION['username'] = $row['username'];

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Get total income & expenses
$sql = "
SELECT 
  (SELECT IFNULL(SUM(income_amount),0) FROM Income WHERE user_id = ?) AS total_income,
  (SELECT IFNULL(SUM(transaction_amount),0) FROM Transactions WHERE user_id = ?) AS total_expenses
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$stmt->bind_result($income, $expenses);
$stmt->fetch();
$stmt->close();
$conn->close();

// Default values if no data yet
$income = $income ?? 0;
$expenses = $expenses ?? 0;
?>
