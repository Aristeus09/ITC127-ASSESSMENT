<?php
require_once "config.php";
include("session-checker.php");

// Check if 'username' parameter is set (passed from accounts-management.php)
if (isset($_GET['username'])) {
    $usernameToDelete = $_GET['username']; // Get the username from the URL

    // Prepare the SQL statement for deleting the user
    $sql = "DELETE FROM tblaccounts WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $usernameToDelete);
        if (mysqli_stmt_execute($stmt)) {    
            // Log the deletion action
            $sql = "INSERT INTO tblaccountslogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete";
                $module = "Accounts Management";
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $usernameToDelete, $_SESSION['username']);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "User account deleted successfully!";
                } else {
                    $message = "Error while logging the deletion action.";
                }
            }
        } else {
            $message = "Error deleting the account.";
        }
    }
}

echo "<script>
    window.location.href = 'accounts-management.php';
</script>";