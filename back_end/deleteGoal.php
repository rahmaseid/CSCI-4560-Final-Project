<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "budgeting_db";

// Database connection
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get goal ID from query parameter
if (isset($_GET['goal_id'])) {
    $goal_id = $_GET['goal_id'];

    // Delete the goal from the database
    $sql = "DELETE FROM SavingGoals WHERE goal_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $goal_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: viewSaving.php"); // Redirect after successful deletion
        exit();
    } else {
        echo "Error deleting goal.";
    }
} else {
    // If no goal_id provided, redirect to viewSaving
    header("Location: viewSaving.php");
    exit();
}

// Close connection
$stmt->close();
$conn->close();
?>
