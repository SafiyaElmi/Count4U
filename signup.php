<?php
include 'db_connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (
        empty($name) ||
        empty($surname) ||
        empty($email) ||
        empty($phone_number) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        try {

            // Check whether email already exists
            $stmt = $conn->prepare("
                SELECT user_id
                FROM \"user\"
                WHERE email = :email
            ");

            $stmt->execute([
                ':email' => $email
            ]);

            if ($stmt->fetch()) {

                $error = "An account with this email already exists.";

            } else {

                // Hash password
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Insert new user
                $stmt = $conn->prepare("
                    INSERT INTO \"user\"
                    (name, surname, email, password, phone_number)
                    VALUES
                    (:name, :surname, :email, :password, :phone_number)
                ");

                $stmt->execute([
                    ':name' => $name,
                    ':surname' => $surname,
                    ':email' => $email,
                    ':password' => $hashed_password,
                    ':phone_number' => $phone_number
                ]);

                header("Location: login.php?signup=success");
                exit;
            }

        } catch (PDOException $e) {

            $error = "Sign up failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up - Count4U</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="styles.css">

</head>

<body>

<div class="container">

    <div class="auth-container">

        <div class="text-center mb-4">

            <h1>Count4U</h1>

            <p>Create your account</p>

        </div>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label for="name" class="form-label">
                        Name
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label for="surname" class="form-label">
                        Surname
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="surname"
                        name="surname"
                        required
                    >

                </div>

            </div>

            <div class="mb-3">

                <label for="email" class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    required
                >

            </div>

            <div class="mb-3">

                <label for="phone_number" class="form-label">
                    Phone Number
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="phone_number"
                    name="phone_number"
                    required
                >

            </div>

            <div class="mb-3">

                <label for="password" class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    required
                >

            </div>

            <div class="mb-3">

                <label for="confirm_password" class="form-label">
                    Confirm Password
                </label>

                <input
                    type="password"
                    class="form-control"
                    id="confirm_password"
                    name="confirm_password"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary w-100"
            >
                Sign Up
            </button>

        </form>

        <div class="text-center mt-3">

            <p>
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>

    </div>

</div>

</body>

</html>