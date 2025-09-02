<?php
require_once "config.php";
include("session-checker.php");

if ($_SESSION['usertype'] !== 'Administrator' && $_SESSION['usertype'] !== 'Technical') {
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
    <title>Equipments - Equipment Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-body">
    <div class="custom-container"></div>

    <h1>Equipments</h1>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
        <input type="text" name="assetnumber" placeholder="Search by Asset Number" value="<?php echo htmlspecialchars($assetnumber); ?>" class="search-input">
        <input type="text" name="serialnumber" placeholder="Search by Serial Number" value="<?php echo htmlspecialchars($serialnumber); ?>" class="search-input">
        <input type="text" name="type" placeholder="Search by Type" value="<?php echo htmlspecialchars($type); ?>" class="search-input">
        <input type="text" name="department" placeholder="Search by Department" value="<?php echo htmlspecialchars($department); ?>" class="search-input">
        <input type="submit" value="Search" class="search-btn">
    </form>

    <div class="dashboard-buttons">
        <button onclick="location.href='add_equipment.php'">Add Equipment</button>
        <button onclick="location.href='dashboard.php'">Back to Dashboard</button>
    </div>

    <h2>Equipment List</h2>
    <table class="account-table">
        <tr>
            <th>Asset Number</th>
            <th>Serial Number</th>
            <th>Type</th>
            <th>Branch</th>
            <th>Department</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['assetnumber'] . "</td>";
                echo "<td>" . $row['serialnumber'] . "</td>";
                echo "<td>" . $row['type'] . "</td>";
                echo "<td>" . $row['branch'] . "</td>";
                echo "<td>" . $row['department'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                echo "<a href='update-equipments.php?assetnumber=" . urlencode($row['assetnumber']) . "' class='edit-link'>Update</a>";
                echo "<a href='#' onclick='showModal(\"" . $row['assetnumber'] . "\")' class='delete-link'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No equipment found.</td></tr>";
        }
        ?>
    </table>

    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete this equipment?</p>
            <button onclick="confirmDelete()">Yes</button>
            <button onclick="cancelDelete()">No</button>
            <input type="hidden" id="deleteAssetNumber" />
        </div>
    </div>

    <script>
    // Function to show modal for account deletion
    function showModal(assetnumber) {
        document.getElementById("confirmationModal").style.display = "block";
        document.getElementById("deleteAssetNumber").value = assetnumber;
    }

    function confirmDelete() {
        var assetnumber = document.getElementById("deleteAssetNumber").value;
        window.location.href = "delete-equipment.php?assetnumber=" + assetnumber;
    }

    function cancelDelete() {
        document.getElementById("confirmationModal").style.display = "none";
    }

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