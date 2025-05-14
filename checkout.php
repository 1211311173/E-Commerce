<?php include_once('./includes/headerNav.php'); ?>
<div class="overlay" data-overlay></div>
<!--
    - HEADER
  -->
<header>
    <!-- top head action, search etc in php -->
    <!-- inc/topheadactions.php -->
    <?php require_once './includes/topheadactions.php'; ?>
    <!-- mobile nav in php -->
    <!-- inc/mobilenav.php -->
    <?php require_once './includes/mobilenav.php'; ?>
    <link rel="stylesheet" href="css/checkout-styles.css">
</header>

<body>

    <div class="appointments-section">

        <div class="appointment-heading">
            <p class="appointment-head">CheckOut</p>
            <span class="appointment-line"></span>

        </div>

        <div class="inner-appointment">

            <section class="edit-detail-field">
                <div class="Add-child-section">
                    <div class="child-detail-inner">

                        <div class="child-fields1">
                            <input type="text" style="color: #676767;" placeholder="First Name">
                        </div>
                        <div class="child-fields3">
                            <input type="text" style="color: #676767;" placeholder="Last Name">
                        </div>

                    </div>
                    <div class="child-detail-inner">

                        <div class="child-fields child-fields4">
                            <input type="text" placeholder="P-134">
                        </div>
                        <div class="child-fields child-fields5 ">
                            <input type="text" placeholder="A5">
                        </div>

                    </div>
                    <div class="child-detail-inner">

                        <div class="child-fields child-fields6">
                            <input type="text" placeholder="Manchester">
                        </div>
                        <div class="child-fields child-fields7 ">
                            <input type="text" placeholder="38000">
                        </div>

                    </div>

                    <div class="child-detail-inner">

                        <div class="child-fields Address-field">
                            <input type="text" style="color: #676767;" placeholder="United kingdom">

                        </div>
                    </div>
                    <div class="child-detail-inner">

                        <div class="child-fields child-fields8">
                            <input type="text" placeholder="+1 0000-0000-0000">
                        </div>
                        <div class="child-fields child-fields9">
                            <input type="text" placeholder="example@email.com">
                        </div>
                    </div>
                    <div class="child-register-btn">
                        <span class="error-ms"></span>
                        <p onclick="checkFields()">Proceed To Pay</p>
                    </div>
                </div>
            </section>
        </div>
        <script src="js/checkout-script.js"></script>
    </div>
</body>
<?php require_once './includes/footer.php'; ?>