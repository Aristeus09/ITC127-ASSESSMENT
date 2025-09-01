<?php
require_once "config.php";
include ("session-checker.php");
if(isset($_POST['btnsubmit'])) {
	$sql = "SELECT * FROM tblaccounts WHERE username = ?";//check if the username is existing
	if($stmt = mysqli_prepare($link, $sql)) {
		mysqli_stmt_bind_param($stmt, "s", $_POST['txtusername']);
		if(mysqli_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0) { //insert
				$sql = "INSERT INTO tblaccounts (username, password, usertype, status, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?)";
				if($stmt = mysqli_prepare($link, $sql)) {
					$status = "ACTIVE";
					$date = date("d/M/Y");
					mysqli_stmt_bind_param($stmt, "ssssss", $_POST['txtusername'], $_POST['txtpassword'], $_POST['cmbtype'], $status, $_SESSION['username'], $date);
					if(mysqli_stmt_execute($stmt)) {
						$sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
						if($stmt = mysqli_prepare($link, $sql)) {
							$date = date("d/m/Y");
							$time = date("h:i:sa");
							$action = "Create";
							$module = "Accounts Management";
							mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, trim($_POST['txtusername']), $_SESSION['username']);
							if(mysqli_stmt_execute($stmt)) {	
								echo "User account added.";
								header("location: accounts-management.php");
								exit();
							}
							else {
								echo "<font color = 'red'>Error on Insert log statement.</font>";
							}
						}
					}
					else {
						echo "<font color = 'red'>ERROR on inserting account.</font>";
					}
				}
			}
			else {
				echo "<font color = 'red'>ERROR: username already in-use.</font>";
			}
		}
		else {
			echo "<font color = 'red'>ERROR on validating existing username.</font>";
		}
	}
}
?>
<html>
	<head>
	<title>Create new account page - Equipment Management System</title>
	<link rel="stylesheet" href="styles.css">
	</head>
<body>
	<p>Fill up this form and submit to create your account</p>
<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
	Username:<input type = "text" name = "txtusername" required><br>
	Password:
	<div class="password-wrapper">
        <input type="password" name="txtpassword" id="txtpassword" required>
        <span id="eye-icon">
            <i class="fa fa-eye-slash"></i>
        </span>
    </div><br>
	Account type: <select name = "cmbtype" id = "cmbtype" required>
		<option value = "">--Select account type--</option>
		<option value = "ADMINISTRATOR">Administrator</option>
		<option value = "TECHNICAL">Technical</option>
		<option value = "USER">User</option>
	</select> <br>
	<input type = "submit" name = "btnsubmit" value = "Submit">
	<a href = "accounts-management.php">Cancel</a>
</form>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	
	<script>
		// Toggle password visibility
		function togglePassword() {
			var passwordField = document.getElementById("txtpassword");
			var icon = document.getElementById("eye-icon").querySelector("i");

			// Check if password is currently visible and toggle accordingly
			if (passwordField.type === "password") {
				passwordField.type = "text";
				icon.classList.remove("fa-eye-slash");
				icon.classList.add("fa-eye");  // Change icon to 'eye-slash'
			} else {
				passwordField.type = "password";
				icon.classList.remove("fa-eye");
				icon.classList.add("fa-eye-slash");  // Change icon back to 'eye'
			}
		}

		// Add event listener to the eye icon
		document.getElementById("eye-icon").addEventListener("click", togglePassword);
	</script>
</body>
</html>