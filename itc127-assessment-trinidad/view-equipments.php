<?php
require_once "config.php";
include("session-checker.php");

// Check user role for access control
// Assuming you store the user role in the session, e.g., $_SESSION['role']
if ($_SESSION['usertype'] !== 'User') {
    // Redirect users with non-view-only roles to a restricted page or dashboard
    header("Location: dashboard.php");
    exit;
}

// Initialize search variables
$assetnumber = '';
$serialnumber = '';
$type = '';
$department = '';

// Check if search fields are set
if(isset($_POST['assetnumber'])) {
    $assetnumber = $_POST['assetnumber'];
}
if(isset($_POST['serialnumber'])) {
    $serialnumber = $_POST['serialnumber'];
}
if(isset($_POST['type'])) {
    $type = $_POST['type'];
}
if(isset($_POST['department'])) {
    $department = $_POST['department'];
}

$sql = "SELECT * FROM tblequipments WHERE 
            assetnumber LIKE ? AND 
            serialnumber LIKE ? AND 
            type LIKE ? AND 
            department LIKE ? 
        ORDER BY datecreated ASC";

// Debugging: Output the SQL query to verify it's correct
//echo $sql;

// Prepare statement
$stmt = mysqli_prepare($link, $sql);

// Add wildcard (%) for partial matching
$assetTerm = "%" . $assetnumber . "%";
$serialTerm = "%" . $serialnumber . "%";
$typeTerm = "%" . $type . "%";
$departmentTerm = "%" . $department . "%";

// Bind parameters
mysqli_stmt_bind_param($stmt, 'ssss', $assetTerm, $serialTerm, $typeTerm, $departmentTerm);

// Execute query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipments - View Equipment Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="custom-container"></div>

    <h1>View Equipments</h1>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
        <input type="text" name="assetnumber" placeholder="Search by Asset Number" value="<?php echo htmlspecialchars($assetnumber); ?>" class="search-input">
        <input type="text" name="serialnumber" placeholder="Search by Serial Number" value="<?php echo htmlspecialchars($serialnumber); ?>" class="search-input">
        <input type="text" name="type" placeholder="Search by Type" value="<?php echo htmlspecialchars($type); ?>" class="search-input">
        <input type="text" name="department" placeholder="Search by Department" value="<?php echo htmlspecialchars($department); ?>" class="search-input">
        <input type="submit" value="Search" class="search-btn">
    </form>

    <div class="dashboard-buttons">
        <button onclick="location.href='dashboard.php'">Back to Dashboard</button>
    </div>

    <h2>Equipment List</h2>
    <table class="account-table">
        <tr>
            <th>Asset Number</th>
            <th>Serial Number</th>
            <th>Type</th>
            <th>Department</th>
            <th>Status</th>
        </tr>

        <?php
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['assetnumber'] . "</td>";
                echo "<td>" . $row['serialnumber'] . "</td>";
                echo "<td>" . $row['type'] . "</td>";
                echo "<td>" . $row['department'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No equipment found.</td></tr>";
        }
        ?>
    </table>

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