<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['transaction_amount']);
    $date = $_POST['traansaction_date'];
    $location = trim($_POST['transaction_location']);
    $category = intval($_POST['category_id']);

    if ($amount <= 0 || empty($date) || empty($location) || !$category_id) {
        die("Please fill in all fields correctly.");
    }

    $stmt = $pdo->prepare("INSERT INTO Transactions (user_id, category_id, transaction_amount, transaction_date, transaction_location) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $category, $amount, $date, $location]);

    header("Location: view_transactions.php");
    exit;
}
