<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";
$error = "";


// Add stock
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $product_id = (int) ($_POST["product_id"] ?? 0);
    $quantity_added = (int) ($_POST["quantity_added"] ?? 0);

    if ($product_id <= 0 || $quantity_added <= 0) {

        $error = "Please enter a valid product and quantity.";

    } else {

        try {

            $stmt = $conn->prepare("
                UPDATE product
                SET quantity = quantity + :quantity_added
                WHERE product_id = :product_id
            ");

            $stmt->execute([
                ':quantity_added' => $quantity_added,
                ':product_id' => $product_id
            ]);

            if ($stmt->rowCount() > 0) {

                $message = "Stock updated successfully.";

            } else {

                $error = "Product not found.";
            }

        } catch (PDOException $e) {

            $error = "Could not update stock.";
        }
    }
}


// Categories
$stmt = $conn->query("
    SELECT category_id, category_name
    FROM category
    ORDER BY category_name
");

$categories = $stmt->fetchAll();


// Products
$stmt = $conn->query("
    SELECT
        product_id,
        product_name,
        category_id,
        selling_price,
        quantity,
        low_stock_level,
        image_path
    FROM product
    ORDER BY product_name
");

$products = $stmt->fetchAll();

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


    <?php if ($message !== ""): ?>

        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
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


            <?php foreach ($categories as $category): ?>

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

            <?php endforeach; ?>

        </div>


        <div class="row g-4 mt-3">

            <?php foreach ($products as $product): ?>

                <div
                    class="col-6 col-md-4 col-lg-3 product-column"
                    data-category="<?php echo $product["category_id"]; ?>"
                >

                    <div
                        class="product-card"
                        onclick="selectProduct(
                            <?php echo $product['product_id']; ?>,
                            <?php echo htmlspecialchars(
                                json_encode($product['product_name']),
                                ENT_QUOTES
                            ); ?>,
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

            <?php endforeach; ?>

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