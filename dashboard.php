<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

try {

    // Today's sales
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


    // Items sold today
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


    // Low stock count
    $stmt = $conn->query("
        SELECT COUNT(*) AS total
        FROM product
        WHERE quantity <= low_stock_level
    ");

    $low_stock_count = $stmt->fetch()["total"];


    // Total products
    $stmt = $conn->query("
        SELECT COUNT(*) AS total
        FROM product
    ");

    $total_products = $stmt->fetch()["total"];


    // Low stock products
    $stmt = $conn->query("
        SELECT product_name, quantity, low_stock_level
        FROM product
        WHERE quantity <= low_stock_level
        ORDER BY quantity ASC
        LIMIT 5
    ");

    $low_stock_products = $stmt->fetchAll();


    // Best sellers
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


    // Slow sellers
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


    // Recent sales
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - Count4U</title>

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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
            Count4U
        </a>

        <div class="d-flex align-items-center">

            <span class="text-white me-3">
                Welcome,
                <?= htmlspecialchars($_SESSION["name"] ?? "") ?>
            </span>

            <a href="logout.php" class="btn btn-outline-light">
                Logout
            </a>

        </div>

    </div>

</nav>


<div class="container mt-4">

    <h1 class="mb-4">
        Dashboard
    </h1>


    <!-- Statistics -->

    <div class="row g-4 mb-4">

        <div class="col-md-3">

            <div class="card p-3">

                <h6>Today's Sales</h6>

                <h3>
                    R<?= number_format((float)$today_sales, 2) ?>
                </h3>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card p-3">

                <h6>Items Sold Today</h6>

                <h3>
                    <?= (int)$items_sold ?>
                </h3>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card p-3">

                <h6>Low Stock</h6>

                <h3>
                    <?= (int)$low_stock_count ?>
                </h3>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card p-3">

                <h6>Total Products</h6>

                <h3>
                    <?= (int)$total_products ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="row g-4">


        <!-- Low stock -->

        <div class="col-md-6">

            <div class="card p-3">

                <h4>Low Stock Products</h4>

                <?php if (empty($low_stock_products)): ?>

                    <p>No products are currently low in stock.</p>

                <?php else: ?>

                    <ul class="list-group">

                        <?php foreach ($low_stock_products as $product): ?>

                            <li class="list-group-item d-flex justify-content-between">

                                <span>
                                    <?= htmlspecialchars($product["product_name"]) ?>
                                </span>

                                <span>
                                    <?= (int)$product["quantity"] ?>
                                </span>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

            </div>

        </div>


        <!-- Best sellers -->

        <div class="col-md-6">

            <div class="card p-3">

                <h4>Best Sellers</h4>

                <?php if (empty($best_sellers)): ?>

                    <p>No sales recorded yet.</p>

                <?php else: ?>

                    <ul class="list-group">

                        <?php foreach ($best_sellers as $product): ?>

                            <li class="list-group-item d-flex justify-content-between">

                                <span>
                                    <?= htmlspecialchars($product["product_name"]) ?>
                                </span>

                                <span>
                                    <?= (int)$product["total_sold"] ?> sold
                                </span>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

            </div>

        </div>


        <!-- Slow sellers -->

        <div class="col-md-6">

            <div class="card p-3">

                <h4>Slow Sellers</h4>

                <?php if (empty($slow_sellers)): ?>

                    <p>No products found.</p>

                <?php else: ?>

                    <ul class="list-group">

                        <?php foreach ($slow_sellers as $product): ?>

                            <li class="list-group-item d-flex justify-content-between">

                                <span>
                                    <?= htmlspecialchars($product["product_name"]) ?>
                                </span>

                                <span>
                                    <?= (int)$product["total_sold"] ?> sold
                                </span>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

            </div>

        </div>


        <!-- Recent sales -->

        <div class="col-md-6">

            <div class="card p-3">

                <h4>Recent Sales</h4>

                <?php if (empty($recent_sales)): ?>

                    <p>No sales recorded yet.</p>

                <?php else: ?>

                    <ul class="list-group">

                        <?php foreach ($recent_sales as $sale): ?>

                            <li class="list-group-item">

                                <div>
                                    Sale #<?= (int)$sale["sale_id"] ?>
                                </div>

                                <small>
                                    <?= htmlspecialchars($sale["sale_date"]) ?>
                                </small>

                                <strong>
                                    R<?= number_format((float)$sale["total_amount"], 2) ?>
                                </strong>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

</body>

</html>