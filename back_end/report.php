<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check session authentication
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

// Retrieve month and year from the submitted form
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';

if (!preg_match('/^(0[1-9]|1[0-2])$/', $month) || !preg_match('/^\d{4}$/', $year)) {
    die("Invalid month or year.");
}

$report_date = "$year-$month";
$formatted_month = DateTime::createFromFormat('!m', $month)->format('F');

// Database connection
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Income Data
$query = "SELECT income_source, income_amount, date_added 
          FROM Income 
          WHERE user_id = ? AND DATE_FORMAT(date_added, '%Y-%m') = ? 
          ORDER BY date_added";
$stmt = $conn->prepare($query);
$stmt->bind_param('is', $user_id, $report_date);
$stmt->execute();
$result = $stmt->get_result();

$income_entries = [];
$total_income = 0;
while ($income = $result->fetch_assoc()) {
    $income_entries[] = $income;
    $total_income += $income['income_amount'];
}
$stmt->close();

// Fetch Transactions Data
$trans_query = "SELECT transaction_amount, category_name, transaction_date, transaction_location 
                FROM Transactions 
                JOIN Categories ON Transactions.category_id = Categories.category_id 
                WHERE user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?";
$stmt = $conn->prepare($trans_query);
$stmt->bind_param("iii", $user_id, $month, $year);
$stmt->execute();
$trans_entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Bills Data
$bill_query = "SELECT bill_name, bill_amount, due_date, payment_status 
               FROM Bills 
               WHERE user_id = ? AND MONTH(due_date) = ? AND YEAR(due_date) = ?";
$stmt = $conn->prepare($bill_query);
$stmt->bind_param("iii", $user_id, $month, $year);
$stmt->execute();
$bill_entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Savings Goals
$saving_query = "SELECT goal_name, target_amount, current_amount 
                 FROM SavingGoals 
                 WHERE user_id = ?";
$stmt = $conn->prepare($saving_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saving_entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate Budget Status
$total_transactions = array_sum(array_column($trans_entries, 'transaction_amount'));
$total_bills = array_sum(array_column($bill_entries, 'bill_amount'));
$total_saved = array_sum(array_column($saving_entries, 'current_amount'));

$status = 'Balanced';
if ($total_income < $total_transactions + $total_bills) {
    $status = 'Over budget';
} elseif ($total_income > $total_transactions + $total_bills) {
    $status = 'Under budget';
}

// Begin HTML Output
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Monthly Financial Report</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body, html {
            height: 100%;
        }
        .center-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background-color: #f8f9fa;
        }
        .content-container {
            width: 100%;
            max-width: 1000px;
        }
    </style>
</head>
<body>
<div class='center-wrapper'>
    <div class='content-container'>";

// Header
echo "<h1 class='text-center my-4'>Monthly Report for $formatted_month $year</h1>";

// Income Section
echo "<div class='card mb-4 text-center'>
        <div class='card-header bg-success text-white'>
            <h2>Income</h2>
        </div>
        <div class='card-body'>
            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Income Source</th>
                        <th>Amount</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>";
foreach ($income_entries as $income) {
    echo "<tr>
            <td>{$income['income_source']}</td>
            <td>$" . number_format($income['income_amount'], 2) . "</td>
            <td>" . date("F j, Y", strtotime($income['date_added'])) . "</td>
          </tr>";
}
echo "</tbody>
      </table>
      <strong>Total Income:</strong> \$" . number_format($total_income, 2) . "</div></div>";

// Transactions Section
echo "<div class='card mb-4 text-center'>
        <div class='card-header bg-success text-white'>
            <h2>Transactions</h2>
        </div>
        <div class='card-body'>
            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>";
foreach ($trans_entries as $trans) {
    echo "<tr>
            <td>{$trans['transaction_location']}</td>
            <td>{$trans['category_name']}</td>
            <td>\${$trans['transaction_amount']}</td>
            <td>" . date("F j, Y", strtotime($trans['transaction_date'])) . "</td>
          </tr>";
}
echo "</tbody>
      </table>
      <strong>Total Transactions:</strong> \$" . number_format($total_transactions, 2) . "</div></div>";


// Bills Section
echo "<div class='card mb-4 text-center'>
        <div class='card-header bg-success text-white'>
            <h2>Bills</h2>
        </div>
        <div class='card-body'>
            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Bill Name</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>";
foreach ($bill_entries as $bill) {
    echo "<tr>
            <td>{$bill['bill_name']}</td>
            <td>\$" . number_format($bill['bill_amount'], 2) . "</td>
            <td>" . date("F j, Y", strtotime($bill['due_date'])) . "</td>
            <td>" . ($bill['payment_status'] == 1 ? 'Paid' : 'Unpaid') . "</td>
          </tr>";
}
echo "</tbody>
      </table>
      <strong>Total Bills:</strong> \$" . number_format($total_bills, 2) . "</div></div>";


// Budget Status Section
echo "<div class='card mb-4 text-center'>
        <div class='card-header bg-success text-white'>
            <h2>Budget Status</h2>
        </div>
        <div class='card-body'>
            <h4>Status: 
                <span style='color: " . ($status === 'Under budget' ? 'green' : ($status === 'Over budget' ? 'red' : 'blue')) . ";'>
                    $status
                </span>
            </h4>
        </div>
    </div>";

// Back button
echo "<div class='text-center'>
        <a href='../front_end/report.html' class='btn btn-success'>Back to Report Form</a>
      </div>";

echo "
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";

$conn->close();
?>