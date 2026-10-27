<?php

session_start();

include 'db_connection.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = (int)$_POST["product_id"];
    $quantity_added = (int)$_POST["quantity_added"];

    if ($quantity_added <= 0) {

        $message = "Please enter a valid quantity.";
        $message_type = "danger";

    } else {

        $stmt = $conn->prepare(
            "UPDATE product
             SET quantity = quantity + ?
             WHERE product_id = ?"
        );

        $stmt->bind_param(
            "ii",
            $quantity_added,
            $product_id
        );


        if ($stmt->execute() && $stmt->affected_rows > 0) {

            $message = "Stock updated successfully!";
            $message_type = "success";

        } else {

            $message = "Product could not be updated.";
            $message_type = "danger";
        }

        $stmt->close();
    }
}

$categories = $conn->query(
    "SELECT category_id, category_name
     FROM category
     ORDER BY category_name"
);

$products = $conn->query(
    "SELECT
        p.product_id,
        p.product_name,
        p.selling_price,
        p.quantity,
        p.category_id,
        p.image_path,
        c.category_name
     FROM product p
     INNER JOIN category c
        ON p.category_id = c.category_id
     ORDER BY p.product_name"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Stock Up | Count4U</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="styles.css">

</head>

<body>

<nav class="shop-navbar">

    <div class="container d-flex justify-content-between align-items-center">

        <a href="dashboard.php" class="logo">
            Count<span>4U</span>
        </a>

        <a href="dashboard.php"
           class="btn btn-outline-light btn-sm">
            ← Dashboard
        </a>

    </div>

</nav>

<div class="container page-container">

    <h1 class="page-title mb-1">
        Stock Up
    </h1>

    <p class="text-muted mb-4">
        Select a product and add the quantity you purchased.
    </p>

    <?php if ($message != ""): ?>

        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <div id="productScreen" class="screen active">

        <h4 class="mb-3">
            Choose a Category
        </h4>

        <div>

            <button
                class="category-btn active"
                onclick="showAllProducts(this)">
                All
            </button>

            <?php while ($category = $categories->fetch_assoc()): ?>

                <button
                    class="category-btn"
                    onclick="showCategory(
                        <?php echo $category['category_id']; ?>,
                        this
                    )">

                    <?php echo htmlspecialchars(
                        $category["category_name"]
                    ); ?>

                </button>

            <?php endwhile; ?>

        </div>

        <div class="row g-4 mt-3">

            <?php while ($product = $products->fetch_assoc()): ?>

                <div
                    class="col-6 col-md-4 col-lg-3 product-column"
                    data-category="<?php echo $product["category_id"]; ?>"
                >

                    <div
                        class="product-card"
                        onclick="selectProduct(
                            <?php echo $product['product_id']; ?>,
                            <?php echo htmlspecialchars(json_encode($product['product_name']), ENT_QUOTES); ?>,
                            <?php echo $product['quantity']; ?>
                        )"
                    >

                        <?php if (!empty($product["image_path"])): ?>

                            <img
                                src="<?php echo htmlspecialchars(
                                    $product["image_path"]
                                ); ?>"
                                class="product-image"
                                alt="<?php echo htmlspecialchars(
                                    $product["product_name"]
                                ); ?>"
                            >

                        <?php else: ?>

                            <div class="product-placeholder">
                                📦
                            </div>

                        <?php endif; ?>

                        <div class="product-name">
                            <?php echo htmlspecialchars(
                                $product["product_name"]
                            ); ?>
                        </div>

                        <div class="stock-text">
                            Current stock:
                            <?php echo $product["quantity"]; ?>
                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

    <div id="stockScreen" class="screen">

        <button
            class="btn btn-outline-secondary mb-4"
            onclick="showProducts()">

            ← Choose Another Product

        </button>

        <div class="stock-box">

            <h3 class="text-center mb-4">
                📦 Add Stock
            </h3>

            <div class="selected-product">

                <h4 id="selectedProductName">
                    Product
                </h4>

                <span class="current-stock">
                    Current stock:
                    <span id="selectedProductStock">0</span>
                </span>

            </div>

            <form method="POST">

                <input
                    type="hidden"
                    name="product_id"
                    id="productId"
                >

                <label class="form-label fw-bold">
                    How many did you buy?
                </label>

                <input
                    type="number"
                    name="quantity_added"
                    id="quantityAdded"
                    class="form-control quantity-input"
                    min="1"
                    placeholder="Enter quantity"
                    required
                >

                <button
                    type="submit"
                    class="btn btn-success w-100 py-3 mt-4"
                >
                    Add to Stock
                </button>

            </form>

        </div>

    </div>

</div>

<script src="script.js" defer></script>

</body>
</html>