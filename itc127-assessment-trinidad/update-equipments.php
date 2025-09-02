<?php
// Start session and check if the user is logged in
require_once "config.php";
include("session-checker.php");

// Initialize the $equipment variable
$equipment = [];

// Check if 'assetnumber' is passed in the URL
if (isset($_GET['assetnumber'])) {
    $assetnumber = $_GET['assetnumber'];  // Use the assetnumber from the URL

    // SQL query to fetch equipment details based on assetnumber
    $sql = "SELECT * FROM tblequipments WHERE assetnumber = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $assetnumber);  // Bind assetnumber as a string
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Fetch the equipment details
            $equipment = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            echo "Equipment not found.";
            exit;
        }
    } else {
        echo "Error preparing the query.";
        exit;
    }
}

// Handle the form submission for updating the equipment
if (isset($_POST['btnupdate'])) {
    // Check if the serial number already exists
    $serialnumber = $_POST['serialnumber'];
    $sql_check_serial = "SELECT * FROM tblequipments WHERE serialnumber = ? AND assetnumber != ?";
    if ($check_stmt = mysqli_prepare($link, $sql_check_serial)) {
        mysqli_stmt_bind_param($check_stmt, "ss", $serialnumber, $_POST['assetnumber']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            // If the serial number already exists, show an error message
            $error_message = "<font color='red'>ERROR: Serial number is already in use.</font>";
        }
    } else {
        echo "Error checking the serial number.";
    }

    // If no error, proceed with the update
    if (empty($error_message)) {
        // Fetch the current data from the database
        $sql = "SELECT * FROM tblequipments WHERE assetnumber = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind the assetnumber parameter to fetch the current data
            mysqli_stmt_bind_param($stmt, "s", $_POST['assetnumber']);
            
            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $old_data = mysqli_fetch_assoc($result); // Fetch the existing data

                // Now, compare the old data with the new data
                $changes_made = false;
                $fields_to_update = [];

                // Compare each field
                foreach ($_POST as $key => $value) {
                    // Skip the assetnumber field itself
                    if ($key !== 'btnupdate' && $key !== 'assetnumber') {
                        if ($old_data[$key] != $value) {
                            $changes_made = true;
                            $fields_to_update[$key] = $value; // Collect changed fields
                        }
                    }
                }

                // If changes were made, update the database
                if ($changes_made) {
                    $update_sql = "UPDATE tblequipments SET
                        serialnumber = ?, 
                        type = ?, 
                        manufacturer = ?, 
                        yearmodel = ?, 
                        description = ?, 
                        branch = ?, 
                        department = ?, 
                        status = ?
                        WHERE assetnumber = ?";
                    
                    if ($update_stmt = mysqli_prepare($link, $update_sql)) {
                        // Assign values to variables before passing to bind_param
                        $serialnumber = $fields_to_update['serialnumber'] ?? $old_data['serialnumber'];
                        $type = $fields_to_update['type'] ?? $old_data['type'];
                        $manufacturer = $fields_to_update['manufacturer'] ?? $old_data['manufacturer'];
                        $yearmodel = $fields_to_update['yearmodel'] ?? $old_data['yearmodel'];
                        $description = $fields_to_update['description'] ?? $old_data['description'];
                        $branch = $fields_to_update['branch'] ?? $old_data['branch'];
                        $department = $fields_to_update['department'] ?? $old_data['department'];
                        $status = $fields_to_update['status'] ?? $old_data['status'];
                        $assetnumber = $_POST['assetnumber'];

                        // Bind parameters
                        mysqli_stmt_bind_param(
                            $update_stmt, 
                            "sssssssss", 
                            $serialnumber,
                            $type,
                            $manufacturer,
                            $yearmodel,
                            $description,
                            $branch,
                            $department,
                            $status,
                            $assetnumber
                        );

                        if (mysqli_stmt_execute($update_stmt)) {
                            // Log the update action in the equipment logs
                            $log_sql = "INSERT INTO tblequipmentslogs (datelog, timelog, assetnumber, performedby, action, module) VALUES (?, ?, ?, ?, ?, ?)";
                            if ($log_stmt = mysqli_prepare($link, $log_sql)) {
                                $datelog = date('d/m/Y');
                                $timelog = date('h:i:sa');
                                $action = "Update";
                                $module = "Equipments";
                                mysqli_stmt_bind_param($log_stmt, "ssssss", $datelog, $timelog, $_POST['assetnumber'], $_SESSION['username'], $action, $module);
                                
                                if (mysqli_stmt_execute($log_stmt)) {
                                    // Redirect back to the equipment list after successful update
                                    header("location: equipments.php");
                                    exit();
                                } else {
                                    echo "<font color='red'>Error logging the update action.</font>";
                                }
                            } else {
                                echo "<font color='red'>Error preparing log query.</font>";
                            }
                        } else {
                            echo "<font color='red'>Error updating equipment.</font>";
                        }
                    } else {
                        echo "<font color='red'>Error preparing update query.</font>";
                    }
                } else {
                    echo "<font color='green'>No changes were made to the equipment data.</font>";
                }
            } else {
                echo "<font color='red'>Error fetching the current data.</font>";
            }
        } else {
            echo "<font color='red'>Error preparing select query.</font>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Equipment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="custom-container"></div>

    <h1>Edit Equipment</h1>
    <?php
    // Display error message if serial number already exists
    if (isset($error_message)) {
        echo $error_message;
    }
    ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="assetnumber">Asset Number:</label>
        <input type="text" name="assetnumber" value="<?php echo isset($_POST['assetnumber']) ? $_POST['assetnumber'] : (isset($equipment['assetnumber']) ? $equipment['assetnumber'] : ''); ?>" readonly required>

        <label for="serialnumber">Serial Number:</label>
        <input type="text" name="serialnumber" value="<?php echo isset($_POST['serialnumber']) ? $_POST['serialnumber'] : (isset($equipment['serialnumber']) ? $equipment['serialnumber'] : ''); ?>" required>

        <label for="type">Type:</label>
        <select name="type" required>
            <?php
            $types = ["Monitor", "CPU", "Keyboard", "Mouse", "AVR", "MAC", "Printer", "Projector"];
            foreach ($types as $type) {
                $selected = (isset($_POST['type']) && $_POST['type'] == $type) || (isset($equipment['type']) && $equipment['type'] == $type) ? 'selected' : '';
                echo "<option value=\"$type\" $selected>$type</option>";
            }
            ?>
        </select>

        <label for="manufacturer">Manufacturer:</label>
        <input type="text" name="manufacturer" value="<?php echo isset($_POST['manufacturer']) ? $_POST['manufacturer'] : (isset($equipment['manufacturer']) ? $equipment['manufacturer'] : ''); ?>" required>

        <label for="yearmodel">Year Model:</label>
        <input type="text" name="yearmodel" value="<?php echo isset($_POST['yearmodel']) ? $_POST['yearmodel'] : (isset($equipment['yearmodel']) ? $equipment['yearmodel'] : ''); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description"><?php echo isset($_POST['description']) ? $_POST['description'] : (isset($equipment['description']) ? $equipment['description'] : ''); ?></textarea>

        <label for="branch">Branch:</label>
        <select name="branch" required>
            <?php
            $branches = ["AU Main Campus", "AU Pasay", "AU Mandaluyong", "AU Malabon", "AU Pasig"];
            foreach ($branches as $branch) {
                $selected = (isset($_POST['branch']) && $_POST['branch'] == $branch) || (isset($equipment['branch']) && $equipment['branch'] == $branch) ? 'selected' : '';
                echo "<option value=\"$branch\" $selected>$branch</option>";
            }
            ?>
        </select>

        <label for="department">Department:</label>
        <input type="text" name="department" value="<?php echo isset($_POST['department']) ? $_POST['department'] : (isset($equipment['department']) ? $equipment['department'] : ''); ?>" required>

        <label for="status">Status:</label>
        <select name="status" required>
            <?php
            $statuses = ["Working", "On-Repair", "Retired"];
            foreach ($statuses as $status) {
                $selected = (isset($_POST['status']) && $_POST['status'] == $status) || (isset($equipment['status']) && $equipment['status'] == $status) ? 'selected' : '';
                echo "<option value=\"$status\" $selected>$status</option>";
            }
            ?>
        </select>

        <input type="submit" name="btnupdate" value="Update Equipment">
    </form>

    <a href="equipments.php" class="back-button">Back to Equipment List</a>

    <script>
        const container = document.querySelector('.custom-container');

        for (let i = 0; i < 100; i++) {
            let star = document.createElement('div');
            star.classList.add('custom-circle-container');

            let size = Math.random() * (3 - 1) + 1;
            let xPos = Math.random() * 100;
            let yPos = Math.random() * 100;
            let delay = Math.random() * 5 + "s";

            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.left = `${xPos}vw`;
            star.style.top = `${yPos}vh`;
            star.style.animationDelay = delay;

            container.appendChild(star);
        }
    </script>
</body>
</html>