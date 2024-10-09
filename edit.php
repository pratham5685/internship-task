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
                $conn->query($sql);
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
</head>
<body>

<h2>Add Dynamic Fields</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div id="dynamicFields">
        <div class="field">
            <input type="text" name="field_names[]" placeholder="Dynamic Field Name">
            <span style="color: red;"><?php echo $field_name_err; ?></span>
        </div>
    </div>
    <br>
    <button type="button" id="addFieldButton">Add New Field</button>
    <br><br>
    <input type="submit" value="Save">
    <button type="button" onclick="window.location.href='index.php'">Home</button>
</form>

<script>
    // JavaScript to add new input fields
    document.getElementById('addFieldButton').onclick = function() {
        const newField = document.createElement('div');
        newField.classList.add('field');
        newField.innerHTML = '<input type="text" name="field_names[]" placeholder="Dynamic Field Name">';
        document.getElementById('dynamicFields').appendChild(newField);
    }
</script>

</body>
</html>
