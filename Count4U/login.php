<?php

session_start();

include 'db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user
    $stmt = $conn->prepare(
        "SELECT user_id, name, surname, email, password
         FROM user
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["surname"] = $user["surname"];
            $_SESSION["email"] = $user["email"];

            header("Location: dashboard.php");
            exit();

        } else {

            $message = "Incorrect email or password.";
        }

    } else {

        $message = "Incorrect email or password.";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Count4U</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="styles.css">

</head>

<body>

<nav class="navbar navbar-dark custom-navbar">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            Count<span>4U</span>
        </a>

        <a href="index.php" class="back-home">
            ← Back to Home
        </a>

    </div>

</nav>

<section class="auth-section">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-5 col-md-7">

                <div class="auth-card">

                    <div class="text-center">

                        <div class="login-icon">
                            ✓
                        </div>

                        <h2 class="auth-title">
                            Welcome Back
                        </h2>

                        <p class="auth-subtitle mb-4">
                            Login to manage your shop.
                        </p>

                    </div>

                    <?php if (isset($_GET["signup"]) && $_GET["signup"] == "success"): ?>

                        <div class="alert alert-success">
                            Account created successfully. You can now login.
                        </div>

                    <?php endif; ?>

                    <?php if ($message != ""): ?>

                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>

                    <?php endif; ?>


                    <form method="POST" action="login.php">

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter your email"
                                required
                            >

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter your password"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-green w-100 py-3"
                        >
                            Login
                        </button>

                    </form>

                    <div class="text-center mt-4">

                        <p class="mb-0">

                            Don't have an account?

                            <a href="signup.php" class="auth-link">
                                Create Account
                            </a>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

</body>
</html>