<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Budgeting App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styling/style.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand">Budgeting App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-start" id="navbarNavDropdown">
                <ul class="navbar-nav gap-4 ms-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html">About Us</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Tools</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="income.html">Add Income</a></li>
                            <li><a class="dropdown-item" href="addTransactions.html">Add Transactions</a></li>
                            <li><a class="dropdown-item" href="saving.html">Savings Goals</a></li>
                            <li><a class="dropdown-item" href="report.html">Reports</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Logout button -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item">
                        <a class="btn btn-outline-danger" href="auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="container mt-5">
        <div class="text-center">
            <h2>Welcome back, <?= $username ?>!</h2>
            <p class="lead">You're logged in. Ready to manage your budget?</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>