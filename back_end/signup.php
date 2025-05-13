<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "mysql";
$username = "root";
$password = "root";
$dbname = "budgeting_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $checkEmail = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        die("This email is already registered. <a href='../front_end/signup.html'>Try again</a>");
    }
    $checkEmail->close();

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

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
            <h4 class='mb-4 text-dark'>Registration Successful!</h4>
            <div class='d-flex justify-content-center gap-3'>
                <button class='btn btn-success' onclick=\"window.location.href='../front_end/signin.html'\">Click to Sign In</button>
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
}
?>
