<?php
    session_start();
    include('db.php');
    $conn = db_connect();

    $result = mysqli_query($conn, "SELECT * FROM products");
    $products = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }

    if (isset($_GET['search'])) {
        $searchTerm = $_GET['search'];
        $filteredProducts = array_filter($products, function ($product) use ($searchTerm) {
            return strpos(strtolower($product['name']), strtolower($searchTerm)) !== false;
        });
        $products = array_values($filteredProducts);
    }

    if (isset($_GET['sort'])) {
        $sortOption = $_GET['sort'];
        if ($sortOption === 'name') {
            usort($products, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        } elseif ($sortOption === 'price') {
            usort($products, function ($a, $b) {
                return $a['price'] - $b['price'];
            });
        }

        $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

        if (isset($_POST['add_to_cart'])) {
            $productId = $_POST['add_to_cart'];
            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
            $cart[$productId] = isset($cart[$productId]) ? $cart[$productId] + 1 : 1;
            $_SESSION['cart'] = $cart;
        }
    }

    mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Web Shop</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php
    echo '<style>';
    echo "@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');";
    echo '</style>';
    ?>
    <h1>Web Shop</h1>
    <div class="actions-container">
        <div>
            <form method="GET">
                <input class="input" type="text" name="search" id="search-input" placeholder="Search">
                <select class="input" name="sort" id="sort-select">
                    <option value="name">Sort by Name</option>
                    <option value="price">Sort by Price</option>
                </select>
                <button type="submit" class="apply-button">Apply</button>
            </form>
        </div>
        <button class="cart-button" onclick="openCartModal()">Cart <span class="cart-badge"><?php echo $cartCount; ?></span></button>
    </div>

    <div class="items-grid">
        <?php foreach ($products as $product) { ?>
            <div class="item">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <h3><?php echo $product['name']; ?></h3>
                <p>Price: $<?php echo $product['price']; ?></p>
                <button onclick="addToCart(<?php echo $product['code']; ?>)" class="add-to-cart-btn">Add to Cart</button>
            </div>
        <?php } ?>
    </div>

    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Cart</h2>
                <span class="close-modal" onclick="closeCartModal()">&times;</span>
            </div>
            <p id="purchase-message" class="purchase-message"></p>
            <ul class="cart-items">
                <?php foreach ($_SESSION['cart'] ?? [] as $productId => $quantity) {
                    $product = getProductById($productId, $products);
                    if ($product) { ?>
                        <li><?php echo $product['name']; ?> (Quantity: <?php echo $quantity; ?>)</li>
                    <?php }
                } ?>
            </ul>
            <p>Total: <span class="cart-total">$0.00</span></p>
            <button class="buy-btn" onclick="buyItems()">Buy</button>
        </div>
    </div>

    <script>
        const items = <?php echo json_encode($products); ?>;
        const cart = [];

        const addToCart = (productId) => {
            const item = items.find((item) => item.code === productId);
            if (item) {
            cart.push(item);
            updateCart();
            updateCartCount(cart.length);
            }
        };

        function updateCart() {
            const cartItems = document.querySelector('.cart-items');
            if (cartItems) {
            cartItems.innerHTML = '';
            cart.forEach((item) => {
                const li = document.createElement('li');
                li.textContent = `${item.name} (Quantity: 1)`;
                cartItems.appendChild(li);
            });
            }
        }

        function updateCartCount(count) {
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
            cartBadge.textContent = count;
            }
        }

        const openCartModal = () => {
            const modal = document.querySelector('.modal');
            if (modal) {
            modal.style.display = 'block';
            }
        };

        const closeCartModal = () => {
            const modal = document.querySelector('.modal');
            if (modal) {
            modal.style.display = 'none';
            }
        };

        const purchaseMessage = document.getElementById('purchase-message');

        const buyItems = () => {
            const cartItems = <?php echo isset($_SESSION['cart']) ? json_encode($_SESSION['cart']) : '[]'; ?>;
            if (cartItems.length > 0) {
            <?php unset($_SESSION['cart']); ?>
            purchaseMessage.textContent = 'Items purchased!';
            purchaseMessage.classList.remove('error');
            purchaseMessage.classList.add('success');
            }
            else {
            purchaseMessage.textContent = 'Error: Cart is empty!';
            purchaseMessage.classList.remove('success');
            purchaseMessage.classList.add('error');
            }
        };
    </script>

</body>
</html>
