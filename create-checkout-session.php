<?php
session_start();
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('YOUR_STRIPE_SECRET_KEY');

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost/E-Commerce';

// Get data from the frontend
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);

$line_items = [];
if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])) {
    foreach ($_SESSION['mycart'] as $item) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $item['name'],
                    // 'images' => [$YOUR_DOMAIN . '/admin/upload/' . $item['product_img']], // Optional
                ],
                'unit_amount' => $item['price'] * 100, // Price in cents
            ],
            'quantity' => $item['product_qty'],
        ];
    }
} else {
    // Handle empty cart error - though frontend should prevent this
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

if (empty($line_items)) {
    echo json_encode(['error' => 'No items to checkout']);
    exit;
}

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.php?session_id={CHECKOUT_SESSION_ID}', // Create a success.php page
        'cancel_url' => $YOUR_DOMAIN . '/checkout.php', // Or a specific cancel page
        // 'customer_email' => $json_obj->customer_email, // Optional: prefill email
    ]);

    echo json_encode(['id' => $checkout_session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>