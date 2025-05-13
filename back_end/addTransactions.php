<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check session
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

// Connect to database
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $transaction_amount = $_POST['transaction_amount'];
    $category_id = $_POST['category_id'];
    $transaction_date = $_POST['transaction_date'];
    $transaction_location = isset($_POST['transaction_location']) ? trim($_POST['transaction_location']) : '';

    // Prepare and insert
    $stmt = $conn->prepare("INSERT INTO Transactions (user_id, transaction_amount, category_id, transaction_date, transaction_location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idiss", $user_id, $transaction_amount, $category_id, $transaction_date, $transaction_location);

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
            <h4 class='mb-4 text-dark'>Transaction added successfully!</h4>
            <div class='d-flex justify-content-center gap-3'>
                <button class='btn btn-success' onclick=\"window.location.href='../front_end/addTransactions.html'\">Add Another</button>
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

    echo "
        </div>
    </body>
    </html>
    ";

    $stmt->close();
}

$conn->close();
?>
