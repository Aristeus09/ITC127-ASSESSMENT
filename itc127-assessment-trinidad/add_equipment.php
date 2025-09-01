<?php
// Start session and check if the user is logged in
require_once "config.php";
include("session-checker.php");

// Handle form submission for adding equipment
if (isset($_POST['btnadd'])) {

    $sql = "SELECT * FROM tblequipments WHERE assetnumber = ? OR serialnumber = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['assetnumber'], $_POST['serialnumber']);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO tblequipments (assetnumber, serialnumber, type, manufacturer, yearmodel, description, branch, department, status, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if($stmt = mysqli_prepare($link, $sql)) {
                    $datelog = date('Y-m-d H:i:s');
                    mysqli_stmt_bind_param($stmt, "sssssssssss", $_POST['assetnumber'], $_POST['serialnumber'], $_POST['type'], $_POST['manufacturer'], $_POST['yearmodel'], $_POST['description'], $_POST['branch'], $_POST['department'], $_POST['status'], $_SESSION['username'], $datelog);
                    if(mysqli_stmt_execute($stmt)) {
                        $sql = "INSERT INTO tblequipmentslogs (datelog, timelog, assetnumber, performedby, action, module) VALUES (?, ?, ?, ?, ?, ?)";
                        if($stmt = mysqli_prepare($link, $sql)) {
                            $datelog = date("d/m/Y");
                            $timelog = date('h:i:sa');
                            $action = "Add";
                            $module = "Equipments";
                            mysqli_stmt_bind_param($stmt, "ssssss", $datelog, $timelog, $_POST['assetnumber'], $_SESSION['username'], $action, $module);
                            if (mysqli_stmt_execute($stmt)) {
                                echo "Equipment Added.";
                                header("location: equipments.php");
                                exit();
                            } else {
                                echo "<font color = 'red'>Error on Insert log statement.</font>";
                            }
                        }
                    } 
                    else {
                        echo "<font color='red'>Error adding equipment. Please try again.</font><br>";
                    }
                }
            }
            else {
                echo "<font color = 'red'>ERROR: AssetNumber or Serial Number exists.</font>";
            }
        }
        else {
            echo "<font color = 'red'>ERROR on validating existing equipment.</font>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Equipment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="custom-container"></div>

    <h1>Add Equipment</h1>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="assetnumber">Asset Number:</label><br>
        <input type="text" id="assetnumber" name="assetnumber" required><br><br>

        <label for="serialnumber">Serial Number:</label><br>
        <input type="text" id="serialnumber" name="serialnumber" required><br><br>

        <label for="type">Type:</label><br>
        <select name="type" id="type" required>
            <option value="">--Select Type--</option>
            <option value="Monitor">Monitor</option>
            <option value="CPU">CPU</option>
            <option value="Keyboard">Keyboard</option>
            <option value="Mouse">Mouse</option>
            <option value="AVR">AVR</option>
            <option value="MAC">MAC</option>
            <option value="Printer">Printer</option>
            <option value="Projector">Projector</option>
        </select><br><br>

        <label for="manufacturer">Manufacturer:</label><br>
        <input type="text" id="manufacturer" name="manufacturer" required><br><br>

        <label for="yearmodel">Year Model:</label><br>
        <input type="text" id="yearmodel" name="yearmodel" maxlength="4" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>

        <label for="branch">Branch:</label><br>
        <select name="branch" id="branch" required>
            <option value="">--Select Campus--</option>
            <option value="AU Main Campus">AU Main Campus</option>
            <option value="AU Pasay">AU Pasay</option>
            <option value="AU Mandaluyong">AU Mandaluyong</option>
            <option value="AU Malabon">AU Malabon</option>
            <option value="AU Pasig">AU Pasig</option>
        </select><br><br>

        <label for="department">Department:</label><br>
        <input type="text" id="department" name="department" required><br><br>

        <label for="status">Status:</label><br>
        <select name="status" id="status" required>
            <option value="Working">Working</option>
            <option value="On-Repair">On-Repair</option>
            <option value="Retired">Retired</option>
        </select><br><br>

        <input type="submit" name="btnadd" value="Add Equipment">
    </form>

    <br>
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