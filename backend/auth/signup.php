<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($username) || empty($password)) {
        die("Please fill in all fields.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO USERS (email, username, password) VALUES (?, ?, ?)");
        $stmt->execute([$email, $username, $hashedPassword]);

        header("Location: ../frontend/signin.html");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Email or username already exists");
        }
        die("Signup failed: " . $e->getMessage());
    }
}
