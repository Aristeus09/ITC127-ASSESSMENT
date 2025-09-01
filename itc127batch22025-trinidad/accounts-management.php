<html>
	<head>
	<title>Accounts Management Page - Equipment Management System</title>
	<link rel="stylesheet" href="styles.css">
	</head>
<body>
	<?php
		session_start();
		//check if there is a session recorded
		if(isset($_SESSION['username'])) {
			echo "<h1>Welcome, " . $_SESSION['username'] . "</h1>";
			echo "<h4>Account type: " . $_SESSION['usertype'] . "</h4>";
		}
		else {
			//redirect the user back to the login page
			header("location: login.php");
		}
	?>
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
		<a href = "create-account.php">Create new account</a>
		<a href = "logout.php">Logout</a>
		<br>Search: <input type = "text" name = "txtsearch">
		<input type = "submit" name = "btnsearch" value = "Search">
	</form>
	
	<script>
		// Function Modal to Show a Pop up Deletion(Yes/No)
		function showModal(username) {
			document.getElementById("confirmationModal").style.display = "block"; // Show modal
			document.getElementById("deleteUsername").value = username; // Set username in hidden field
		}

		function confirmDelete() {
			var username = document.getElementById("deleteUsername").value;
			// Redirect to delete-account.php with the username
			window.location.href = "delete-account.php?username=" + username;
		}

		function cancelDelete() {
			document.getElementById("confirmationModal").style.display = "none"; // Close modal
		}
	</script>
	
	<div id="confirmationModal">
		<div>
			<p>Are you sure you want to delete the account?</p>
			<button onclick="confirmDelete()">Yes</button>
			<button onclick="cancelDelete()">No</button>
			<input type="hidden" id="deleteUsername" />
		</div>
	</div>
</body>
</html>
<?php
function buildtable($result) {
	if(mysqli_num_rows($result) > 0) {
		//create table using html
		echo "<table>";
		//create headers
		echo "<tr>";
		echo "<th>Username</th><th>Usertype</th><th>Status</th><th>Create by</th><th>Date created</th><th>Actions</>";
		echo "</tr>";
		echo "<br>";
		//display the data of the table
		while($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			echo "<td>" .  $row['username'] . "</td>";
			echo "<td>" .  $row['usertype'] . "</td>";
			echo "<td>" .  $row['status'] . "</td>";
			echo "<td>" .  $row['createdby'] . "</td>";
			echo "<td>" .  $row['datecreated'] . "</td>";
			echo "<td>";
			echo "<a href = 'update-account.php?username=" . $row['username'] . "'>Edit</a>";
			echo "<a href='#' onclick='showModal(\"" . $row['username'] . "\")'>Delete</a>";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else {
		echo "No record/s found.";
	}
}
//display the table
require_once "config.php";
//search
if(isset($_POST['btnsearch'])) {
	$sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username";
	if($stmt = mysqli_prepare($link, $sql)) {
		$searchvalue = '%' . $_POST['txtsearch'] . '%';
		mysqli_stmt_bind_param($stmt, "ss", $searchvalue, $searchvalue);
		if(mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			buildtable($result);
		}
		else {
			echo "<font color = 'red'>ERROR on loading the data from table.</font>";
		}
	}
}
else { //load the data from table
	$sql = "SELECT * FROM tblaccounts ORDER BY username";
	if($stmt = mysqli_prepare($link, $sql)) {
		if(mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			buildtable($result);
		}
		else {
			echo "<font color = 'red'>ERROR on loading the data from table.</font>";
		}
	}
}
?>