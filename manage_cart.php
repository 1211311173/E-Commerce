<?php
require_once 'includes/session_helper.php';

if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['mycart'])) {
        $_SESSION['mycart'] = [];
    }
    
    $item_id_column = array_column($_SESSION['mycart'], 'product_id');
    if (in_array($_POST['product_id'], $item_id_column)) {
        // Product already in cart, redirect back
        header('location:viewdetail.php?id=' . $_POST['product_id'] . '&category=' . $_POST['product_category'] . '&status=alreadyincart');
        exit;
    }
    
    // Add new item to cart
    $count_cart = count($_SESSION['mycart']);
    $_SESSION['mycart'][$count_cart] = array(
        'name' => $_POST['product_name'],
        'price' => $_POST['product_price'],
        'product_id' => $_POST['product_id'],
        'category' => $_POST['product_category'],
        'product_qty' => $_POST['product_qty'],
        'product_img' => $_POST['product_img']
    );
    header('location:viewdetail.php?id=' . $_POST['product_id'] . '&category=' . $_POST['product_category'] . '&status=added');
    exit;
}

// Handle removing an item from the cart
if (isset($_POST['remove_from_cart']) && isset($_POST['product_id_to_remove'])) {
    if (isset($_SESSION['mycart'])) {
        foreach ($_SESSION['mycart'] as $key => $value) {
            if ($value['product_id'] == $_POST['product_id_to_remove']) {
                unset($_SESSION['mycart'][$key]);
                // Re-index the array to prevent issues with non-contiguous keys
                $_SESSION['mycart'] = array_values($_SESSION['mycart']);
                break;
            }
        }
    }
    header('location:cart.php?status=removed');
    exit;
}

// If the script is accessed without a specific action, redirect to index
if (empty($_POST)) {
    header('location:index.php');
    exit;
}
?>