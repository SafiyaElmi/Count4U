<?php

session_start();

include 'db_connection.php';


if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["name"];

$today_sales = 0;

$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(total_amount), 0)
     FROM sale
     WHERE user_id = ?
     AND DATE(sale_date) = CURDATE()"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($today_sales);
$stmt->fetch();
$stmt->close();

$items_sold = 0;

$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(si.quantity), 0)
     FROM sale_item si
     INNER JOIN sale s ON si.sale_id = s.sale_id
     WHERE s.user_id = ?
     AND DATE(s.sale_date) = CURDATE()"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($items_sold);
$stmt->fetch();
$stmt->close();

$low_stock_count = 0;

$result = $conn->query(
    "SELECT COUNT(*)
     FROM product
     WHERE quantity <= low_stock_level"
);

if ($result) {
    $row = $result->fetch_row();
    $low_stock_count = $row[0];
}

$total_products = 0;

$result = $conn->query(
    "SELECT COUNT(*) FROM product"
);

if ($result) {
    $row = $result->fetch_row();
    $total_products = $row[0];
}

$low_stock_products = $conn->query(
    "SELECT product_name, quantity, low_stock_level
     FROM product
     WHERE quantity <= low_stock_level
     ORDER BY quantity ASC
     LIMIT 5"
);

$best_sellers = $conn->query(
    "SELECT
        p.product_name,
        SUM(si.quantity) AS total_sold
     FROM sale_item si
     INNER JOIN product p
        ON si.product_id = p.product_id
     INNER JOIN sale s
        ON si.sale_id = s.sale_id
     WHERE s.user_id = $user_id
     GROUP BY p.product_id, p.product_name
     ORDER BY total_sold DESC
     LIMIT 5"
);

$slow_sellers = $conn->query(
    "SELECT
        p.product_name,
        COALESCE(SUM(
            CASE
                WHEN s.user_id = $user_id
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
     LIMIT 5"
);

$recent_sales = $conn->prepare(
    "SELECT sale_id, sale_date, total_amount
     FROM sale
     WHERE user_id = ?
     ORDER BY sale_date DESC
     LIMIT 5"
);

$recent_sales->bind_param("i", $user_id);
$recent_sales->execute();

$recent_sales_result = $recent_sales->get_result();

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

                    <?php if ($low_stock_products && $low_stock_products->num_rows > 0): ?>

                        <?php while ($product = $low_stock_products->fetch_assoc()): ?>

                            <div class="stock-row">

                                <span class="stock-name">
                                    <?php echo htmlspecialchars($product["product_name"]); ?>
                                </span>

                                <span class="stock-number">
                                    <?php echo $product["quantity"]; ?> left
                                </span>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <p class="text-muted">
                            All products have enough stock.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

            <div class="col-lg-4 col-md-12">

                <div class="content-card">

                    <h4>
                        🏆 Best Sellers
                    </h4>

                    <?php if ($best_sellers && $best_sellers->num_rows > 0): ?>

                        <div class="chart-container">
                            <canvas id="bestSellersChart"></canvas>
                        </div>

                    <?php else: ?>

                        <p class="text-muted">
                            No sales recorded yet.
                        </p>

                    <?php endif; ?>

                </div>

            </div>
            
            <div class="col-lg-4 col-md-12">
                
                <div class="content-card">
                    
                    <h4>
                        📉 Slow Sellers
                    </h4>

                    <?php if ($slow_sellers && $slow_sellers->num_rows > 0): ?>
                    
                        <div class="chart-container">
                            <canvas id="slowSellersChart"></canvas>
                        </div>
                    
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

                    <?php if ($recent_sales_result->num_rows > 0): ?>

                        <?php while ($sale = $recent_sales_result->fetch_assoc()): ?>

                            <div class="sale-row">

                                <div>

                                    <div class="sale-id">
                                        Sale #<?php echo $sale["sale_id"]; ?>
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

                        <?php endwhile; ?>

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
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const productNames = [
        <?php
        $best_sellers->data_seek(0);
        while ($product = $best_sellers->fetch_assoc()) {
            echo json_encode($product["product_name"]) . ",";
        }
        ?>
    ];

    const productsSold = [
        <?php
        $best_sellers->data_seek(0);
        while ($product = $best_sellers->fetch_assoc()) {
            echo $product["total_sold"] . ",";
        }
        ?>
    ];
    
    const slowProductNames = [
        <?php
        $slow_sellers->data_seek(0);
        while ($product = $slow_sellers->fetch_assoc()) {
            echo json_encode($product["product_name"]) . ",";
        }
        ?>
    ];

    const slowProductsSold = [
        <?php
        $slow_sellers->data_seek(0);
        while ($product = $slow_sellers->fetch_assoc()) {
            echo $product["total_sold"] . ",";
        }
        ?>
    ];

    new Chart(document.getElementById('bestSellersChart'), {
        type: 'bar',

        data: {
            labels: productNames,

            datasets: [{
                data: productsSold,

                backgroundColor: '#20A464',
                borderColor: '#20A464',
                borderWidth: 1,
                borderRadius: 6
            }]
        },

        options: {
            responsive: true,

            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    new Chart(document.getElementById('slowSellersChart'), {
        type: 'bar',

        data: {
            labels: slowProductNames,

            datasets: [{
                data: slowProductsSold,

                backgroundColor: '#0B1F3A',
                borderColor: '#0B1F3A',
                borderWidth: 1,
                borderRadius: 6
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },

                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
    
</html>