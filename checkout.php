<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page with return URL
    $return_url = '';
    if (isset($_POST['buy_now_action'])) {
        // For buy now actions, redirect back to product page
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
        $product_category = isset($_POST['product_category']) ? $_POST['product_category'] : '';
        if ($product_id && $product_category) {
            $return_url = urlencode("viewdetail.php?id=" . $product_id . "&category=" . $product_category);
        }
    } else {
        // For cart checkout, redirect back to cart
        $return_url = urlencode("cart.php");
    }
    
    header('Location: login.php' . ($return_url ? '?redirect=' . $return_url : ''));
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' || (!isset($_POST['proceed_to_checkout_action']) && !isset($_POST['buy_now_action']))
) {
    header('Location: cart.php');
    exit;
}

require 'vendor/autoload.php';

$env = parse_ini_file('.env');
$stripeApiKey = $env["STRIPE_SECRET_KEY"];

\Stripe\Stripe::setApiKey($stripeApiKey);

$DOMAIN = 'http://localhost/E-Commerce';
$cancel_page_url = $DOMAIN . '/cart.php';

$line_items = [];
if (isset($_POST['buy_now_action'])) {
    // --- Buy Now from viewdetail.php ---
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : null;
    $product_price_str = isset($_POST['product_price']) ? $_POST['product_price'] : null;
    $product_qty_str = isset($_POST['product_qty']) ? $_POST['product_qty'] : null;
    // For redirecting back to the product page on cancel/error
    $product_id_for_redirect = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $product_category_for_redirect = isset($_POST['product_category']) ? $_POST['product_category'] : null;

    $price = filter_var($product_price_str, FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($product_qty_str, FILTER_VALIDATE_INT);

    if ($product_name && $price !== false && $price > 0 && $quantity !== false && $quantity > 0) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $product_name,
                ],
                'unit_amount' => (int) round($price * 100),
            ],
            'quantity' => $quantity,
        ];

        $cancel_page_url = $DOMAIN . "/viewdetail.php?id=" . urlencode($product_id_for_redirect) . "&category=" . urlencode($product_category_for_redirect);

    } else {
        $_SESSION['checkout_error'] = 'Invalid product data for immediate purchase.';
        $redirect_url = $DOMAIN . '/cart.php?error=buynow_invalid_data'; // Fallback
        if ($product_id_for_redirect && $product_category_for_redirect) {
            $redirect_url = $DOMAIN . "/viewdetail.php?id=" . urlencode($product_id_for_redirect) . "&category=" . urlencode($product_category_for_redirect) . "&error=buynow_invalid_data";
        }
        header('Location: ' . $redirect_url);
        exit;
    }

} elseif (isset($_POST['proceed_to_checkout_action'])) {
    // --- Proceed to Checkout from cart.php ---
    if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])) {
        foreach ($_SESSION['mycart'] as $item) {
            $item_price_str = isset($item['price']) ? $item['price'] : (isset($item['product_price']) ? $item['product_price'] : null);
            $item_qty_str = isset($item['product_qty']) ? $item['product_qty'] : null;
            $item_name = isset($item['name']) ? $item['name'] : (isset($item['product_name']) ? $item['product_name'] : 'Unknown Product');

            $price = filter_var($item_price_str, FILTER_VALIDATE_FLOAT);
            $quantity = filter_var($item_qty_str, FILTER_VALIDATE_INT);

            if ($item_name && $price !== false && $price > 0 && $quantity !== false && $quantity > 0) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => $item_name,
                        ],
                        'unit_amount' => (int) round($price * 100),
                    ],
                    'quantity' => $quantity,
                ];
            } else {
                error_log("Invalid item in cart during checkout: " . print_r($item, true));
            }
        }
    }
}

$expires_at_timestamp = time() + (30 * 60);

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => $DOMAIN . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $cancel_page_url,
        'expires_at' => $expires_at_timestamp,
    ]);

    header("HTTP/1.1 303 See Other");
    header('Location: ' . $checkout_session->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("Stripe API Error: " . $e->getMessage());
    $_SESSION['checkout_error'] = 'Payment gateway error: ' . $e->getMessage();
    $redirect_url = $DOMAIN . '/cart.php';
    if (isset($_POST['buy_now_action']) && isset($_POST['product_id'])) {
        $redirect_url = $DOMAIN . "/viewdetail.php?id=" . urlencode($_POST['product_id']) . "&category=" . urlencode($_POST['product_category']);
    }
    header('Location: ' . $redirect_url);
    exit;
} catch (Exception $e) {
    error_log("General Error during checkout: " . $e->getMessage());
    $_SESSION['checkout_error'] = 'An unexpected error occurred: ' . $e->getMessage();
    $redirect_url = $DOMAIN . '/cart.php';
    if (isset($_POST['buy_now_action']) && isset($_POST['product_id'])) {
        $redirect_url = $DOMAIN . "/viewdetail.php?id=" . urlencode($_POST['product_id']) . "&category=" . urlencode($_POST['product_category']);
    }
    header('Location: ' . $redirect_url);
    exit;
}

?>