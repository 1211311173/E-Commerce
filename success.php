<?php
// Include header navigation (this starts the session)
include_once('./includes/headerNav.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Get session ID from URL parameter
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?php echo $_SESSION['web-name']; ?></title>
    <link rel="stylesheet" href="css/style-prefix.css">
    <link rel="stylesheet" href="css/success-styles.css">
</head>

<div class="overlay" data-overlay></div>

<!-- HEADER -->
<header>
    <!-- Top head action, search etc -->
    <?php require_once './includes/topheadactions.php'; ?>
    <!-- Desktop navigation -->
    <?php require_once './includes/desktopnav.php'; ?>
    <!-- Mobile navigation -->
    <?php require_once './includes/mobilenav.php'; ?>
</header>

<!-- MAIN CONTENT -->
<main>
    <div class="success-container">
        <div class="success-card">
            <!-- Success Icon -->
            <div class="success-icon">
                <div class="checkmark-circle">
                    <div class="checkmark">
                        <div class="checkmark-stem"></div>
                        <div class="checkmark-kick"></div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div class="success-content">
                <h1 class="success-title">Payment Successful!</h1>
                <p class="success-subtitle">Thank you for your purchase. Your order has been processed successfully.</p>
                
                <?php if ($session_id): ?>
                    <div class="transaction-details">
                        <p class="transaction-id">
                            <strong>Transaction ID:</strong> 
                            <span class="id-text"><?php echo htmlspecialchars(substr($session_id, 0, 20)) . '...'; ?></span>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="success-info">
                    <div class="info-item">
                        <ion-icon name="mail-outline"></ion-icon>
                        <span>A confirmation email has been sent to your registered email address.</span>
                    </div>
                    <div class="info-item">
                        <ion-icon name="time-outline"></ion-icon>
                        <span>Your order will be processed within 24 hours.</span>
                    </div>
                    <div class="info-item">
                        <ion-icon name="location-outline"></ion-icon>
                        <span>You'll receive tracking information once your order ships.</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="success-actions">
                <a href="order-history.php" class="btn-primary">
                    <ion-icon name="receipt-outline"></ion-icon>
                    View Order History
                </a>
                <a href="index.php" class="btn-secondary">
                    <ion-icon name="home-outline"></ion-icon>
                    Continue Shopping
                </a>
            </div>

            <!-- Additional Information -->
            <div class="additional-info">
                <h3>What happens next?</h3>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Order Confirmation</h4>
                            <p>You'll receive an email confirmation with your order details.</p>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Processing</h4>
                            <p>We'll prepare your order for shipment within 1-2 business days.</p>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Shipping</h4>
                            <p>Your order will be shipped and you'll receive tracking information.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Support -->
            <div class="support-section">
                <h3>Need Help?</h3>
                <p>If you have any questions about your order, please don't hesitate to contact us.</p>
                <div class="support-buttons">
                    <a href="contact.php" class="btn-support">
                        <ion-icon name="mail-outline"></ion-icon>
                        Contact Support
                    </a>
                    <a href="tel:+1234567890" class="btn-support">
                        <ion-icon name="call-outline"></ion-icon>
                        Call Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Success Page JavaScript -->
<script>
    // TODO: MOVE THIS OUT
document.addEventListener('DOMContentLoaded', function() {
    // Animate the checkmark
    setTimeout(() => {
        document.querySelector('.checkmark').style.animation = 'checkmark 0.6s ease-in-out 0.2s both';
        document.querySelector('.checkmark-circle').style.animation = 'checkmark-circle 0.6s ease-in-out both';
    }, 300);

    // Clear cart from session storage if exists
    if (typeof(Storage) !== "undefined") {
        localStorage.removeItem('cart');
        sessionStorage.removeItem('cart');
    }

    // Auto-scroll to success message on mobile
    if (window.innerWidth <= 768) {
        document.querySelector('.success-title').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }
});
</script>

<?php require_once './includes/footer.php'; ?>
</html>
