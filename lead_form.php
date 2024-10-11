<?php
// Include the database connection file
include 'db.php';

// Initialize variables
$name = $address = $email = "";
$name_err = $address_err = $email_err = "";

// Fetch dynamic fields from the leads table
$dynamic_fields = [];
$sql = "SHOW COLUMNS FROM leads";
$result = $conn->query($sql);

// Store dynamic field names
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ignore predefined fields: 'id', 'name', 'address', 'email'
        if (!in_array($row['Field'], ['id', 'name', 'address', 'email'])) {
            $dynamic_fields[] = $row['Field'];
        }
    }
}

// Process form submission when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Initialize array for dynamic field values
    $dynamic_field_values = [];
    foreach ($dynamic_fields as $field) {
        if (!empty(trim($_POST[$field]))) {
            $dynamic_field_values[$field] = trim($_POST[$field]);
        }
    }

    // If no errors, insert data into the database
    if (empty($name_err) && empty($address_err) && empty($email_err)) {
        $sql = "INSERT INTO leads (name, address, email" . (count($dynamic_field_values) > 0 ? ', ' . implode(', ', array_keys($dynamic_field_values)) : '') . ") VALUES (?, ?, ?" . (count($dynamic_field_values) > 0 ? ', ' . str_repeat('?, ', count($dynamic_field_values) - 1) . '?' : '') . ")";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param(str_repeat('s', 3 + count($dynamic_field_values)), $name, $address, $email, ...array_values($dynamic_field_values));

            if ($stmt->execute()) {
                // Redirect to the same page with success flag to trigger JavaScript pop-up
                header("Location: " . $_SERVER['REQUEST_URI'] . "?success=1");
                exit(); // Stop script execution after redirect
            } else {
                echo "Something went wrong. Please try again.";
            }

            // Close statement
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px; /* Fixed width for the sidebar */
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 260px; /* Margin to the left of the sidebar */
        }
        .form-title {
            margin-bottom: 30px;
        }
    </style>
    <script>
        // Check if the URL contains 'success=1'
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success') && urlParams.get('success') == '1') {
                alert('Lead added successfully!');
                window.location.href = window.location.pathname; // This will reload the page without query params
            }
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2 class="text-white text-center">Dashboard</h2>
    <a href="index.php">All Leads</a>
        <a href="lead_form.php">Lead Form</a>
        <a href="edit.php">Edit Lead Form</a>
</div>

<div class="main-content container">
    <h2 class="form-title">Add Lead</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
            <span class="text-danger"><?php echo $name_err; ?></span>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" class="form-control" name="address" value="<?php echo $address; ?>">
            <span class="text-danger"><?php echo $address_err; ?></span>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" name="email" value="<?php echo $email; ?>">
            <span class="text-danger"><?php echo $email_err; ?></span>
        </div>

        <!-- Display dynamic fields -->
        <?php foreach ($dynamic_fields as $field): ?>
        <div class="form-group">
            <label><?php echo ucfirst($field); ?></label>
            <input type="text" class="form-control" name="<?php echo $field; ?>">
        </div>
        <?php endforeach; ?>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='edit.php'">Edit</button>
        </div>
    </form>
</div>

</body>
</html>
