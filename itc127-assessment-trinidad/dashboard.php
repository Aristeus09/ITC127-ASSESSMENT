<?php
include ("session-checker.php");
?>

<html>
<head>
    <title>Dashboard - Equipment Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="custom-container"></div>

    <div class="dashboard-content">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <h2>Dashboard</h2>

        <div class="dashboard-buttons">
            <?php if($_SESSION['usertype'] == 'Administrator'): ?>
                <button onclick="location.href='accounts-management.php'">Accounts Management</button>
                <button onclick="location.href='equipments.php'">Equipments</button>
                <button onclick="location.href='logs.php'">Logs</button>
            <?php elseif($_SESSION['usertype'] == 'Technical'): ?>
                <button onclick="location.href='equipments.php'">Equipments</button>
            <?php elseif($_SESSION['usertype'] == 'User'): ?>
                <button onclick="location.href='view-equipments.php'">View Equipments</button>
            <?php endif; ?>
        </div>

        <br>
        <a href="logout.php" class="logout-link">Logout</a>
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