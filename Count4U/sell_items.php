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

    $cart = json_decode($_POST["cart"], true);

    if (!empty($cart)) {

        $user_id = $_SESSION["user_id"];

        // Start transaction
        $conn->begin_transaction();

        try {

            $total_amount = 0;

            foreach ($cart as $item) {

                $product_id = (int)$item["product_id"];
                $quantity = (int)$item["quantity"];

                $stmt = $conn->prepare(
                    "SELECT selling_price, quantity
                     FROM product
                     WHERE product_id = ?"
                );

                $stmt->bind_param("i", $product_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows != 1) {
                    throw new Exception("Product not found.");
                }

                $product = $result->fetch_assoc();

                if ($quantity > $product["quantity"]) {
                    throw new Exception(
                        "Not enough stock available."
                    );
                }

                $total_amount +=
                    $product["selling_price"] * $quantity;

                $stmt->close();
            }

            $stmt = $conn->prepare(
                "INSERT INTO sale (user_id, total_amount)
                 VALUES (?, ?)"
            );

            $stmt->bind_param(
                "id",
                $user_id,
                $total_amount
            );

            $stmt->execute();

            $sale_id = $conn->insert_id;

            $stmt->close();

            foreach ($cart as $item) {

                $product_id = (int)$item["product_id"];
                $quantity = (int)$item["quantity"];

                // Get current price
                $stmt = $conn->prepare(
                    "SELECT selling_price
                     FROM product
                     WHERE product_id = ?"
                );

                $stmt->bind_param("i", $product_id);
                $stmt->execute();

                $result = $stmt->get_result();
                $product = $result->fetch_assoc();

                $unit_price = $product["selling_price"];
                $subtotal = $unit_price * $quantity;

                $stmt->close();

                $stmt = $conn->prepare(
                    "INSERT INTO sale_item
                    (sale_id, product_id, quantity, unit_price, subtotal)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "iiidd",
                    $sale_id,
                    $product_id,
                    $quantity,
                    $unit_price,
                    $subtotal
                );

                $stmt->execute();

                $stmt->close();

                $stmt = $conn->prepare(
                    "UPDATE product
                     SET quantity = quantity - ?
                     WHERE product_id = ?"
                );

                $stmt->bind_param(
                    "ii",
                    $quantity,
                    $product_id
                );

                $stmt->execute();

                $stmt->close();
            }

            $conn->commit();

            $message = "Sale completed successfully!";
            $message_type = "success";

        } catch (Exception $e) {

            $conn->rollback();

            $message = $e->getMessage();
            $message_type = "danger";
        }
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

    <title>Sell Items | Count4U</title>

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
        Sell Items
    </h1>

    <p class="text-muted mb-4">
        Select products and add them to the customer's cart.
    </p>

    <?php if ($message != ""): ?>

        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <div id="categoryScreen" class="screen active">

        <h4 class="mb-3">
            Choose a Category
        </h4>

        <div>

            <button
                class="category-btn active"
                onclick="showAllProducts()">
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

        <div class="row g-4 mt-3" id="productGrid">

            <?php while ($product = $products->fetch_assoc()): ?>

                <div
                    class="col-6 col-md-4 col-lg-3 product-column"
                    data-category="<?php echo $product["category_id"]; ?>"
                >

                    <div
                        class="product-card"
                        onclick="addProduct(
                            <?php echo $product['product_id']; ?>,
                            <?php echo htmlspecialchars(json_encode($product['product_name']), ENT_QUOTES); ?>,
                            <?php echo $product['selling_price']; ?>,
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
                                🛍️
                            </div>

                        <?php endif; ?>


                        <div class="product-name">
                            <?php echo htmlspecialchars(
                                $product["product_name"]
                            ); ?>
                        </div>

                        <div class="product-price">
                            R<?php echo number_format(
                                $product["selling_price"],
                                2
                            ); ?>
                        </div>

                        <div class="stock-text">
                            <?php echo $product["quantity"]; ?> available
                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

    <div id="cartScreen" class="screen">

        <button
            class="btn btn-outline-secondary mb-4"
            onclick="showCategories()">

            ← Continue Shopping

        </button>

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="cart-box">

                    <h3 class="mb-4">
                        🛒 Current Sale
                    </h3>

                    <div id="cartItems">

                        <p class="text-muted">
                            Your cart is empty.
                        </p>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">

                        <span class="total">
                            Total
                        </span>

                        <span class="total" id="cartTotal">
                            R0.00
                        </span>

                    </div>

                    <form
                        method="POST"
                        action="sell_items.php"
                        onsubmit="prepareSale()"
                        class="mt-4"
                    >

                        <input
                            type="hidden"
                            name="cart"
                            id="cartInput"
                        >

                        <button
                            type="submit"
                            class="btn btn-success w-100 py-3"
                            id="checkoutButton"
                            disabled
                        >
                            Complete Sale
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="script.js" defer></script>

</body>
</html>