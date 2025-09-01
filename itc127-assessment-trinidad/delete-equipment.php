<?php
require_once "config.php";
include("session-checker.php");

// Check if 'assetnumber' parameter is set
if (isset($_GET['assetnumber'])) {
    $assetNumberToDelete = $_GET['assetnumber']; // Get the asset number from the URL

    // Prepare the SQL statement for deleting the equipment
    $sql = "DELETE FROM tblequipments WHERE assetnumber = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $assetNumberToDelete);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt); // Close delete statement

            // Log the deletion action
            $sql = "INSERT INTO tblaccountslogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete";
                $module = "Equipments Management";
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $assetNumberToDelete, $_SESSION['username']);
                mysqli_stmt_execute($stmt);
            }
        }
    }
}

echo "<script>
    window.location.href = 'equipments.php';
</script>";