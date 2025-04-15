<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $bill_name = $_POST['bill_name'];
    $bill_amount = $_POST['bill_amount'];
    $due_date = $_POST['due_date'];
    $payment_status = (int)$_POST['paid_status'];
    $payment_date = $payment_status ? ($_POST['payment_date'] ?? date('Y-m-d')) : null;

    // Insert into Bills table
    $stmt = $pdo->prepare("INSERT INTO Bills 
        (user_id, bill_name, bill_amount, due_date, payment_date, payment_status) 
        VALUES (?, ?, ?, ?, ?, ?)");

    try {
        $stmt->execute([$user_id, $bill_name, $bill_amount, $due_date, $payment_date, $payment_status]);
        header("Location: ../../front_end/addBills.html?success=1");
    } catch (PDOException $e) {
        header("Location: ../../front_end/addBills.html?error=" . urlencode($e->getMessage()));
    }
}
