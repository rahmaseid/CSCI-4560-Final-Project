<?php
session_start();
require_once '../includes/db.php';

if($SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm-password'];

  if (empty($email) || empty($username) || empty($password) || empty($confirm)){
    die("All fields are required.");
  }

  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email.")
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  try{
    $stmt = $pdo->prepare("INSERT INTO users(email, username, password) VALUES (?,?,?)");
    $stmt->execute([$email, $username, $hashedPassword]);

    header("Location: login.html");
    exit;
  }
    catch(PDOException $e) {
      if ($e->getCode() == 23000) {
        die("Email or username already exists");
      }
      die("Signup failed: ". $e->getMessafe())
    }
  
}
>
