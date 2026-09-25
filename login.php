<?php
session_start();
include 'db_connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";
    } else {

        try {
            $stmt = $conn->prepare("
                SELECT user_id, name, surname, email, password
                FROM \"user\"
                WHERE email = :email
            ");

            $stmt->execute([
                ':email' => $email
            ]);

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["surname"] = $user["surname"];
                $_SESSION["email"] = $user["email"];

                header("Location: dashboard.php");
                exit;

            } else {
                $error = "Invalid email or password.";
            }

        } catch (PDOException $e) {
            $error = "Login failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Count4U</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<div class="container">

    <div class="auth-container">

        <div class="text-center mb-4">
            <h1>Count4U</h1>
            <p>Login to your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET["signup"]) && $_GET["signup"] === "success"): ?>
            <div class="alert alert-success">
                Account created successfully. You can now log in.
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>

                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>

                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>

        </form>

        <div class="text-center mt-3">
            <p>
                Don't have an account?
                <a href="signup.php">Sign Up</a>
            </p>
        </div>

    </div>

</div>

</body>
</html>