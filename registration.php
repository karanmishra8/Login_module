<?php
session_start();

// Redirect to index.php if user is already logged in
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

require_once "database.php";

$errors = array();

if (isset($_POST["submit"])) {
    $fullName = htmlspecialchars($_POST["fullname"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
        array_push($errors, "All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $passwordRepeat) {
        array_push($errors, "Passwords do not match");
    }

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            array_push($errors, "Email already exists");
        }
    } else {
        array_push($errors, "Database error");
    }

    if (count($errors) === 0) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success mt-3'>You are registered successfully.</div>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Something went wrong</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #888888		; /* Light gray background */
        }
        .card {
            background-color: #fff; /* White background for the card */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Soft shadow */
        }
        .btn-primary {
            background-color: #007bff; /* Blue primary button color */
            border-color: #007bff; /* Blue border color */
            width: 100%; /* Full-width button */
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Register</h2>
                        <?php if (count($errors) > 0) : ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error) : ?>
                                    <p><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <form action="registration.php" method="post">
                            <div class="mb-3">
                                <label for="fullname" class="form-label"> Name:</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($_POST['fullname']) ? $_POST['fullname'] : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="repeat_password" class="form-label">Confirm Password:</label>
                                <input type="password" class="form-control" id="repeat_password" name="repeat_password">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-secondary rounded border" name="submit">Register</button>
                            </div>
                        </form>
                        <p class="mt-3 text-center">Already registered? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-Lhs3z1AABSpkdAjkP2+PrB5CsrshPvDhrrM5A+XmN+ZTvx3XW2KkrF3uyS8tUnGj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-J7Z5rxJ5O9npwXIhBfVvwCUbVd+h2CZ9zYyHW/cMELZzIt++FV5p9B9sVW+WVv0Z" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close database connection if required
// $conn->close();
?>
