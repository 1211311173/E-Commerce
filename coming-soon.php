<?php include_once('./includes/headerNav.php'); ?>

<div class="overlay" data-overlay></div>

<!-- HEADER -->
<header>
    <!-- Top head action, search etc -->
    <?php require_once './includes/topheadactions.php'; ?>
    <!-- Desktop navigation -->
    <?php require_once './includes/desktopnav.php'; ?>
    <!-- Mobile navigation -->
    <?php require_once './includes/mobilenav.php'; ?>
    <!-- Coming Soon Styles -->
    <link rel="stylesheet" href="css/coming-soon-styles.css">
</header>

<!-- MAIN CONTENT -->
<main>
    <div class="coming-soon-container">
        <div class="coming-soon-content">
            <div class="coming-soon-icon">
                <ion-icon name="construct-outline"></ion-icon>
            </div>
            
            <h1 class="coming-soon-title">Coming Soon!</h1>
            
            <p class="coming-soon-subtitle">
                We're working hard to bring you this feature. Our team is putting the finishing touches 
                on something amazing. Please check back soon for updates!
            </p>
            
            <a href="index.php" class="btn-return-home">
                <ion-icon name="home-outline"></ion-icon>
                Return to Homepage
            </a>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const content = document.querySelector('.coming-soon-content');
    if (content) {
        content.style.animationDelay = '0.3s';
    }
});
</script>

<?php require_once './includes/footer.php'; ?>