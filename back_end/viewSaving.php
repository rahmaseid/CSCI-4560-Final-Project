<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to view this page.");
}

// Database connection
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current user ID
$user_id = $_SESSION['user_id'];

// Prepare the SQL query to fetch savings goals for the logged-in user
$sql = "SELECT goal_id, goal_name, target_amount, current_amount FROM SavingGoals WHERE user_id = ? ORDER BY goal_name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Start outputting the HTML page
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Savings Goals</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../styling/style.css">
</head>
<body>


  <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">
        <h4 class="mb-0">Your Savings Goals</h4>
      </div>
      <div class="card-body">
        <!-- Button to Add New Goal -->
        <a href="saving.html" class="btn btn-success mb-3">Add New Goal</a>

        <!-- Table to Display Savings Goals -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Goal Name</th>
              <th scope="col">Target Amount</th>
              <th scope="col">Current Amount</th>
              <th scope="col">Progress</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Calculate progress percentage
                    $progress = ($row['current_amount'] / $row['target_amount']) * 100;
                    $progress = round($progress, 2); // Round to 2 decimal places

                    echo "<tr>
                            <td>" . htmlspecialchars($row['goal_name']) . "</td>
                            <td>$" . number_format($row['target_amount'], 2) . "</td>
                            <td>$" . number_format($row['current_amount'], 2) . "</td>
                            <td>" . $progress . "%</td>
                            <td>
                                <a href='editGoal.php?goal_id=" . $row['goal_id'] . "' class='btn btn-sm btn-success'>Edit</a>
                                <a href='deleteGoal.php?goal_id=" . $row['goal_id'] . "' class='btn btn-sm btn-danger'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No savings goals found.</td></tr>";
            }

            // Free result and close connection
            $stmt->close();
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
      <!-- Centered Button (margin-top = 4) -->
      <div class="text-center mt-4">
        <a href='../front_end/home.html' class="btn btn-success">Go Back to Dashboard</a>
      </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
