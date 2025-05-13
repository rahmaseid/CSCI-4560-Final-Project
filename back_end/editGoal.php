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

    // Get the goal's data from the database
    $sql = "SELECT goal_name, target_amount, current_amount FROM SavingGoals WHERE goal_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $goal_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // No goal found for this user, redirect to view page
        header("Location: viewSaving.php");
        exit();
    }
    
    $goal = $result->fetch_assoc();
} else {
    // If no goal_id provided, redirect to viewSaving
    header("Location: viewSaving.php");
    exit();
}

// Handle form submission to update the goal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $goal_name = $_POST['goal_name'];
    $target_amount = $_POST['target_amount'];
    $current_amount = $_POST['current_amount'];

    // Update the goal in the database
    $update_sql = "UPDATE SavingGoals SET goal_name = ?, target_amount = ?, current_amount = ? WHERE goal_id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sdiii", $goal_name, $target_amount, $current_amount, $goal_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        header("Location: viewSaving.php"); // Redirect after successful update
        exit();
    } else {
        $error_message = "Error updating goal. Please try again.";
    }
}

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Savings Goal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
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
                    <li class="nav-item"><a class="nav-link" href="home.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Tools</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="addIncome.html">Add Income</a></li>
                            <li><a class="dropdown-item" href="addTransactions.html">Add Transactions</a></li>
                            <li><a class="dropdown-item" href="addBills.html">Add Bills</a></li>
                            <li><a class="dropdown-item" href="saving.html">Savings Goals</a></li>
                            <li><a class="dropdown-item" href="report.html">Reports</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item"><a class="btn btn-logout" href="../back_end/auth/signout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Edit Savings Goal</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form action="editGoal.php?goal_id=<?php echo $goal_id; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label required">Goal Name</label>
                        <input type="text" class="form-control" name="goal_name" value="<?php echo htmlspecialchars($goal['goal_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Target Amount ($)</label>
                        <input type="number" step="0.01" class="form-control" name="target_amount" value="<?php echo htmlspecialchars($goal['target_amount']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Amount ($)</label>
                        <input type="number" step="0.01" class="form-control" name="current_amount" value="<?php echo htmlspecialchars($goal['current_amount']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Goal</button>
                </form>
            </div>
        </div>
        <a href="viewSaving.php" class="btn btn-secondary mt-3">← Back to Savings Goals</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
