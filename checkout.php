<?php
session_start();

require 'vendor/autoload.php';

$env = parse_ini_file('.env');
$stripeApiKey = $env["STRIPE_SECRET_KEY"];

\Stripe\Stripe::setApiKey($stripeApiKey);

$DOMAIN = 'http://localhost/E-Commerce';

$line_items = [];
if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])) {
    foreach ($_SESSION['mycart'] as $item) {
        $price = filter_var($item['price'], FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($item['product_qty'], FILTER_VALIDATE_INT);

        if ($price === false || $quantity === false || $price <= 0 || $quantity <= 0) {
            // Handle invalid item data, perhaps log it and skip, or show an error
            // For now, we'll skip or you can redirect to an error page
            // error_log("Invalid item data in cart: " . print_r($item, true));
            // header('Location: ' . $DOMAIN . '/cart.php?error=invalid_item_data');
            // exit;
            continue; // Or handle more gracefully
        }

        $line_items[] = [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $item['name'],
                    // 'images' => [$DOMAIN . '/admin/upload/' . $item['product_img']], // Optional
                ],
                'unit_amount' => (int)($price * 100), // Price in cents, ensure it's an integer
            ],
            'quantity' => $quantity,
        ];
    }
}

// Check if cart was empty or all items were invalid
if (empty($line_items)) {
    // Redirect back to cart with an error message or display an error page
    // For simplicity, redirecting to cart page (you might want a more specific error page)
    $_SESSION['checkout_error'] = 'Your cart is empty or contains invalid items.'; // Optional: set a flash message
    header('Location: ' . $DOMAIN . '/cart.php'); // Or your cart page
    exit;
}


try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => $DOMAIN . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $DOMAIN . '/cart.php',
    ]);

    header("HTTP/1.1 303 See Other"); 
    header('Location: ' . $checkout_session->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("Stripe API Error: " . $e->getMessage());
    $_SESSION['checkout_error'] = 'There was an issue connecting to the payment gateway. Please try again later. Details: ' . $e->getMessage();
    header('Location: ' . $DOMAIN . '/cart.php?error=payment_gateway_error');
    exit;
} catch (Exception $e) {
    error_log("General Error during checkout: " . $e->getMessage());
    $_SESSION['checkout_error'] = 'An unexpected error occurred. Please try again. Details: ' . $e->getMessage();
    header('Location: ' . $DOMAIN . '/cart.php?error=unknown_error'); 
    exit;
}

?>