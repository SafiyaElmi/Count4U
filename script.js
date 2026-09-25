let cart = [];

function showCategory(categoryId, button) {

    document.querySelectorAll(".category-btn")
        .forEach(btn => btn.classList.remove("active"));

    button.classList.add("active");

    document.querySelectorAll(".product-column")
        .forEach(product => {

            if (product.dataset.category == categoryId) {
                product.style.display = "block";
            } else {
                product.style.display = "none";
            }

        });
}

function showAllProducts() {

    document.querySelectorAll(".category-btn")
        .forEach(btn => btn.classList.remove("active"));

    event.target.classList.add("active");

    document.querySelectorAll(".product-column")
        .forEach(product => {
            product.style.display = "block";
        });
}

function addProduct(
    productId,
    productName,
    price,
    stock
) {

    if (stock <= 0) {
        alert("This product is out of stock.");
        return;
    }

    let existing = cart.find(
        item => item.product_id === productId
    );


    if (existing) {

        if (existing.quantity < stock) {
            existing.quantity++;
        } else {
            alert("You cannot sell more than the available stock.");
        }

    } else {

        cart.push({
            product_id: productId,
            product_name: productName,
            price: price,
            stock: stock,
            quantity: 1
        });

    }

    updateCart();

    showCart();

}

function updateCart() {

    let cartItems = document.getElementById("cartItems");

    let total = 0;

    cartItems.innerHTML = "";

    if (cart.length === 0) {

        cartItems.innerHTML =
            '<p class="text-muted">Your cart is empty.</p>';

        document.getElementById("checkoutButton").disabled = true;

    } else {

        cart.forEach((item, index) => {

            let subtotal =
                item.price * item.quantity;

            total += subtotal;

            cartItems.innerHTML += `

                <div class="cart-item">

                    <div>

                        <strong>
                            ${item.product_name}
                        </strong>

                        <br>

                        <small>
                            R${item.price.toFixed(2)}
                        </small>

                    </div>

                    <div class="quantity-controls">

                        <button
                            type="button"
                            onclick="changeQuantity(${index}, -1)">
                            -
                        </button>

                        <span class="mx-2">
                            ${item.quantity}
                        </span>

                        <button
                            type="button"
                            onclick="changeQuantity(${index}, 1)">
                            +
                        </button>

                    </div>


                    <strong>
                        R${subtotal.toFixed(2)}
                    </strong>

                </div>

            `;

        });

        document.getElementById("checkoutButton").disabled = false;

    }

    document.getElementById("cartTotal").innerText =
        "R" + total.toFixed(2);
}

function changeQuantity(index, change) {

    let item = cart[index];

    item.quantity += change;


    if (item.quantity <= 0) {
        cart.splice(index, 1);
    }


    if (item.quantity > item.stock) {
        item.quantity = item.stock;
    }


    updateCart();
}

function showCart() {

    document.getElementById("categoryScreen")
        .classList.remove("active");

    document.getElementById("cartScreen")
        .classList.add("active");

    updateCart();
}

function showCategories() {

    document.getElementById("cartScreen")
        .classList.remove("active");

    document.getElementById("categoryScreen")
        .classList.add("active");

}

function prepareSale() {

    document.getElementById("cartInput").value =
        JSON.stringify(cart);

}

function selectProduct(
    productId,
    productName,
    currentStock
) {

    document.getElementById("productId").value =
        productId;

    document.getElementById("selectedProductName")
        .innerText = productName;

    document.getElementById("selectedProductStock")
        .innerText = currentStock;


    document.getElementById("quantityAdded").value = "";


    document.getElementById("productScreen")
        .classList.remove("active");

    document.getElementById("stockScreen")
        .classList.add("active");

}

function showProducts() {

    document.getElementById("stockScreen")
        .classList.remove("active");

    document.getElementById("productScreen")
        .classList.add("active");

}