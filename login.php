<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: users_list.php");
   exit();
}

require_once "database.php";

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Fetch user from database based on email
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Verify password
        if (password_verify($password, $user["password"])) {
            // Password is correct, start session and redirect to users list
            $_SESSION["user"] = $user; // Store user details in session
            header("Location: user_list.php");
            exit(); // Ensure script stops executing after redirection
        } else {
            $error = "Password does not match.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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
                        <h2 class="card-title text-center mb-4">Login</h2>
                        <?php if (isset($error)) : ?>
                            <div class='alert alert-danger'><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-secondary" name="login">Login</button>
                            </div>
                        </form>
                        <p class="mt-3 text-center">Not registered yet? <a href="registration.php">Register here</a></p>
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
