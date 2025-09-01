<?php
//check if the form is submitted using the button
if(isset($_POST['btnlogin'])) {
	//require config file
	require_once "config.php";
	//create login statement
	$sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'ACTIVE'";
	//check if the statement will run by preparing the statement the link
	if($stmt = mysqli_prepare($link, $sql)) {
		//bind the data from the statement
		mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);
		//execute statement
		if(mysqli_stmt_execute($stmt)) {
			//get the result
			$result = mysqli_stmt_get_result($stmt);
			//check if there is/are row/s on the result
			if(mysqli_num_rows($result) > 0) {
				//fetch result as array
				$account = mysqli_fetch_array($result, MYSQLI_ASSOC);
				//create session
				session_start();
				//record the session
				$_SESSION['username'] = $_POST['txtusername'];
				$_SESSION['usertype'] = $account['usertype'];
				//redirect to the accounts management page
				header("location:accounts-management.php");
			}			
			else {
				echo "<font color = 'red'>Incorrect login details or account is inactive.</font>";
			}
		}
		else {
			echo "<font color = 'red'>ERROR on the login statement.</font>";
		}
	}
}
?>
<html>
	<head>
		<title>Login Page - Equipment Management System</title>
		<link rel="stylesheet" href="styles.css">
	</head>
<body>
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
		Username: <input type = "text" name = "txtusername"><br>
		 Password:
        <div class="password-wrapper">
            <input type="password" name="txtpassword" id="txtpassword" required>
            <span id="eye-icon">
                <i class="fa fa-eye-slash"></i>
            </span>
        </div><br>
        <input type="submit" name="btnlogin" value="Login">
    </form>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
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

        document.getElementById("eye-icon").addEventListener("click", togglePassword);
    </script>
	</form>
</body>
</html>
