<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST["expense_amount"]);
    $location = trim($_POST["expense_name"]);
    $category_id = intval($_POST["category_id"]);
    $date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO Transactions (user_id, transaction_amount, category_id, transaction_date, transaction_location) VALUES (?, ?, ?, ?, ?)");

    try {
        if ($stmt->execute([$user_id, $amount, $category_id, $date, $location])) {
            header("Location: ../../front_end/addTransactions.html?success=1");
            exit;
        } else {
            header("Location: ../../front_end/addTransactions.html?error=1");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../../front_end/addTransactions.html?error=" . urlencode($e->getMessage()));
        exit;
    }
}
