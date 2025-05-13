<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to view this page.");
}

$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Get filters from GET request
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Base SQL
$sql = "SELECT t.transaction_id, t.transaction_amount, t.transaction_date, t.transaction_location, c.category_name
        FROM Transactions t
        JOIN Categories c ON t.category_id = c.category_id
        WHERE t.user_id = ?";

// Add filters based on input
$params = [$user_id];
$types = "i";

if ($selected_month !== "all") {
    $sql .= " AND MONTH(t.transaction_date) = ?";
    $params[] = intval($selected_month);
    $types .= "i";
}

if ($selected_year !== "all") {
    $sql .= " AND YEAR(t.transaction_date) = ?";
    $params[] = intval($selected_year);
    $types .= "i";
}

$sql .= " ORDER BY t.transaction_date ASC";

// Prepare and execute
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">

<h1>Your Transactions 
    <?php 
    if ($selected_month === "all" && $selected_year === "all") {
        echo "(All Time)";
    } elseif ($selected_month === "all") {
        echo "for Year $selected_year";
    } elseif ($selected_year === "all") {
        echo "for " . date('F', mktime(0, 0, 0, $selected_month, 10)) . " (All Years)";
    } else {
        echo "for " . date('F Y', strtotime("$selected_year-$selected_month-01"));
    }
    ?>
</h1>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Category</th>
                <th>Date</th>
                <th>Location</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($row = $result->fetch_assoc()): 
                $total += $row['transaction_amount'];
            ?>
            <tr>
                <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= date('M j, Y', strtotime($row['transaction_date'])) ?></td>
                <td><?= htmlspecialchars($row['transaction_location']) ?></td>
                <td>$<?= number_format($row['transaction_amount'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-4 me-5">
        <strong>Total Income:</strong> $<?= number_format($total, 2) ?>
    </div>
<?php else: ?>
    <p class="mt-4">No transactions found for the selected filters.</p>
<?php endif; ?>

<div class="text-center mt-4">
    <a href="../front_end/home.html" class="btn btn-success">Go Back to Dashboard</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
