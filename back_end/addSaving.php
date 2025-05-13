<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Simulated user_id (replace with session-based ID in production)
$user_id = 1;

// Get POST data
$goal_name      = $_POST['goal_name'] ?? '';
$target_amount  = $_POST['target_amount'] ?? '';
$current_amount = $_POST['current_amount'] ?? 0;

// Validate input
if (empty($goal_name) || empty($target_amount)) {
    die("Missing required fields.");
}

// Insert into DB
$stmt = $conn->prepare(
    "INSERT INTO SavingGoals (user_id, goal_name, target_amount, current_amount)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("isdd", $user_id, $goal_name, $target_amount, $current_amount);

// Styling message
echo "
<html>
<head>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .center-screen {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class='center-screen'>
";
if ($stmt->execute()) {
    echo "
    <div class='alert alert-success text-center p-5 shadow rounded'>
        <h4 class='mb-4 text-dark'>Saving goal added successfully!</h4>
        <div class='d-flex justify-content-center gap-3'>
            <button class='btn btn-success' onclick=\"window.location.href='../front_end/saving.html'\">Add Another</button>
            <button class='btn btn-success' onclick=\"window.location.href='../front_end/home.html'\">Go Back to Dashboard</button>
        </div>
    </div>
";
} else {
    echo "
        <div class='alert alert-danger text-center p-5 shadow rounded'>
            <h4 class='mb-3 text-dark'>Error occurred:</h4>
            <p class='text-dark'>" . htmlspecialchars($stmt->error) . "</p>
        </div>
    ";
}

$stmt->close();
$conn->close();
?>
