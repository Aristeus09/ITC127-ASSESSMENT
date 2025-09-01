<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounts Management Page - Equipment Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="custom-container"></div>

    <?php
    session_start();
    if (isset($_SESSION['username'])) {
        echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . "</h1>";
        echo "<h4>Account type: " . htmlspecialchars($_SESSION['usertype']) . "</h4>";
    } else {
        header("location: login.php");
        exit;
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="search-form">
        <a href="create-account.php" class="create-account-link">Create New Account</a>
        <a href="dashboard.php" class="back-link">Back</a>
        <input type="text" name="txtsearch" placeholder="Search by Username or Usertype" class="search-input">
        <input type="submit" name="btnsearch" value="Search" class="search-btn">
    </form>

    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete the account?</p>
            <input type="hidden" id="deleteUsername">
            <button onclick="confirmDelete()">Yes, Delete</button>
            <button onclick="cancelDelete()">Cancel</button>
        </div>
    </div>

    <script>
        function showModal(username) {
            document.getElementById("confirmationModal").style.display = "block";
            document.getElementById("deleteUsername").value = username;
        }

        function confirmDelete() {
            const username = document.getElementById("deleteUsername").value;
            window.location.href = "delete-account.php?username=" + encodeURIComponent(username);
        }

        function cancelDelete() {
            document.getElementById("confirmationModal").style.display = "none";
        }
    </script>

    <?php
    function buildtable($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table class='account-table'>";
            echo "<tr><th>Username</th><th>Usertype</th><th>Status</th><th>Created By</th><th>Date Created</th><th>Actions</th></tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['usertype']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['createdby']) . "</td>";
                echo "<td>" . htmlspecialchars($row['datecreated']) . "</td>";
                echo "<td>";
                echo "<a href='update-account.php?username=" . urlencode($row['username']) . "' class='edit-link'>Edit</a>";
                echo "<a href='#' onclick='showModal(\"" . addslashes($row['username']) . "\")' class='delete-link'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }
    }

    require_once "config.php";

    if (isset($_POST['btnsearch'])) {
        $sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username";
        if ($stmt = mysqli_prepare($link, $sql)) {
            $searchvalue = '%' . $_POST['txtsearch'] . '%';
            mysqli_stmt_bind_param($stmt, "ss", $searchvalue, $searchvalue);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            buildtable($result);
        } else {
            echo "<p style='color:red;'>Error loading data from table.</p>";
        }
    } else {
        $sql = "SELECT * FROM tblaccounts ORDER BY username";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            buildtable($result);
        } else {
            echo "<p style='color:red;'>Error loading data from table.</p>";
        }
    }
    ?>

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