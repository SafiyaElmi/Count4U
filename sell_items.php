<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cart"])) {

    $cart = json_decode($_POST["cart"], true);

    if (!is_array($cart) || empty($cart)) {

        $error = "Your cart is empty.";

    } else {

        try {

            $conn->beginTransaction();

            $total_amount = 0;
            $sale_items = [];

            // Check products and calculate total
            foreach ($cart as $item) {

                $product_id = (int) $item["product_id"];
                $quantity = (int) $item["quantity"];

                if ($quantity <= 0) {
                    throw new Exception("Invalid quantity.");
                }

                $stmt = $conn->prepare("
                    SELECT selling_price, quantity
                    FROM product
                    WHERE product_id = :product_id
                    FOR UPDATE
                ");

                $stmt->execute([
                    ':product_id' => $product_id
                ]);

                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception("Product not found.");
                }

                if ($quantity > $product["quantity"]) {
                    throw new Exception("Not enough stock available.");
                }

                $unit_price = (float) $product["selling_price"];
                $subtotal = $unit_price * $quantity;

                $total_amount += $subtotal;

                $sale_items[] = [
                    "product_id" => $product_id,
                    "quantity" => $quantity,
                    "unit_price" => $unit_price,
                    "subtotal" => $subtotal
                ];
            }

            // Create sale
            $stmt = $conn->prepare("
                INSERT INTO sale
                    (user_id, total_amount)
                VALUES
                    (:user_id, :total_amount)
                RETURNING sale_id
            ");

            $stmt->execute([
                ':user_id' => $user_id,
                ':total_amount' => $total_amount
            ]);

            $sale_id = $stmt->fetchColumn();

            // Add sale items and update stock
            foreach ($sale_items as $item) {

                $stmt = $conn->prepare("
                    INSERT INTO sale_item
                    (
                        sale_id,
                        product_id,
                        quantity,
                        unit_price,
                        subtotal
                    )
                    VALUES
                    (
                        :sale_id,
                        :product_id,
                        :quantity,
                        :unit_price,
                        :subtotal
                    )
                ");

                $stmt->execute([
                    ':sale_id' => $sale_id,
                    ':product_id' => $item["product_id"],
                    ':quantity' => $item["quantity"],
                    ':unit_price' => $item["unit_price"],
                    ':subtotal' => $item["subtotal"]
                ]);

                // Reduce stock
                $stmt = $conn->prepare("
                    UPDATE product
                    SET quantity = quantity - :quantity
                    WHERE product_id = :product_id
                ");

                $stmt->execute([
                    ':quantity' => $item["quantity"],
                    ':product_id' => $item["product_id"]
                ]);
            }

            $conn->commit();

            $message = "Sale completed successfully.";

        } catch (Exception $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $error = $e->getMessage();
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


        <div class="row g-4 mt-3" id="productGrid">

            <?php foreach ($products as $product): ?>

                <div
                    class="col-6 col-md-4 col-lg-3 product-column"
                    data-category="<?php echo $product["category_id"]; ?>"
                >

                    <div
                        class="product-card"
                        onclick="addProduct(
                            <?php echo $product['product_id']; ?>,
                            <?php echo htmlspecialchars(
                                json_encode($product['product_name']),
                                ENT_QUOTES
                            ); ?>,
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

            <?php endforeach; ?>

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