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
        // Ignore predefined fields: 'name', 'address', 'email'
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
    <script>
        // Check if the URL contains 'success=1'
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success') && urlParams.get('success') == '1') {
                // Show a pop-up to indicate success
                alert('Lead added successfully!');

                // After the alert, redirect the user to remove 'success' from the URL
                window.location.href = window.location.pathname; // This will reload the page without query params
            }
        }
    </script>
</head>
<body>

<h2>Lead Form</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div>
        <label>Name</label>
        <input type="text" name="name" value="<?php echo $name; ?>">
        <span style="color: red;"><?php echo $name_err; ?></span>
    </div>
    <br>
    <div>
        <label>Address</label>
        <input type="text" name="address" value="<?php echo $address; ?>">
        <span style="color: red;"><?php echo $address_err; ?></span>
    </div>
    <br>
    <div>
        <label>Email</label>
        <input type="text" name="email" value="<?php echo $email; ?>">
        <span style="color: red;"><?php echo $email_err; ?></span>
    </div>
    <br>

    <!-- Display dynamic fields -->
    <?php foreach ($dynamic_fields as $field): ?>
    <div>
        <label><?php echo ucfirst($field); ?></label>
        <input type="text" name="<?php echo $field; ?>">
    </div>
    <br>
    <?php endforeach; ?>



<div>
    <input type="submit" value="Submit">
    <button type="button" onclick="window.location.href='edit.php'">Edit</button>
</div>


</form>

</body>
</html>

