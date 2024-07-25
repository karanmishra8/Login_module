<?php
session_start();
// Redirect to login if user is not logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Include database connection file
require_once 'database.php';

// Check if export button is clicked
if (isset($_POST['export'])) {
    // Fetch all users from the database
    $sql = "SELECT id, full_name, email FROM user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Set headers for Excel file download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="user_list.xls"'); // Adjusted filename extension to .xls for Excel
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open a file pointer connected to php://output for writing Excel data
        $fp = fopen('php://output', 'w');

        // Output Excel headers
        fputs($fp, "ID\tFull Name\tEmail\n");

        // Initialize an incremental ID counter
        $id = 1;

        // Output Excel data rows with incremental ID
        while ($row = $result->fetch_assoc()) {
            // Adjust the format to ensure each field is properly tab-separated
            fputs($fp, "$id\t{$row['full_name']}\t{$row['email']}\n");
            $id++; // Increment ID for the next user
        }

        // Close the file pointer
        fclose($fp);

        // Exit to prevent additional output
        exit();
    } else {
        echo "<script>alert('No records found to export');</script>";
    }
}

// Fetch all users from the database for displaying on the page
$sql = "SELECT * FROM user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f0f0f0; /* Light gray background */
        }
        .container {
            margin-top: 50px;
        }
        .btn-export {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-8">
                <h2>User List</h2>
            </div>
            <div class="col-4 text-end">
                <form method="post" action="">
                    <button type="submit" name="export" class="btn btn-success btn-export">Export to Excel</button>
                </form>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $id = 1; // Initialize ID counter
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $id . "</td>"; // Display incremental ID
                        echo "<td>" . $row["full_name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "</tr>";
                        $id++; // Increment ID for the next user
                    }
                } else {
                    echo "<tr><td colspan='3'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <p><a href="logout.php" class="btn btn-danger">Logout</a></p>
    </div>

    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-Lhs3z1AABSpkdAjkP2+PrB5CsrshPvDhrrM5A+XmN+ZTvx3XW2KkrF3uyS8tUnGj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-J7Z5rxJ5O9npwXIhBfVvwCUbVd+h2CZ9zYyHW/cMELZzIt++FV5p9B9sVW+WVv0Z" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
