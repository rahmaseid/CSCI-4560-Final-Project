<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$income_id = $_GET['id'] ?? null;

if ($income_id) {
    $stmt = $pdo->prepare("DELETE FROM Income WHERE income_id = ? AND user_id = ?");
    $stmt->execute([$income_id, $user_id]);
}

header("Location: view_income.php");
exit;
