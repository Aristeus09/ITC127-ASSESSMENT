<?php
// Start session and check if the user is logged in
require_once "config.php";
include("session-checker.php");

if(isset($_POST['btnsubmit'])) { // Update account
    $sql = "UPDATE tblaccounts SET password = ?, usertype = ?, status = ? WHERE username = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $_POST['txtpassword'], $_POST['cmbtype'], $_POST['rbstatus'], $_GET['username']);
        if(mysqli_stmt_execute($stmt)) {
            $sql = "INSERT INTO tblaccountslogs (datelog, timelog, action, module, performedto, performedby) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            if($stmt = mysqli_prepare($link, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Update";
                $module = "Accounts Management";
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_GET['username'], $_SESSION['username']);
                if(mysqli_stmt_execute($stmt)) {
                    echo "User account updated!";
                    header("location: accounts-management.php");
                    exit();    
                } else {
                    echo "<font color='red'>Error on insert log statement</font>";
                }
            }
        } else {
            echo "<font color='red'>Error on update statement. </font>";
        }
    }
} else { // Loading the data into the form
    if(isset($_GET['username']) && !empty(trim($_GET['username']))) {
        $sql = "SELECT * FROM tblaccounts WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                echo "<font color='red'>Error on loading account data.</font>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Account - Equipment Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="custom-container"></div>

    <p>Change the value on this form and submit to update the account</p>
    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST">
        Username: <?php echo $account['username']; ?> <br>
        Password:
        <div class="password-wrapper">
            <input type="password" name="txtpassword" id="txtpassword" value="<?php echo $account['password']; ?>" required>
            <span id="eye-icon"><i class="fa fa-eye-slash"></i></span>
        </div><br>

        Current User type: <?php echo $account['usertype']; ?> <br>
        Change User type to: 
        <select name="cmbtype" id="cmbtype" required>
            <option value="">--Select Account Type--</option>
            <option value="ADMINISTRATOR">Administrator</option>
            <option value="TECHNICAL">Technician</option>
            <option value="USER">User</option>
        </select><br>

        <div class="radio-container">
            <?php
                $status = $account['status'];
                if ($status == 'ACTIVE') {
                    echo '<input type="radio" name="rbstatus" value="ACTIVE" checked><label>Active</label>';
                    echo '<input type="radio" name="rbstatus" value="INACTIVE"><label>Inactive</label>';
                } else {
                    echo '<input type="radio" name="rbstatus" value="ACTIVE"><label>Active</label>';
                    echo '<input type="radio" name="rbstatus" value="INACTIVE" checked><label>Inactive</label>';
                }
            ?>
        </div>

        <input type="submit" name="btnsubmit" value="Update">
        <a href="accounts-management.php">Cancel</a>
    </form>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById("txtpassword");
            const icon = document.getElementById("eye-icon").querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }

        document.getElementById("eye-icon").addEventListener("click", togglePassword);

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