<?php
session_start();

// Check if the form is submitted using the button
if (isset($_POST['btnlogin'])) {
    // Require config file
    require_once "config.php";

    // Create login statement
    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'Active'";

    // Check if the statement will run by preparing the statement with the link
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind the data from the statement
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);

        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            // Get the result
            $result = mysqli_stmt_get_result($stmt);

            // Check if there is/are rows in the result
            if (mysqli_num_rows($result) > 0) {
                // Fetch result as array
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Record session
                $_SESSION['username'] = $_POST['txtusername'];
                $_SESSION['usertype'] = $account['usertype'];

                // Redirect to the dashboard page
                header("location:dashboard.php");
            } else {
                // Set error message if login fails
                $error_message = "Incorrect login details or account is inactive.";
            }
        } else {
            // Set error message if statement execution fails
            $error_message = "ERROR executing the login statement.";
        }
    }
}
?>

<html>
<head>
    <title>Login Page - Equipment Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="custom-container"></div>
    <div class="login-form">
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form">
            <h1>Login to Your Account</h1>
            <label for="txtusername">Username:</label>
            <input type="text" name="txtusername" id="txtusername" required><br>
            <label for="txtpassword">Password:</label>
            <div class="password-wrapper">
                <input type="password" name="txtpassword" id="txtpassword" required>
                <span id="eye-icon" onclick="togglePassword()">
                    <i class="fa fa-eye-slash"></i>
                </span>
            </div><br>
            <input type="submit" name="btnlogin" value="Login">
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("txtpassword");
            var icon = document.getElementById("eye-icon").querySelector("i");

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