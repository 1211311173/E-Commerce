<?php
// Prevent any output before JSON response
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../includes/config.php';
require_once '../includes/security.php';

// Set JSON header
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

// Handle login check separately
if ($action === 'check_login') {
    echo json_encode(['is_logged_in' => isset($_SESSION['id'])]);
    exit();
}

// For other actions, require login
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add favorites']);
    exit();
}

$customer_id = $_SESSION['id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

// Get total favorites count for the user
function getFavoritesCount($customer_id) {
    global $secureDB;
    try {
        $query = "SELECT COUNT(*) as count FROM favorites WHERE customer_id = ?";
        $result = $secureDB->select($query, [$customer_id], 'i');
        if ($result && $row = $result->fetch_assoc()) {
            return $row['count'];
        }
        return 0;
    } catch (Exception $e) {
        return 0;
    }
}

// Check if product is already in favorites
function isInFavorites($customer_id, $product_id) {
    global $secureDB;
    try {
        $query = "SELECT id FROM favorites WHERE customer_id = ? AND product_id = ?";
        $result = $secureDB->select($query, [$customer_id, $product_id], 'ii');
        return $result && $result->num_rows > 0;
    } catch (Exception $e) {
        return false;
    }
}

try {
    switch ($action) {
        case 'add':
            if (!isInFavorites($customer_id, $product_id)) {
                $query = "INSERT INTO favorites (customer_id, product_id) VALUES (?, ?)";
                $result = $secureDB->insert($query, [$customer_id, $product_id], 'ii');
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Added to favorites',
                        'count' => getFavoritesCount($customer_id)
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Already in favorites']);
            }
            break;

        case 'remove':
            $query = "DELETE FROM favorites WHERE customer_id = ? AND product_id = ?";
            $result = $secureDB->delete($query, [$customer_id, $product_id], 'ii');
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Removed from favorites',
                    'count' => getFavoritesCount($customer_id)
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
            }
            break;

        case 'count':
            echo json_encode([
                'success' => true,
                'count' => getFavoritesCount($customer_id)
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
} 