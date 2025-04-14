<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $source = trim($_POST['income_source']);
    $amount = floatval($_POST['income_amount']);
    $user_id = $_SESSION['user_id'];

    if ($source === '' || $amount <= 0) {
        die("Invalid amount. Please enter a valid income source and amount.");
    }

    $stmt = $pdo->prepare("INSERT INTO INCOME (user_id, income_source, income_amount) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $source, $amount]);

    header("Location: view_income.php");
    exit;
}
