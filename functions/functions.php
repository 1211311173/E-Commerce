<?php
    require_once './includes/config.php';

    // get banner products and details
    function get_banner_details(){
        global $conn;
        $query = "SELECT * FROM banner WHERE banner.banner_status = 1";


        return $result = mysqli_query($conn, $query);
    }


    // get top rated products
    function get_category_bar_products(){
        global $conn;
        $query = "SELECT * FROM category_bar WHERE category_bar.category_status = 1";

        return $result = mysqli_query($conn, $query);
    }


    // get categories
    function get_categories(){
        global $conn;
        $query = "SELECT * FROM category WHERE category.status = 1";

        return $result = mysqli_query($conn, $query);
    }

    // get clothes category
    function get_clothes_category(){
        global $conn;
        $query = "SELECT * FROM clothes WHERE clothes.coloth_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }
    // get footwear category
    function get_footwear_category(){
        global $conn;
        $query = "SELECT * FROM footwear WHERE footwear.footwear_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }
    // get jewelry category
    function get_jewelry_category(){
        global $conn;
        $query = "SELECT * FROM jewelry WHERE jewelry.jewelry_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }
    // get perfume category
    function get_perfume_category(){
        global $conn;
        $query = "SELECT * FROM perfume WHERE perfume.perfume_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }
    // get cosmetics category
    function get_cosmetics_category(){
        global $conn;
        $query = "SELECT * FROM cosmetics WHERE cosmetics.cosmetics_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }
    // get glasses category
    function get_glasses_category(){
        global $conn;
        $query = "SELECT * FROM glasses WHERE glasses.glasses_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }
    // get bags category
    function get_bags_category(){
        global $conn;
        $query = "SELECT * FROM bags WHERE bags.bags_category_status = 1";

        return $result = mysqli_query($conn, $query);
    }


    // get best sellers form product table
    function get_best_sellers(){
        // SELECT * FROM products ORDER BY products
        global $conn;
        // $query = "SELECT products.product_id, products.product_title, products.category_id, products.product_price, products.product_price, products.product_img FROM products
        // LEFT JOIN section
        // ON products.section_id = section.id
        // WHERE section.id = 6 AND section.status = 1";
        $query = "SELECT * FROM products LIMIT 4;";


        return $result = mysqli_query($conn, $query);
    }


    // get new sellers
    function get_new_arrivals(){
        global $conn;
        $query = "SELECT * FROM products LIMIT 8 OFFSET 0;";

        return $result = mysqli_query($conn, $query);
    }


    // get trending products
    function get_trending_products(){
//  SELECT *
// FROM your_table_name
// LIMIT (m - n + 1) OFFSET (n - 1);
// For example, if you want to select rows 3 to 7 from a table, you would replace (n - 1) with (3 - 1) and (m - n + 1) with (7 - 3 + 1). This would result in OFFSET 2 LIMIT 5. This query will retrieve the rows within the specified range from the table.

        global $conn;
        $query = "SELECT * FROM products LIMIT 8 OFFSET 8;";

        return $result = mysqli_query($conn, $query);
    }

    // get top rated products
    function get_top_rated_products(){
        global $conn;
        $query = "SELECT * FROM products LIMIT 8 OFFSET 16;";

        return $result = mysqli_query($conn, $query);
    }

    // get deal of the day
    function get_deal_of_day(){
        global $conn;
        $query = "SELECT * FROM deal_of_the_day WHERE deal_of_the_day.deal_status = 1";


        return $result = mysqli_query($conn, $query);
    }


    function get_new_products($offset, $limit){
        // "SELECT * FROM products ORDER BY products.product_id DESC LIMIT {$offset},{$limit}";
        global $conn;
        $query = "SELECT * FROM products ORDER BY products.product_id DESC LIMIT {$offset},{$limit}";


        return $result = mysqli_query($conn, $query);
    }


    function display_electronic_category(){
        global $connect;
        $query = "SELECT * FROM category_electronics WHERE category_electronics.status = 1";


        return $result = mysqli_query($connect, $query);
    }

    // get product through id from product table
    function get_product($id){
        global $secureDB;

        // Validate the product ID
        $product_id = InputValidator::validateInt($id, 1);
        if ($product_id === false) {
            return false;
        }

        $query = "SELECT * FROM products WHERE products.product_id = ?";
        return $secureDB->select($query, [$product_id], 'i');
    }

        // get specific category
    function get_items_by_category_items($category){
        global $secureDB;

        // Validate and sanitize category
        $safe_category = InputValidator::sanitizeString($category, 50);
        if (empty($safe_category)) {
            return false;
        }

        $query = "SELECT * FROM products WHERE products.product_catag = ? AND products.status = 1";
        return $secureDB->select($query, [$safe_category], 's');
    }

    // get recent orders for notification toast
    function get_recent_orders() {
        global $conn;
        $query = "SELECT o.order_id, o.order_date, oi.product_id, p.product_title, p.product_img, c.customer_fname 
                  FROM orders o 
                  JOIN order_items oi ON o.order_id = oi.order_id 
                  JOIN products p ON oi.product_id = p.product_id 
                  JOIN customer c ON o.customer_id = c.customer_id 
                  WHERE o.order_status = 'confirmed' 
                  ORDER BY o.order_date DESC 
                  LIMIT 5";
        
        return mysqli_query($conn, $query);
    }

    // get product count by category
    function get_product_count_by_category($category) {
        global $secureDB;
        
        // Validate and sanitize category
        $safe_category = InputValidator::sanitizeString($category, 50);
        if (empty($safe_category)) {
            return 0;
        }
        
        // First check if it's a subcategory (case-insensitive)
        $query = "SELECT COUNT(*) as count FROM products WHERE LOWER(subcategory) = LOWER(?) AND status = 1";
        $result = $secureDB->select($query, [$safe_category], 's');
        
        if ($result && $row = $result->fetch_assoc()) {
            if ($row['count'] > 0) {
                return $row['count'];
            }
        }
        
        // If no results in subcategory, check if it's a broad category (case-insensitive)
        $query = "SELECT COUNT(*) as count FROM products WHERE LOWER(product_catag) = LOWER(?) AND status = 1";
        $result = $secureDB->select($query, [$safe_category], 's');
        
        if ($result && $row = $result->fetch_assoc()) {
            return $row['count'];
        }
        
        return 0;
    }
?>