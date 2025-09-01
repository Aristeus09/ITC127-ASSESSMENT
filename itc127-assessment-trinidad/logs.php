<?php
require_once "config.php";
include("session-checker.php");

// Handle "Delete All" action for equipment logs
if (isset($_POST['delete_all_equipment_logs'])) {
    // Delete all records from tblequipmentslogs
    $delete_sql = "DELETE FROM tblequipmentslogs";
    if (mysqli_query($link, $delete_sql)) {
        // Log the delete action in tbllogs
        $datelog = date("d/m/Y");
        $timelog = date("h:i:sa");
        $module = "Equipment-Logs";
        $action = "Delete All";
        $performedto = "All Equipment Logs";
        $performedby = $_SESSION['username'];
        $log_sql = "INSERT INTO tblaccountslogs (datelog, timelog, module, action, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
        if ($log_stmt = mysqli_prepare($link, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "ssssss", $datelog, $timelog, $module, $action, $performedto, $performedby);
            mysqli_stmt_execute($log_stmt);
        }
    }
}

// Handle "Delete All" action for account logs
if (isset($_POST['delete_all_account_logs'])) {
    // Delete all records from tblaccountslogs
    $delete_sql = "DELETE FROM tblaccountslogs";
    if (mysqli_query($link, $delete_sql)) {
        // Log the delete action in tbllogs
        $datelog = date("d/m/Y");
        $timelog = date("h:i:sa");
        $module = "Account-Logs";
        $action = "Delete All";
        $performedto = "All Account Logs";
        $performedby = $_SESSION['username'];
        $log_sql = "INSERT INTO tblaccountslogs (datelog, timelog, module, action, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
        if ($log_stmt = mysqli_prepare($link, $log_sql)) {
            mysqli_stmt_bind_param($log_stmt, "ssssss", $datelog, $timelog, $module, $action, $performedto, $performedby);
            mysqli_stmt_execute($log_stmt);
        }
    }
}

// Fetch logs from tblequipmentslogs
$equipmentLogQuery = "SELECT * FROM tblequipmentslogs ORDER BY datelog DESC, timelog DESC";
$equipmentLogResult = mysqli_query($link, $equipmentLogQuery);

// Fetch logs from tblaccountslogs
$actionLogQuery = "SELECT * FROM tblaccountslogs ORDER BY datelog DESC, timelog DESC";
$actionLogResult = mysqli_query($link, $actionLogQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="custom-container"></div>

    <div class="dashboard-content">
        <h1>Logs</h1>

        <!-- Equipment Logs Section -->
        <div class="table-section">
            <h2>Equipment Logs</h2>
            <form method="POST">
                <div class="button-wrapper">
                    <input type="submit" name="delete_all_equipment_logs" value="Delete All Equipment Logs" class="delete-button" onclick="return confirm('Are you sure you want to delete all equipment logs?');">
                </div>
            </form>
            <table class="account-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Asset Number</th>
                        <th>Performed By</th>
                        <th>Action</th>
                        <th>Module</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($equipmentLogResult) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($equipmentLogResult)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['datelog']) ?></td>
                                <td><?= htmlspecialchars($row['timelog']) ?></td>
                                <td><?= htmlspecialchars($row['assetnumber']) ?></td>
                                <td><?= htmlspecialchars($row['performedby']) ?></td>
                                <td><?= htmlspecialchars($row['action']) ?></td>
                                <td><?= htmlspecialchars($row['module']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No equipment logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Action Logs Section -->
        <div class="table-section">
            <h2>Action Logs</h2>
            <form method="POST">
                <div class="button-wrapper">
                    <input type="submit" name="delete_all_account_logs" value="Delete All Action Logs" class="delete-button" onclick="return confirm('Are you sure you want to delete all action logs?');">
                </div>
            </form>
            <table class="account-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Performed To</th>
                        <th>Performed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($actionLogResult) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($actionLogResult)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['datelog']) ?></td>
                                <td><?= htmlspecialchars($row['timelog']) ?></td>
                                <td><?= htmlspecialchars($row['module']) ?></td>
                                <td><?= htmlspecialchars($row['action']) ?></td>
                                <td><?= htmlspecialchars($row['performedto']) ?></td>
                                <td><?= htmlspecialchars($row['performedby']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No action logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>

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