<?php

include 'db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        $check = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";

        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO user (name, surname, email, password, phone_number)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $name,
                $surname,
                $email,
                $hashed_password,
                $phone_number
            );

            if ($stmt->execute()) {

                header("Location: login.php?signup=success");
                exit();

            } else {

                $message = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up | Count4U</title>

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

            <div class="col-lg-6 col-md-8">

                <div class="auth-card">

                    <div class="text-center mb-4">

                        <div class="auth-logo">
                            Count<span>4U</span>
                        </div>

                        <h2 class="auth-title mt-3">
                            Create Your Account
                        </h2>

                        <p class="auth-subtitle">
                            Start managing your shop with Count4U.
                        </p>

                    </div>

                    <?php if ($message != ""): ?>

                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST" action="signup.php">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Enter your name"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Surname
                                </label>

                                <input
                                    type="text"
                                    name="surname"
                                    class="form-control"
                                    placeholder="Enter your surname"
                                    required
                                >

                            </div>

                        </div>

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

                        <div class="mb-3">

                            <label class="form-label">
                                Phone Number
                            </label>

                            <input
                                type="tel"
                                name="phone_number"
                                class="form-control"
                                placeholder="Enter your phone number"
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Create a password"
                                required
                            >

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                placeholder="Confirm your password"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-green w-100 py-3"
                        >
                            Create Account
                        </button>

                    </form>

                    <div class="text-center mt-4">

                        <p class="mb-0">

                            Already have an account?

                            <a href="login.php" class="auth-link">
                                Login
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