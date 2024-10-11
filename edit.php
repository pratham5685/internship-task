<?php
// Include the database connection file
include 'db.php';

// Initialize variables
$field_names = [];
$field_name_err = "";

// Process form submission when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Loop through posted field names
    foreach ($_POST['field_names'] as $field_name) {
        $field_name = trim($field_name);
        
        // Validate dynamic field name
        if (empty($field_name)) {
            $field_name_err = "Please enter a field name.";
            break; // Break out of the loop if there's an error
        } else {
            // Sanitize the input to prevent SQL injection
            $field_name = preg_replace('/[^a-zA-Z0-9_]/', '', $field_name); // Allow only alphanumeric and underscore
            if (!empty($field_name)) {
                // Construct the SQL query
                $sql = "ALTER TABLE leads ADD `$field_name` VARCHAR(255)";
                if ($conn->query($sql) === FALSE) {
                    $field_name_err = "Error adding field: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dynamic Fields</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .sidebar { height: 100vh; background-color: #343a40; }
        .sidebar a { color: #ffffff; }
        .sidebar a:hover { background-color: #495057; }
        .content { margin-left: 250px; padding: 20px; }
        .field { margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-3">
        <h2 class="text-white">Dashboard</h2>
        <ul class="nav flex-column">
            <a href="index.php">All Leads</a>
            <a href="lead_form.php">Lead Form</a>
            <a href="edit.php">Edit Lead Form</a>
        </ul>
    </div>

    <div class="content">
        <h2>Add Dynamic Fields</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div id="dynamicFields">
                <div class="field">
                    <input type="text" name="field_names[]" class="form-control" placeholder="Dynamic Field Name" required>
                    <span class="text-danger"><?php echo $field_name_err; ?></span>
                </div>
            </div>
            <br>
            <button type="button" id="addFieldButton" class="btn btn-secondary">Add New Field</button>
            <br><br>
            <input type="submit" value="Save" class="btn btn-primary">
            <button type="button" onclick="window.location.href='index.php'" class="btn btn-secondary">Home</button>
        </form>
    </div>
</div>

<script>
    // JavaScript to add new input fields
    document.getElementById('addFieldButton').onclick = function() {
        const newField = document.createElement('div');
        newField.classList.add('field');
        newField.innerHTML = '<input type="text" name="field_names[]" class="form-control" placeholder="Dynamic Field Name" required>';
        document.getElementById('dynamicFields').appendChild(newField);
    }
</script>

</body>
</html>
