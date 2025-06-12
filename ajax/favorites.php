<?php
session_start();
header('Content-Type: application/json');

// Include database connection
require_once '../includes/config.php';

// Check if action is set
if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'];

try {
    switch ($action) {
        case 'check_login':
            echo json_encode([
                'success' => true,
                'is_logged_in' => isset($_SESSION['id'])
            ]);
            break;
            
        case 'check':
            if (!isset($_SESSION['id'])) {
                echo json_encode(['success' => false, 'message' => 'Not logged in']);
                exit;
            }
            
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }
            
            $product_id = (int)$_POST['product_id'];
            $customer_id = (int)$_SESSION['id'];
            
            $check_sql = "SELECT id FROM favorites WHERE customer_id = ? AND product_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ii", $customer_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            echo json_encode([
                'success' => true,
                'is_favorited' => $result->num_rows > 0
            ]);
            break;
            
        case 'add':
            if (!isset($_SESSION['id'])) {
                echo json_encode(['success' => false, 'message' => 'Please login to add favorites']);
                exit;
            }
            
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }
            
            $product_id = (int)$_POST['product_id'];
            $customer_id = (int)$_SESSION['id'];
            
            // Check if already exists
            $check_sql = "SELECT id FROM favorites WHERE customer_id = ? AND product_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ii", $customer_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Already in favorites']);
                exit;
            }
            
            // Add to favorites
            $insert_sql = "INSERT INTO favorites (customer_id, product_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ii", $customer_id, $product_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Added to favorites!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add to favorites']);
            }
            break;
            
        case 'remove':
            if (!isset($_SESSION['id'])) {
                echo json_encode(['success' => false, 'message' => 'Please login']);
                exit;
            }
            
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }
            
            $product_id = (int)$_POST['product_id'];
            $customer_id = (int)$_SESSION['id'];
            
            $delete_sql = "DELETE FROM favorites WHERE customer_id = ? AND product_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("ii", $customer_id, $product_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Removed from favorites!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
            }
            break;
            
        case 'count':
            if (!isset($_SESSION['id'])) {
                echo json_encode(['success' => true, 'count' => 0]);
                exit;
            }
            
            $customer_id = (int)$_SESSION['id'];
            $count_sql = "SELECT COUNT(*) as count FROM favorites WHERE customer_id = ?";
            $stmt = $conn->prepare($count_sql);
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode(['success' => true, 'count' => (int)$row['count']]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>