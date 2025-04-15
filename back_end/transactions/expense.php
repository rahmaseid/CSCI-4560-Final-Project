<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $expenseName = trim($_POST["expense_name"]);
    $expenseAmount = $_POST["expense_amount"];

    $stmt = $pdo->prepare("INSERT INTO Transactions (name, amount) VALUES (?, ?)");


    try {
        if ($stmt->execute([$expenseName, $expenseAmount])) {
            echo "Expense added! <a href='../front_end/expense.html'>Back</a>";
        } else {
            echo "Error adding expense.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
