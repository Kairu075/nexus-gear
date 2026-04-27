<?php
// php/api.php - Central API handler
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    // AUTH
    case 'login': handleLogin(); break;
    case 'register': handleRegister(); break;
    case 'logout': handleLogout(); break;
    case 'check_auth': handleCheckAuth(); break;

    // PRODUCTS
    case 'get_products': handleGetProducts(); break;
    case 'get_product': handleGetProduct(); break;
    case 'search_products': handleSearchProducts(); break;
    case 'get_featured': handleGetFeatured(); break;
    case 'get_bestsellers': handleGetBestSellers(); break;
    case 'get_categories': handleGetCategories(); break;
    case 'get_brands': handleGetBrands(); break;

    // CART
    case 'add_to_cart': handleAddToCart(); break;
    case 'get_cart': handleGetCart(); break;
    case 'update_cart': handleUpdateCart(); break;
    case 'remove_from_cart': handleRemoveFromCart(); break;
    case 'clear_cart': handleClearCart(); break;

    // WISHLIST
    case 'toggle_wishlist': handleToggleWishlist(); break;
    case 'get_wishlist': handleGetWishlist(); break;

    // CHECKOUT
    case 'apply_discount': handleApplyDiscount(); break;
    case 'place_order': handlePlaceOrder(); break;
    case 'get_orders': handleGetOrders(); break;
    case 'get_order': handleGetOrder(); break;

    // REVIEWS
    case 'add_review': handleAddReview(); break;
    case 'get_reviews': handleGetReviews(); break;

    // PROFILE
    case 'update_profile': handleUpdateProfile(); break;
    case 'change_password': handleChangePassword(); break;
    case 'upload_avatar': handleUploadAvatar(); break;

    // ADMIN
    case 'admin_get_dashboard': handleAdminDashboard(); break;
    case 'admin_get_products': handleAdminGetProducts(); break;
    case 'admin_save_product': handleAdminSaveProduct(); break;
    case 'admin_delete_product': handleAdminDeleteProduct(); break;
    case 'admin_get_product_by_id': handleAdminGetProductById(); break;
    case 'admin_upload_image': handleAdminUploadImage(); break;
    case 'admin_delete_image': handleAdminDeleteImage(); break;
    case 'admin_set_primary_image': handleAdminSetPrimaryImage(); break;  // <-- ADDED
    case 'admin_get_orders': handleAdminGetOrders(); break;
    case 'admin_update_order': handleAdminUpdateOrder(); break;
    case 'admin_get_users': handleAdminGetUsers(); break;
    case 'admin_update_user': handleAdminUpdateUser(); break;
    case 'admin_get_revenue': handleAdminRevenue(); break;
    case 'admin_get_categories': handleAdminGetCategories(); break;
    case 'admin_save_category': handleAdminSaveCategory(); break;
    case 'admin_get_discounts': handleAdminGetDiscounts(); break;
    case 'admin_save_discount': handleAdminSaveDiscount(); break;
    case 'admin_toggle_discount': handleAdminToggleDiscount(); break;
    case 'admin_approve_review': handleAdminApproveReview(); break;
    case 'admin_get_reviews': handleAdminGetReviews(); break;

    default: jsonResponse(['success' => false, 'message' => 'Unknown action']);
}

// ===================== AUTH =====================
function handleLogin() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) jsonResponse(['success' => false, 'message' => 'All fields required.']);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid email or password.']);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_avatar'] = $user['avatar'];

    jsonResponse(['success' => true, 'message' => 'Login successful.', 'user' => [
        'id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'],
        'role' => $user['role'], 'avatar' => $user['avatar']
    ]]);
}

function handleRegister() {
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');

    if (!$name || !$email || !$password) jsonResponse(['success' => false, 'message' => 'All required fields must be filled.']);
    if (strlen($password) < 6) jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonResponse(['success' => false, 'message' => 'Invalid email address.']);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Email already registered.']);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hash, $phone]);
    $userId = $pdo->lastInsertId();

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = 'customer';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_avatar'] = 'default.png';

    jsonResponse(['success' => true, 'message' => 'Account created successfully!', 'user' => [
        'id' => $userId, 'name' => $name, 'email' => $email, 'role' => 'customer'
    ]]);
}

function handleLogout() {
    session_destroy();
    jsonResponse(['success' => true]);
}

function handleCheckAuth() {
    if (isLoggedIn()) {
        $user = getCurrentUser();
        jsonResponse(['success' => true, 'logged_in' => true, 'user' => [
            'id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'],
            'role' => $user['role'], 'avatar' => $user['avatar'],
            'phone' => $user['phone'], 'address_line' => $user['address_line'],
            'city' => $user['city'], 'province' => $user['province'],
            'zip_code' => $user['zip_code'], 'lat' => $user['lat'], 'lng' => $user['lng']
        ]]);
    } else {
        jsonResponse(['success' => true, 'logged_in' => false]);
    }
}

// ===================== PRODUCTS =====================
function handleGetProducts() {
    $pdo = getDB();
    $category = $_GET['category'] ?? '';
    $brand = $_GET['brand'] ?? '';
    $sort = $_GET['sort'] ?? 'newest';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = intval($_GET['limit'] ?? 12);
    $offset = ($page - 1) * $limit;

    $where = ["p.is_active = 1"];
    $params = [];

    if ($category) { $where[] = "c.slug = ?"; $params[] = $category; }
    if ($brand) { $where[] = "b.slug = ?"; $params[] = $brand; }

    $whereStr = implode(' AND ', $where);
    $orderBy = match($sort) {
        'price_asc' => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'rating' => 'p.rating_avg DESC',
        'popular' => 'p.total_sold DESC',
        default => 'p.created_at DESC'
    };

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE $whereStr");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    $params[] = $limit;
    $params[] = $offset;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, b.name as brand_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE $whereStr ORDER BY $orderBy LIMIT ? OFFSET ?");
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    jsonResponse(['success' => true, 'products' => $products, 'total' => $total, 'pages' => ceil($total / $limit), 'page' => $page]);
}

function handleGetProduct() {
    $slug = $_GET['slug'] ?? '';
    if (!$slug) jsonResponse(['success' => false, 'message' => 'Product not found.']);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name
        FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.slug = ? AND p.is_active = 1");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
    if (!$product) jsonResponse(['success' => false, 'message' => 'Product not found.']);

    $imgStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order");
    $imgStmt->execute([$product['id']]);
    $product['images'] = $imgStmt->fetchAll();

    $revStmt = $pdo->prepare("SELECT r.*, u.name as user_name, u.avatar as user_avatar FROM reviews r
        JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC");
    $revStmt->execute([$product['id']]);
    $product['reviews'] = $revStmt->fetchAll();

    $relStmt = $pdo->prepare("SELECT p.*, (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 LIMIT 4");
    $relStmt->execute([$product['category_id'], $product['id']]);
    $product['related'] = $relStmt->fetchAll();

    jsonResponse(['success' => true, 'product' => $product]);
}

function handleSearchProducts() {
    $q = sanitize($_GET['q'] ?? '');
    if (!$q) jsonResponse(['success' => true, 'products' => []]);

    $pdo = getDB();
    $search = "%$q%";
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, b.name as brand_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? OR b.name LIKE ?)
        ORDER BY p.total_sold DESC LIMIT 20");
    $stmt->execute([$search, $search, $search, $search]);
    jsonResponse(['success' => true, 'products' => $stmt->fetchAll()]);
}

function handleGetFeatured() {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_featured = 1 AND p.is_active = 1 ORDER BY p.created_at DESC LIMIT 8");
    $stmt->execute();
    jsonResponse(['success' => true, 'products' => $stmt->fetchAll()]);
}

function handleGetBestSellers() {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_best_seller = 1 AND p.is_active = 1 ORDER BY p.total_sold DESC LIMIT 8");
    $stmt->execute();
    jsonResponse(['success' => true, 'products' => $stmt->fetchAll()]);
}

function handleGetCategories() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 WHERE c.is_active = 1 GROUP BY c.id ORDER BY c.sort_order");
    jsonResponse(['success' => true, 'categories' => $stmt->fetchAll()]);
}

function handleGetBrands() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT b.*, COUNT(p.id) as product_count FROM brands b LEFT JOIN products p ON b.id = p.brand_id AND p.is_active = 1 WHERE b.is_active = 1 GROUP BY b.id ORDER BY b.name");
    jsonResponse(['success' => true, 'brands' => $stmt->fetchAll()]);
}

// ===================== CART =====================
function handleAddToCart() {
    requireLogin();
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    if (!$product_id) jsonResponse(['success' => false, 'message' => 'Invalid product.']);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    if (!$product) jsonResponse(['success' => false, 'message' => 'Product not found.']);

    $checkStmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$_SESSION['user_id'], $product_id]);
    $existing = $checkStmt->fetch();

    $newQty = $existing ? $existing['quantity'] + $quantity : $quantity;
    if ($newQty > $product['stock']) jsonResponse(['success' => false, 'message' => 'Not enough stock available.']);

    if ($existing) {
        $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?")->execute([$newQty, $existing['id']]);
    } else {
        $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)")->execute([$_SESSION['user_id'], $product_id, $quantity]);
    }

    $countStmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $countStmt->execute([$_SESSION['user_id']]);
    jsonResponse(['success' => true, 'message' => 'Added to cart!', 'cart_count' => intval($countStmt->fetchColumn())]);
}

function handleGetCart() {
    if (!isLoggedIn()) jsonResponse(['success' => true, 'items' => [], 'count' => 0, 'total' => 0]);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.stock, p.slug,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
        FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? AND p.is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll();

    $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
    $count = array_sum(array_column($items, 'quantity'));

    jsonResponse(['success' => true, 'items' => $items, 'count' => $count, 'total' => $total]);
}

function handleUpdateCart() {
    requireLogin();
    $cart_id = intval($_POST['cart_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    $item = $stmt->fetch();
    if (!$item) jsonResponse(['success' => false, 'message' => 'Cart item not found.']);
    if ($quantity > $item['stock']) jsonResponse(['success' => false, 'message' => 'Insufficient stock.']);

    $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?")->execute([$quantity, $cart_id]);
    jsonResponse(['success' => true]);
}

function handleRemoveFromCart() {
    requireLogin();
    $cart_id = intval($_POST['cart_id'] ?? 0);
    $pdo = getDB();
    $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cart_id, $_SESSION['user_id']]);
    jsonResponse(['success' => true]);
}

function handleClearCart() {
    requireLogin();
    $pdo = getDB();
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$_SESSION['user_id']]);
    jsonResponse(['success' => true]);
}

// ===================== WISHLIST =====================
function handleToggleWishlist() {
    requireLogin();
    $product_id = intval($_POST['product_id'] ?? 0);
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$_SESSION['user_id'], $product_id]);
        jsonResponse(['success' => true, 'action' => 'removed']);
    } else {
        $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$_SESSION['user_id'], $product_id]);
        jsonResponse(['success' => true, 'action' => 'added']);
    }
}

function handleGetWishlist() {
    requireLogin();
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT p.*, (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
        FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? AND p.is_active = 1 ORDER BY w.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    jsonResponse(['success' => true, 'items' => $stmt->fetchAll()]);
}

// ===================== CHECKOUT =====================
function handleApplyDiscount() {
    requireLogin();
    $code = strtoupper(sanitize($_POST['code'] ?? ''));
    $subtotal = floatval($_POST['subtotal'] ?? 0);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM discount_codes WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) AND (max_uses IS NULL OR used_count < max_uses)");
    $stmt->execute([$code]);
    $discount = $stmt->fetch();

    if (!$discount) jsonResponse(['success' => false, 'message' => 'Invalid or expired discount code.']);
    if ($subtotal < $discount['min_order']) jsonResponse(['success' => false, 'message' => "Minimum order of ₱" . number_format($discount['min_order'], 2) . " required."]);

    $amount = $discount['type'] === 'percentage' ? ($subtotal * $discount['value'] / 100) : $discount['value'];
    $amount = min($amount, $subtotal);

    jsonResponse(['success' => true, 'discount' => [
        'id' => $discount['id'], 'code' => $discount['code'],
        'type' => $discount['type'], 'value' => $discount['value'],
        'amount' => round($amount, 2)
    ]]);
}

function handlePlaceOrder() {
    requireLogin();
    $pdo = getDB();

    $cartStmt = $pdo->prepare("SELECT c.quantity, p.id as product_id, p.name, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $cartStmt->execute([$_SESSION['user_id']]);
    $items = $cartStmt->fetchAll();
    if (empty($items)) jsonResponse(['success' => false, 'message' => 'Your cart is empty.']);

    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock']) {
            jsonResponse(['success' => false, 'message' => "{$item['name']} has insufficient stock."]);
        }
    }

    $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
    $discount_id = intval($_POST['discount_id'] ?? 0);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $total = max(0, $subtotal - $discount_amount);

    $payment_method = $_POST['payment_method'] ?? 'cod';
    $notes = sanitize($_POST['notes'] ?? '');
    $shipping_name = sanitize($_POST['shipping_name'] ?? '');
    $shipping_phone = sanitize($_POST['shipping_phone'] ?? '');
    $shipping_address = sanitize($_POST['shipping_address'] ?? '');
    $shipping_city = sanitize($_POST['shipping_city'] ?? '');
    $shipping_province = sanitize($_POST['shipping_province'] ?? '');
    $shipping_zip = sanitize($_POST['shipping_zip'] ?? '');
    $gcash_ref = sanitize($_POST['gcash_ref'] ?? '');
    $card_last4 = sanitize($_POST['card_last4'] ?? '');

    $order_number = generateOrderNumber();

    $pdo->beginTransaction();
    try {
        $orderStmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, subtotal, discount_amount, total, discount_code_id, payment_method, notes, shipping_name, shipping_phone, shipping_address, shipping_city, shipping_province, shipping_zip, gcash_ref, card_last4) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $orderStmt->execute([$order_number, $_SESSION['user_id'], $subtotal, $discount_amount, $total, $discount_id ?: null, $payment_method, $notes, $shipping_name, $shipping_phone, $shipping_address, $shipping_city, $shipping_province, $shipping_zip, $gcash_ref, $card_last4]);
        $order_id = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?,?,?,?,?,?)");
        foreach ($items as $item) {
            $itemStmt->execute([$order_id, $item['product_id'], $item['name'], $item['price'], $item['quantity'], $item['price'] * $item['quantity']]);
        }

        if ($discount_id) {
            $pdo->prepare("UPDATE discount_codes SET used_count = used_count + 1 WHERE id = ?")->execute([$discount_id]);
        }

        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        $pdo->commit();
        jsonResponse(['success' => true, 'message' => 'Order placed successfully!', 'order_number' => $order_number, 'order_id' => $order_id]);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['success' => false, 'message' => 'Failed to place order. Please try again.']);
    }
}

function handleGetOrders() {
    requireLogin();
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    jsonResponse(['success' => true, 'orders' => $stmt->fetchAll()]);
}

function handleGetOrder() {
    requireLogin();
    $order_id = intval($_GET['id'] ?? 0);
    $pdo = getDB();

    // Admin can view any order; customers only their own
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
    }
    $order = $stmt->fetch();
    if (!$order) jsonResponse(['success' => false, 'message' => 'Order not found.']);

    $itemsStmt = $pdo->prepare("SELECT oi.*, (SELECT image_path FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as image FROM order_items oi WHERE oi.order_id = ?");
    $itemsStmt->execute([$order_id]);
    $order['items'] = $itemsStmt->fetchAll();

    jsonResponse(['success' => true, 'order' => $order]);
}

// ===================== REVIEWS =====================
function handleAddReview() {
    requireLogin();
    $product_id = intval($_POST['product_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $title = sanitize($_POST['title'] ?? '');
    $body = sanitize($_POST['body'] ?? '');

    if (!$product_id || $rating < 1 || $rating > 5) jsonResponse(['success' => false, 'message' => 'Invalid review data.']);

    $pdo = getDB();
    $purchaseCheck = $pdo->prepare("SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'delivered'");
    $purchaseCheck->execute([$_SESSION['user_id'], $product_id]);
    if (!$purchaseCheck->fetch()) jsonResponse(['success' => false, 'message' => 'You can only review products you have purchased and received.']);

    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, title, body) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating), title=VALUES(title), body=VALUES(body)");
    $stmt->execute([$_SESSION['user_id'], $product_id, $rating, $title, $body]);

    $ratingStmt = $pdo->prepare("UPDATE products SET rating_avg = (SELECT AVG(rating) FROM reviews WHERE product_id = ? AND is_approved = 1), rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = ? AND is_approved = 1) WHERE id = ?");
    $ratingStmt->execute([$product_id, $product_id, $product_id]);

    jsonResponse(['success' => true, 'message' => 'Review submitted successfully!']);
}

function handleGetReviews() {
    $product_id = intval($_GET['product_id'] ?? 0);
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT r.*, u.name as user_name, u.avatar FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC");
    $stmt->execute([$product_id]);
    jsonResponse(['success' => true, 'reviews' => $stmt->fetchAll()]);
}

// ===================== PROFILE =====================
function handleUpdateProfile() {
    requireLogin();
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address_line = sanitize($_POST['address_line'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $province = sanitize($_POST['province'] ?? '');
    $zip_code = sanitize($_POST['zip_code'] ?? '');
    $lat = floatval($_POST['lat'] ?? 0);
    $lng = floatval($_POST['lng'] ?? 0);

    $pdo = getDB();
    $pdo->prepare("UPDATE users SET name=?, phone=?, address_line=?, city=?, province=?, zip_code=?, lat=?, lng=?, updated_at=NOW() WHERE id=?")
        ->execute([$name, $phone, $address_line, $city, $province, $zip_code, $lat ?: null, $lng ?: null, $_SESSION['user_id']]);
    $_SESSION['user_name'] = $name;
    jsonResponse(['success' => true, 'message' => 'Profile updated successfully!']);
}

function handleChangePassword() {
    requireLogin();
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    if (strlen($new) < 6) jsonResponse(['success' => false, 'message' => 'New password must be at least 6 characters.']);

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!password_verify($current, $user['password'])) jsonResponse(['success' => false, 'message' => 'Current password is incorrect.']);

    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);
    jsonResponse(['success' => true, 'message' => 'Password changed successfully!']);
}

function handleUploadAvatar() {
    requireLogin();
    if (!isset($_FILES['avatar'])) jsonResponse(['success' => false, 'message' => 'No file uploaded.']);
    $file = $_FILES['avatar'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) jsonResponse(['success' => false, 'message' => 'Only JPG, PNG, WEBP allowed.']);
    if ($file['size'] > 2 * 1024 * 1024) jsonResponse(['success' => false, 'message' => 'File size must be under 2MB.']);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
    $path = UPLOAD_PATH . 'avatars/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $path)) jsonResponse(['success' => false, 'message' => 'Upload failed.']);

    $pdo = getDB();
    $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$filename, $_SESSION['user_id']]);
    $_SESSION['user_avatar'] = $filename;
    jsonResponse(['success' => true, 'avatar' => $filename, 'avatar_url' => UPLOAD_URL . 'avatars/' . $filename]);
}

// ===================== ADMIN =====================
function handleAdminDashboard() {
    requireAdmin();
    $pdo = getDB();

    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();
    $totalRevenue = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE order_status NOT IN ('cancelled')")->fetchColumn();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    $lowStockProducts = $pdo->query("SELECT * FROM products WHERE stock <= 10 AND is_active = 1 ORDER BY stock ASC")->fetchAll();
    $recentOrders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
    $monthlySales = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as revenue, COUNT(*) as orders FROM orders WHERE order_status != 'cancelled' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month")->fetchAll();

    jsonResponse(['success' => true, 'data' => [
        'total_products' => $totalProducts, 'total_orders' => $totalOrders,
        'pending_orders' => $pendingOrders, 'total_revenue' => $totalRevenue,
        'total_users' => $totalUsers, 'low_stock' => $lowStockProducts,
        'recent_orders' => $recentOrders, 'monthly_sales' => $monthlySales
    ]]);
}

function handleAdminGetProductById() {
    requireAdmin();
    $id = intval($_GET['id'] ?? 0);
    if (!$id) jsonResponse(['success' => false, 'message' => 'ID required.']);
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         LEFT JOIN brands b ON p.brand_id = b.id
         WHERE p.id = ?"
    );
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) jsonResponse(['success' => false, 'message' => 'Not found.']);

    $imgs = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id");
    $imgs->execute([$id]);
    $product['images'] = $imgs->fetchAll();

    jsonResponse(['success' => true, 'product' => $product]);
}

function handleAdminGetProducts() {
    requireAdmin();
    $pdo = getDB();
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 15;
    $offset = ($page - 1) * $limit;
    $search = sanitize($_GET['search'] ?? '');

    $where = $search ? "WHERE p.name LIKE ? OR b.name LIKE ?" : "";
    $params = $search ? ["%$search%", "%$search%"] : [];

    $total = $pdo->prepare("SELECT COUNT(*) FROM products p LEFT JOIN brands b ON p.brand_id = b.id $where");
    $total->execute($params);

    $params[] = $limit; $params[] = $offset;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, b.name as brand_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id
        $where ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute($params);

    jsonResponse(['success' => true, 'products' => $stmt->fetchAll(), 'total' => $total->fetchColumn()]);
}

function handleAdminSaveProduct() {
    requireAdmin();
    $pdo = getDB();
    $id = intval($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $description = $_POST['description'] ?? '';
    $short_description = sanitize($_POST['short_description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $original_price = floatval($_POST['original_price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $brand_id = intval($_POST['brand_id'] ?? 0);
    $is_featured = intval($_POST['is_featured'] ?? 0);
    $is_best_seller = intval($_POST['is_best_seller'] ?? 0);
    $specifications = $_POST['specifications'] ?? '';

    if (!$name || !$price) jsonResponse(['success' => false, 'message' => 'Name and price required.']);

    if ($id) {
        $existing = $pdo->prepare("SELECT slug FROM products WHERE id = ?");
        $existing->execute([$id]);
        $row = $existing->fetch();
        $slug = $row ? $row['slug'] : strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)) . '-' . $id;

        $pdo->prepare("UPDATE products SET name=?,slug=?,description=?,short_description=?,price=?,original_price=?,stock=?,category_id=?,brand_id=?,is_featured=?,is_best_seller=?,specifications=?,updated_at=NOW() WHERE id=?")
            ->execute([$name, $slug, $description, $short_description, $price, $original_price ?: null, $stock, $category_id ?: null, $brand_id ?: null, $is_featured, $is_best_seller, $specifications ?: null, $id]);
        jsonResponse(['success' => true, 'message' => 'Product updated.', 'id' => $id, 'slug' => $slug]);
    } else {
        $baseSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $slug = trim($baseSlug, '-') . '-' . time();
        $check = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) $slug .= '-' . rand(100, 999);

        $pdo->prepare("INSERT INTO products (name,slug,description,short_description,price,original_price,stock,category_id,brand_id,is_featured,is_best_seller,specifications) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$name, $slug, $description, $short_description, $price, $original_price ?: null, $stock, $category_id ?: null, $brand_id ?: null, $is_featured, $is_best_seller, $specifications ?: null]);
        $newId = $pdo->lastInsertId();
        jsonResponse(['success' => true, 'message' => 'Product created.', 'id' => $newId, 'slug' => $slug]);
    }
}

function handleAdminDeleteProduct() {
    requireAdmin();
    $id = intval($_POST['id'] ?? 0);
    $pdo = getDB();
    $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Product removed.']);
}

function handleAdminUploadImage() {
    requireAdmin();
    $product_id = intval($_POST['product_id'] ?? 0);
    $is_primary = intval($_POST['is_primary'] ?? 0);
    if (!$product_id || !isset($_FILES['image'])) jsonResponse(['success' => false, 'message' => 'Invalid request.']);

    $file = $_FILES['image'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) jsonResponse(['success' => false, 'message' => 'Invalid image type.']);
    if ($file['size'] > 5 * 1024 * 1024) jsonResponse(['success' => false, 'message' => 'File too large (max 5MB).']);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'prod_' . $product_id . '_' . time() . '_' . rand(100,999) . '.' . $ext;
    $path = UPLOAD_PATH . 'products/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $path)) jsonResponse(['success' => false, 'message' => 'Upload failed.']);

    $pdo = getDB();

    // If this is the first image for the product, always make it primary
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
    $countStmt->execute([$product_id]);
    $existingCount = (int)$countStmt->fetchColumn();
    if ($existingCount === 0) $is_primary = 1;

    if ($is_primary) {
        $pdo->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?")->execute([$product_id]);
    }

    $sortStmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM product_images WHERE product_id = ?");
    $sortStmt->execute([$product_id]);
    $sort = $sortStmt->fetchColumn();

    $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary, sort_order) VALUES (?,?,?,?)")
        ->execute([$product_id, $filename, $is_primary, $sort]);

    $image_id = $pdo->lastInsertId();
    jsonResponse(['success' => true, 'image_id' => $image_id, 'filename' => $filename, 'url' => UPLOAD_URL . 'products/' . $filename, 'is_primary' => $is_primary]);
}

// ===================== FIX: Set primary image =====================
function handleAdminSetPrimaryImage() {
    requireAdmin();
    $image_id   = intval($_POST['image_id']   ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);

    if (!$image_id || !$product_id) {
        jsonResponse(['success' => false, 'message' => 'Missing image_id or product_id.']);
    }

    $pdo = getDB();

    // Verify this image actually belongs to this product
    $check = $pdo->prepare("SELECT id FROM product_images WHERE id = ? AND product_id = ?");
    $check->execute([$image_id, $product_id]);
    if (!$check->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Image not found for this product.']);
    }

    // Step 1: clear current primary for this product
    $pdo->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?")
        ->execute([$product_id]);

    // Step 2: set the chosen image as primary
    $pdo->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?")
        ->execute([$image_id]);

    jsonResponse(['success' => true, 'message' => 'Primary image updated.']);
}

// ===================== FIX: Delete image (auto-promote next) =====================
function handleAdminDeleteImage() {
    requireAdmin();
    $id = intval($_POST['id'] ?? 0);
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    if ($img) {
        // Delete physical file
        @unlink(UPLOAD_PATH . 'products/' . $img['image_path']);

        // Delete DB record
        $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$id]);

        // If we just deleted the primary image, promote the next remaining one
        if ($img['is_primary']) {
            $next = $pdo->prepare(
                "SELECT id FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1"
            );
            $next->execute([$img['product_id']]);
            $nextImg = $next->fetch();
            if ($nextImg) {
                $pdo->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?")
                    ->execute([$nextImg['id']]);
            }
        }
    }

    jsonResponse(['success' => true]);
}

function handleAdminGetOrders() {
    requireAdmin();
    $pdo = getDB();
    $status = $_GET['status'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 15;
    $offset = ($page - 1) * $limit;

    $where = $status ? "WHERE o.order_status = ?" : "";
    $params = $status ? [$status] : [];

    $total = $pdo->prepare("SELECT COUNT(*) FROM orders o $where");
    $total->execute($params);

    $params[] = $limit; $params[] = $offset;
    $stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone, COUNT(oi.id) as item_count FROM orders o JOIN users u ON o.user_id = u.id LEFT JOIN order_items oi ON o.id = oi.order_id $where GROUP BY o.id ORDER BY o.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'orders' => $stmt->fetchAll(), 'total' => $total->fetchColumn()]);
}

function handleAdminUpdateOrder() {
    requireAdmin();
    $pdo = getDB();
    $id = intval($_POST['id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $valid = ['pending','processing','approved','shipped','delivered','cancelled'];
    if (!in_array($status, $valid)) jsonResponse(['success' => false, 'message' => 'Invalid status.']);

    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    if (!$order) jsonResponse(['success' => false, 'message' => 'Order not found.']);

    if ($status === 'approved' && $order['order_status'] !== 'approved') {
        $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $items->execute([$id]);
        foreach ($items->fetchAll() as $item) {
            $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?), total_sold = total_sold + ? WHERE id = ?")
                ->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
        }
    }

    $timestamps = ['approved' => 'approved_at', 'shipped' => 'shipped_at', 'delivered' => 'delivered_at'];
    $tsCol = $timestamps[$status] ?? null;
    $tsUpdate = $tsCol ? ", $tsCol = NOW()" : "";
    $pdo->prepare("UPDATE orders SET order_status = ? $tsUpdate WHERE id = ?")->execute([$status, $id]);

    jsonResponse(['success' => true, 'message' => 'Order updated successfully.']);
}

function handleAdminGetUsers() {
    requireAdmin();
    $pdo = getDB();
    $search = sanitize($_GET['search'] ?? '');
    $where = $search ? "WHERE role = 'customer' AND (name LIKE ? OR email LIKE ?)" : "WHERE role = 'customer'";
    $params = $search ? ["%$search%", "%$search%"] : [];
    $stmt = $pdo->prepare("SELECT u.*, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id $where GROUP BY u.id ORDER BY u.created_at DESC");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'users' => $stmt->fetchAll()]);
}

function handleAdminUpdateUser() {
    requireAdmin();
    $id = intval($_POST['id'] ?? 0);
    $is_active = intval($_POST['is_active'] ?? 1);
    $pdo = getDB();
    $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role = 'customer'")->execute([$is_active, $id]);
    jsonResponse(['success' => true]);
}

function handleAdminRevenue() {
    requireAdmin();
    $pdo = getDB();
    $period = $_GET['period'] ?? 'monthly';

    $format = $period === 'daily' ? '%Y-%m-%d' : '%Y-%m';
    $interval = $period === 'daily' ? '30 DAY' : '12 MONTH';

    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '$format') as period, SUM(total) as revenue, COUNT(*) as orders
        FROM orders WHERE order_status != 'cancelled' AND created_at >= DATE_SUB(NOW(), INTERVAL $interval)
        GROUP BY period ORDER BY period");

    $topProducts = $pdo->query("SELECT p.name, SUM(oi.quantity) as sold, SUM(oi.subtotal) as revenue
        FROM order_items oi JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id WHERE o.order_status != 'cancelled'
        GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5")->fetchAll();

    jsonResponse(['success' => true, 'chart_data' => $stmt->fetchAll(), 'top_products' => $topProducts]);
}

function handleAdminGetCategories() {
    requireAdmin();
    $pdo = getDB();
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 GROUP BY c.id ORDER BY c.sort_order");
    jsonResponse(['success' => true, 'categories' => $stmt->fetchAll()]);
}

function handleAdminSaveCategory() {
    requireAdmin();
    $pdo = getDB();
    $id = intval($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

    if ($id) {
        $pdo->prepare("UPDATE categories SET name=?,slug=?,description=?,sort_order=? WHERE id=?")->execute([$name, $slug, $description, $sort_order, $id]);
    } else {
        $pdo->prepare("INSERT INTO categories (name,slug,description,sort_order) VALUES (?,?,?,?)")->execute([$name, $slug, $description, $sort_order]);
    }
    jsonResponse(['success' => true]);
}

function handleAdminGetDiscounts() {
    requireAdmin();
    $pdo = getDB();
    jsonResponse(['success' => true, 'discounts' => $pdo->query("SELECT * FROM discount_codes ORDER BY created_at DESC")->fetchAll()]);
}

function handleAdminSaveDiscount() {
    requireAdmin();
    $pdo = getDB();
    $id = intval($_POST['id'] ?? 0);
    $code = strtoupper(sanitize($_POST['code'] ?? ''));
    $type = $_POST['type'] ?? 'percentage';
    $value = floatval($_POST['value'] ?? 0);
    $min_order = floatval($_POST['min_order'] ?? 0);
    $max_uses = $_POST['max_uses'] ? intval($_POST['max_uses']) : null;
    $expires_at = $_POST['expires_at'] ?? null;

    if ($id) {
        $pdo->prepare("UPDATE discount_codes SET code=?,type=?,value=?,min_order=?,max_uses=?,expires_at=? WHERE id=?")->execute([$code,$type,$value,$min_order,$max_uses,$expires_at,$id]);
    } else {
        $pdo->prepare("INSERT INTO discount_codes (code,type,value,min_order,max_uses,expires_at) VALUES (?,?,?,?,?,?)")->execute([$code,$type,$value,$min_order,$max_uses,$expires_at]);
    }
    jsonResponse(['success' => true]);
}

function handleAdminToggleDiscount() {
    requireAdmin();
    $id = intval($_POST['id'] ?? 0);
    $pdo = getDB();
    $pdo->prepare("UPDATE discount_codes SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}

function handleAdminGetReviews() {
    requireAdmin();
    $pdo = getDB();
    $stmt = $pdo->query("SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
    jsonResponse(['success' => true, 'reviews' => $stmt->fetchAll()]);
}

function handleAdminApproveReview() {
    requireAdmin();
    $id = intval($_POST['id'] ?? 0);
    $approved = intval($_POST['approved'] ?? 1);
    $pdo = getDB();
    $pdo->prepare("UPDATE reviews SET is_approved = ? WHERE id = ?")->execute([$approved, $id]);
    jsonResponse(['success' => true]);
}