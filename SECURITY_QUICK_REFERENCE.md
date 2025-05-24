# Security Quick Reference Guide

## 🛡️ SQL Injection Prevention - Quick Reference

### ✅ DO - Use These Secure Patterns

#### 1. Database Queries
```php
// ✅ SECURE - Use prepared statements
$sql = "SELECT * FROM products WHERE product_id = ?";
$result = $secureDB->select($sql, [$product_id], 'i');

// ✅ SECURE - Multiple parameters
$sql = "SELECT * FROM products WHERE category = ? AND price < ?";
$result = $secureDB->select($sql, [$category, $max_price], 'sd');
```

#### 2. Input Validation
```php
// ✅ SECURE - Validate integers
$id = InputValidator::validateInt($_GET['id'], 1);
if ($id === false) {
    header("Location: error.php?invalid_id");
    exit();
}

// ✅ SECURE - Validate emails
$email = InputValidator::validateEmail($_POST['email']);
if (!$email) {
    echo "Invalid email format";
    return;
}

// ✅ SECURE - Sanitize strings
$name = InputValidator::sanitizeString($_POST['name'], 100);
```

#### 3. Search Functionality
```php
// ✅ SECURE - Validate search terms
$search_term = InputValidator::validateSearchTerm($_GET['search']);
if ($search_term === false) {
    echo "Invalid search term";
    return;
}

$sql = "SELECT * FROM products WHERE title LIKE ?";
$term = '%' . $search_term . '%';
$result = $secureDB->select($sql, [$term], 's');
```

### ❌ DON'T - Avoid These Vulnerable Patterns

#### 1. Direct String Concatenation
```php
// ❌ VULNERABLE - Never do this
$sql = "SELECT * FROM products WHERE id = {$_GET['id']}";
$sql = "SELECT * FROM users WHERE email = '{$_POST['email']}'";
```

#### 2. Unvalidated Input
```php
// ❌ VULNERABLE - No validation
$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = $id";
```

#### 3. mysqli_real_escape_string Only
```php
// ❌ INSUFFICIENT - Not enough protection
$email = mysqli_real_escape_string($conn, $_POST['email']);
$sql = "SELECT * FROM users WHERE email = '$email'";
```

## 🔧 Common Use Cases

### User Authentication
```php
// Secure login
$email = InputValidator::validateEmail($_POST['email']);
if (!$email) {
    echo "Invalid email";
    exit();
}

$sql = "SELECT * FROM customer WHERE customer_email = ?";
$result = $secureDB->select($sql, [$email], 's');

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($_POST['password'], $user['customer_pwd'])) {
        // Login successful
    }
}
```

### Product Management
```php
// Secure product creation
$title = InputValidator::sanitizeString($_POST['title'], 255);
$price = InputValidator::validateFloat($_POST['price'], 0);
$category = InputValidator::sanitizeString($_POST['category'], 50);

if ($price === false) {
    echo "Invalid price";
    exit();
}

$sql = "INSERT INTO products (title, price, category) VALUES (?, ?, ?)";
$result = $secureDB->insert($sql, [$title, $price, $category], 'sds');
```

### Pagination
```php
// Secure pagination
$page = InputValidator::validateInt($_GET['page'], 1);
$limit = InputValidator::validateInt($_GET['limit'], 1, 100);

if ($page === false) $page = 1;
if ($limit === false) $limit = 10;

$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM products LIMIT ?, ?";
$result = $secureDB->select($sql, [$offset, $limit], 'ii');
```

## 📋 Parameter Type Reference

| Type | Description | Example |
|------|-------------|---------|
| `s` | String | `'hello'`, `'user@email.com'` |
| `i` | Integer | `123`, `0`, `-45` |
| `d` | Double/Float | `12.34`, `0.0`, `-45.67` |
| `b` | Blob | Binary data |

### Multiple Parameters
```php
// String, Integer, Double
$secureDB->select($sql, [$name, $id, $price], 'sid');

// All strings
$secureDB->select($sql, [$email, $name, $address], 'sss');
```

## 🚨 Emergency Checklist

If you suspect SQL injection vulnerability:

1. **Immediate Actions:**
   - [ ] Stop using the vulnerable code
   - [ ] Check logs for suspicious activity
   - [ ] Validate all user inputs

2. **Fix Implementation:**
   - [ ] Replace with prepared statements
   - [ ] Add input validation
   - [ ] Test with malicious inputs

3. **Verification:**
   - [ ] Run security tests
   - [ ] Code review
   - [ ] Penetration testing

## 🔍 Testing Your Code

### Quick Test Commands
```php
// Test with SQL injection payload
$malicious_id = "1' OR '1'='1";
$safe_id = InputValidator::validateInt($malicious_id, 1);
// Should return false

// Test search sanitization
$malicious_search = "'; DROP TABLE products; --";
$safe_search = InputValidator::validateSearchTerm($malicious_search);
// Should return false
```

### Run Security Test
```bash
# Access the security test script
http://your-domain/security_test.php
```

## 📞 Need Help?

1. **Review the full documentation:** `SECURITY_IMPLEMENTATION.md`
2. **Check the security functions:** `includes/security.php`
3. **Run the test script:** `security_test.php`

## 🎯 Remember

- **Always validate input** before using it
- **Use prepared statements** for all database queries
- **Sanitize output** when displaying user data
- **Test your code** with malicious inputs
- **Keep security updated** as you add new features

---
*This guide covers the essential security patterns implemented in your E-Commerce application. Follow these patterns consistently to maintain security.*
