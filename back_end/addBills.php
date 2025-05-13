<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Connect to database
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized. Please log in.");
}
$user_id = $_SESSION['user_id'];

//Retrieve POST data
$bill_name      = $_POST['bill_name'] ?? '';
$bill_amount    = $_POST['bill_amount'] ?? '';
$due_date       = $_POST['due_date'] ?? '';
$payment_status = $_POST['paid_status'] ?? '';
// Allow payment_date to NULL
$payment_date = empty($_POST['payment_date']) ? null : $_POST['payment_date'];


//Validate input
if (empty($bill_name) || empty($bill_amount) || empty($due_date) || $payment_status === '') {
    die("Missing required fields.");
}

//Convert payment_status to boolean
$payment_status = $payment_status == '1' ? 1 : 0;

//Prepare statement
$stmt = $conn->prepare(
    "INSERT INTO Bills (user_id, bill_name, bill_amount, due_date, payment_date, payment_status)
     VALUES (?, ?, ?, ?, ?, ?)"
);

//Bind and execute
$stmt->bind_param("isdssi", $user_id, $bill_name, $bill_amount, $due_date, $payment_date, $payment_status);

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
        <h4 class='mb-4 text-dark'>Bill added successfully!</h4>
        <div class='d-flex justify-content-center gap-3'>
            <button class='btn btn-success' onclick=\"window.location.href='../front_end/addBills.html'\">Add Another</button>
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
$conn->close();
?>
