<?php
require_once "config.php";
include ("session-checker.php");
if(isset($_POST['btnsubmit'])) { //update account
	$sql = "UPDATE tblaccounts SET password = ?, usertype = ?, status = ? WHERE username = ?";
	if($stmt = mysqli_prepare($link, $sql)) {
		mysqli_stmt_bind_param($stmt, "ssss", $_POST['txtpassword'], $_POST['cmbtype'], $_POST['rbstatus'], $_GET['username']);
		if(mysqli_stmt_execute($stmt)) {
			$sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
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
				}
				else {
					echo "<font color = 'red'>Error on insert log statement</font>";
				}
			}
		}
		else {
			echo "<font color = 'red'>Error on update statement. </font>";
		}
	}
}
else { //loading the date to the form
	if(isset($_GET['username']) && !empty(trim($_GET['username']))) {
		$sql = "SELECT * FROM tblaccounts WHERE username = ?";
		if($stmt = mysqli_prepare($link, $sql)) {
			mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
			if(mysqli_stmt_execute($stmt)) {
				$result = mysqli_stmt_get_result($stmt);
				$account = mysqli_fetch_array($result, MYSQLI_ASSOC);
			}
			else {
				echo "<font color = 'red>Error on loading account data.</font>";
			}
		}
	}
}
?>
<html>
	<head>
		<title>Update Account - Equipment Management </title>
		<link rel="stylesheet" href="styles.css">
	</head>
<body>
	<p>Change the value on this form and submit to update the account</p>
	<form action = "<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method = "POST">
		Username: <?php echo $account['username']; ?> <br>
		Password: 
    <div class="password-wrapper">
        <input type="password" name="txtpassword" value="<?php echo $account['password']; ?>" required><br>
    </div>
    
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
</body>
</html>
