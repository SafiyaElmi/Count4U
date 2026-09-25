<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Count4U</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="styles.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            Count<span>4U</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>

                <li class="nav-item ms-lg-3">
                    <a href="login.php" class="btn btn-outline-light">
                        Login
                    </a>
                </li>

                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a href="signup.php" class="btn btn-green">
                        Sign Up
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>

<section class="hero-section">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <span class="hero-label">
                    SIMPLE. SMART. COUNTED.
                </span>

                <h1>
                    Your Shop,<br>
                    <span>Counted For You.</span>
                </h1>

                <p class="hero-text">
                    Count4U helps small shops manage their sales and stock
                    in one simple place. Sell products, keep track of stock,
                    and understand what is selling in your shop.
                </p>

                <div class="hero-buttons">

                    <a href="signup.php" class="btn btn-green btn-lg">
                        Get Started
                    </a>

                    <a href="login.php" class="btn btn-outline-navy btn-lg">
                        Login
                    </a>

                </div>

            </div>

            <div class="col-lg-6 mt-5 mt-lg-0">

                <div class="dashboard-preview">

                    <div class="preview-header">
                        <div>
                            <small>Today's Sales</small>
                            <h3>R2,450.00</h3>
                        </div>

                        <div class="sales-icon">
                            ✓
                        </div>
                    </div>

                    <hr>

                    <div class="preview-stats">

                        <div class="preview-card">
                            <small>Items Sold</small>
                            <strong>86</strong>
                        </div>

                        <div class="preview-card">
                            <small>Low Stock</small>
                            <strong class="green-text">4</strong>
                        </div>

                    </div>

                    <div class="stock-preview">

                        <div class="stock-title">
                            <span>Stock Overview</span>
                            <span class="green-text">View All</span>
                        </div>

                        <div class="stock-item">
                            <span>🥖 Bread</span>
                            <span>24</span>
                        </div>

                        <div class="stock-item">
                            <span>🥤 Drinks</span>
                            <span>18</span>
                        </div>

                        <div class="stock-item low-stock">
                            <span>🥛 Milk</span>
                            <span>4 left</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section id="features" class="features-section">

    <div class="container">

        <div class="text-center section-heading">

            <span class="section-label">
                WHAT COUNTS
            </span>

            <h2>
                Everything Your Shop Needs
            </h2>

            <p>
                Keep your sales and stock organised without making
                things complicated.
            </p>

        </div>

        <div class="row g-4">

            <!-- Feature 1 -->
            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        🛒
                    </div>

                    <h4>Sell With Ease</h4>

                    <p>
                        Select products, add them to the cart and
                        complete the sale. Count4U calculates the
                        total and updates your stock.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        📦
                    </div>

                    <h4>Keep Track of Stock</h4>

                    <p>
                        Quickly see how much stock you have and
                        add newly purchased stock without complicated
                        stock records.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        📊
                    </div>

                    <h4>Understand Your Sales</h4>

                    <p>
                        See your sales performance and identify
                        products that sell quickly or slowly.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="how-section">

    <div class="container">

        <div class="text-center section-heading">

            <span class="section-label">
                HOW IT WORKS
            </span>

            <h2>
                Simple From Start to Finish
            </h2>

        </div>

        <div class="row text-center g-4">

            <div class="col-md-4">

                <div class="step-number">1</div>

                <h4>Set Up Your Shop</h4>

                <p>
                    Sign up and get your products and stock ready.
                </p>

            </div>

            <div class="col-md-4">

                <div class="step-number">2</div>

                <h4>Make Sales</h4>

                <p>
                    Select products and complete customer purchases.
                </p>

            </div>


            <div class="col-md-4">

                <div class="step-number">3</div>

                <h4>Count4U Does the Rest</h4>

                <p>
                    Sales are recorded and stock is updated automatically.
                </p>

            </div>

        </div>

    </div>

</section>

<section class="cta-section">

    <div class="container text-center">

        <h2>
            Ready to simplify your shop?
        </h2>

        <p>
            Let Count4U keep track while you focus on your customers.
        </p>

        <a href="signup.php" class="btn btn-green btn-lg">
            Create Your Account
        </a>

    </div>

</section>

<footer class="footer">

    <div class="container text-center">

        <h5>
            Count<span>4U</span>
        </h5>

        <p>
            Smart Stock & Sales Management
        </p>

        <small>
            © 2026 Count4U. All rights reserved.
        </small>

    </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>