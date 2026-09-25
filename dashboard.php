<?php

session_start();

include 'db_connection.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$user_name = $_SESSION["name"] ?? "User";

try {

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_amount), 0) AS total
        FROM sale
        WHERE user_id = :user_id
        AND sale_date::date = CURRENT_DATE
    ");

    $stmt->execute([
        ':user_id' => $user_id
    ]);

    $today_sales = $stmt->fetch()["total"];

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(si.quantity), 0) AS total
        FROM sale_item si
        JOIN sale s ON si.sale_id = s.sale_id
        WHERE s.user_id = :user_id
        AND s.sale_date::date = CURRENT_DATE
    ");

    $stmt->execute([
        ':user_id' => $user_id
    ]);

    $items_sold = $stmt->fetch()["total"];

    $stmt = $conn->query("
        SELECT COUNT(*) AS total
        FROM product
        WHERE quantity <= low_stock_level
    ");

    $low_stock_count = $stmt->fetch()["total"];

    $stmt = $conn->query("
        SELECT COUNT(*) AS total
        FROM product
    ");

    $total_products = $stmt->fetch()["total"];

    $stmt = $conn->query("
        SELECT product_name, quantity, low_stock_level
        FROM product
        WHERE quantity <= low_stock_level
        ORDER BY quantity ASC
        LIMIT 5
    ");

    $low_stock_products = $stmt->fetchAll();

    $stmt = $conn->prepare("
        SELECT
            p.product_name,
            SUM(si.quantity) AS total_sold
        FROM sale_item si
        JOIN product p
            ON si.product_id = p.product_id
        JOIN sale s
            ON si.sale_id = s.sale_id
        WHERE s.user_id = :user_id
        GROUP BY p.product_id, p.product_name
        ORDER BY total_sold DESC
        LIMIT 5
    ");

    $stmt->execute([
        ':user_id' => $user_id
    ]);

    $best_sellers = $stmt->fetchAll();

    $stmt = $conn->prepare("
        SELECT
            p.product_name,
            COALESCE(SUM(
                CASE
                    WHEN s.user_id = :user_id
                    THEN si.quantity
                    ELSE 0
                END
            ), 0) AS total_sold
        FROM product p
        LEFT JOIN sale_item si
            ON p.product_id = si.product_id
        LEFT JOIN sale s
            ON si.sale_id = s.sale_id
        GROUP BY p.product_id, p.product_name
        ORDER BY total_sold ASC
        LIMIT 5
    ");

    $stmt->execute([
        ':user_id' => $user_id
    ]);

    $slow_sellers = $stmt->fetchAll();

    $stmt = $conn->prepare("
        SELECT sale_id, sale_date, total_amount
        FROM sale
        WHERE user_id = :user_id
        ORDER BY sale_date DESC
        LIMIT 5
    ");

    $stmt->execute([
        ':user_id' => $user_id
    ]);

    $recent_sales = $stmt->fetchAll();


} catch (PDOException $e) {

    die("Dashboard error: " . htmlspecialchars($e->getMessage()));

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Count4U</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="styles.css">

</head>

<body>

<nav class="dashboard-navbar">

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">

            <a
                href="dashboard.php"
                class="dashboard-logo text-decoration-none"
            >
                Count<span>4U</span>
            </a>

            <div class="d-flex align-items-center gap-3">

                <span class="welcome-text d-none d-md-block">
                    Hi, <?php echo htmlspecialchars($user_name); ?>
                </span>

                <a
                    href="logout.php"
                    class="btn btn-outline-light btn-sm"
                >
                    Logout
                </a>

            </div>

        </div>

    </div>

</nav>

<main class="dashboard-container">

    <div class="container">

        <div class="mb-4">

            <h1 class="dashboard-title">
                Good day, <?php echo htmlspecialchars($user_name); ?> 👋
            </h1>

            <p class="dashboard-subtitle">
                Here's what's happening in your shop today.
            </p>

        </div>

        <div class="row g-4 mb-4">

            <div class="col-md-6">

                <div class="action-card">

                    <div class="action-icon">
                        🛒
                    </div>

                    <h4>
                        Sell Items
                    </h4>

                    <p>
                        Select products, add them to the cart
                        and complete a sale.
                    </p>

                    <a
                        href="sell_items.php"
                        class="btn action-btn"
                    >
                        Start Selling →
                    </a>

                </div>

            </div>

            <div class="col-md-6">

                <div class="action-card green">

                    <div class="action-icon">
                        📦
                    </div>

                    <h4>
                        Stock Up
                    </h4>

                    <p>
                        Add newly purchased stock to your products.
                    </p>

                    <a
                        href="stock_up.php"
                        class="btn action-btn"
                    >
                        Add Stock →
                    </a>

                </div>

            </div>

        </div>

        <div class="row g-4 mb-4">

            <div class="col-md-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        💰
                    </div>

                    <div class="stat-label">
                        Today's Sales
                    </div>

                    <div class="stat-value">
                        R<?php echo number_format($today_sales, 2); ?>
                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        🛍️
                    </div>

                    <div class="stat-label">
                        Items Sold Today
                    </div>

                    <div class="stat-value">
                        <?php echo $items_sold; ?>
                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        ⚠️
                    </div>

                    <div class="stat-label">
                        Low Stock
                    </div>

                    <div class="stat-value">
                        <?php echo $low_stock_count; ?>
                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        📦
                    </div>

                    <div class="stat-label">
                        Total Products
                    </div>

                    <div class="stat-value">
                        <?php echo $total_products; ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="row g-4 mb-4">

            <div class="col-lg-4">

                <div class="content-card">

                    <h4>
                        ⚠️ Low Stock
                    </h4>

                    <?php if (!empty($low_stock_products)): ?>

                        <?php foreach ($low_stock_products as $product): ?>

                            <div class="stock-row">

                                <span class="stock-name">
                                    <?php
                                    echo htmlspecialchars(
                                        $product["product_name"]
                                    );
                                    ?>
                                </span>

                                <span class="stock-number">
                                    <?php
                                    echo $product["quantity"];
                                    ?>
                                    left
                                </span>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="text-muted">
                            All products have enough stock.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="content-card">

                    <h4>
                        🏆 Best Sellers
                    </h4>

                    <?php if (!empty($best_sellers)): ?>

                        <?php foreach ($best_sellers as $product): ?>

                            <div class="product-row">

                                <span class="product-name">
                                    <?php
                                    echo htmlspecialchars(
                                        $product["product_name"]
                                    );
                                    ?>
                                </span>

                                <span class="product-number">
                                    <?php
                                    echo $product["total_sold"];
                                    ?>
                                    sold
                                </span>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="text-muted">
                            No sales recorded yet.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="content-card">

                    <h4>
                        📉 Slow Sellers
                    </h4>

                    <?php if (!empty($slow_sellers)): ?>

                        <?php foreach ($slow_sellers as $product): ?>

                            <div class="product-row">

                                <span class="product-name">
                                    <?php
                                    echo htmlspecialchars(
                                        $product["product_name"]
                                    );
                                    ?>
                                </span>

                                <span class="product-number">
                                    <?php
                                    echo $product["total_sold"];
                                    ?>
                                    sold
                                </span>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="text-muted">
                            No sales recorded yet.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-12">

                <div class="content-card">

                    <h4>
                        🧾 Recent Sales
                    </h4>

                    <?php if (!empty($recent_sales)): ?>

                        <?php foreach ($recent_sales as $sale): ?>

                            <div class="sale-row">

                                <div>

                                    <div class="sale-id">
                                        Sale #<?php
                                        echo $sale["sale_id"];
                                        ?>
                                    </div>

                                    <div class="sale-date">

                                        <?php
                                        echo date(
                                            "d M Y, H:i",
                                            strtotime($sale["sale_date"])
                                        );
                                        ?>

                                    </div>

                                </div>

                                <div class="sale-amount">

                                    R<?php
                                    echo number_format(
                                        $sale["total_amount"],
                                        2
                                    );
                                    ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="text-center py-4">

                            <p class="text-muted mb-0">
                                No sales have been recorded yet.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</main>

</body>
</html>
