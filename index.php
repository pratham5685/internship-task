<?php
// Include the database connection file
include 'db.php';

// Fetch all leads from the database
$sql = "SELECT * FROM leads";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Leads</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding: 20px;
            position: fixed; /* Fixes the sidebar in place */
            overflow-y: auto; /* Enables scrolling if content overflows */
            width: 250px; /* Fixed width for the sidebar */
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 10px 0;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding: 10px;
        }
        .content {
            margin-left: 260px; /* Adjust for fixed width sidebar */
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            margin-top: 30px; /* Added top margin for title */
        }
        .card {
            margin-bottom: 20px;
        }
        table {
            margin: 0 auto; /* Center the table */
            width: auto; /* Allow table to fit content */
        }
    </style>
</head>
<body>

<div class="d-flex flex-column flex-md-row">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="text-white">Dashboard</h2>
        <!-- <a href="all_leads.php">All Leads</a> -->
        <a href="lead_form.php">Lead Form</a>
        <a href="edit.php">Edit Lead Form</a>
        <!-- Add more sidebar links as needed -->
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>All Leads</h2>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped table-responsive">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <?php
                            // Fetch dynamic fields for table headers
                            $dynamic_fields_sql = "SHOW COLUMNS FROM leads";
                            $dynamic_fields_result = $conn->query($dynamic_fields_sql);
                            while ($row = $dynamic_fields_result->fetch_assoc()) {
                                // Skip predefined fields
                                if (!in_array($row['Field'], ['id', 'name', 'address', 'email'])) {
                                    echo "<th>" . ucfirst($row['Field']) . "</th>";
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display each lead
                        if ($result->num_rows > 0) {
                            while ($lead = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $lead['id'] . "</td>";
                                echo "<td>" . $lead['name'] . "</td>";
                                echo "<td>" . $lead['address'] . "</td>";
                                echo "<td>" . $lead['email'] . "</td>";

                                // Display dynamic field values
                                foreach ($dynamic_fields_result as $row) {
                                    if (!in_array($row['Field'], ['id', 'name', 'address', 'email'])) {
                                        echo "<td>" . (isset($lead[$row['Field']]) ? $lead[$row['Field']] : '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No leads found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
