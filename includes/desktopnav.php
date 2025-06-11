<?php
require_once 'includes/session_helper.php';
?>
<!-- desktop navigation -->
<nav class="desktop-navigation-menu">
  <div class="container">
    <ul class="desktop-menu-category-list">

      <li class="menu-category">
        <a href="index.php?id=<?php echo isLoggedIn() ? getCurrentUserId() : 'unknown'; ?>"
          class="menu-title">
          Home
        </a>
      </li>

      <li class="menu-category">
        <a href="./category.php?category=<?php echo "men"; ?>" class="menu-title">Men's</a>
      </li>

      <li class="menu-category">
        <a href="./category.php?category=<?php echo "women"; ?>" class="menu-title">Women's</a>
      </li>

      <li class="menu-category">
        <a href="contact.php?id=<?php echo isLoggedIn() ? getCurrentUserId() : 'unknown'; ?>"
          class="menu-title">
          Contact
        </a>
      </li>

      <li class="menu-category">
        <a href="about.php?id=<?php echo isLoggedIn() ? getCurrentUserId() : 'unknown'; ?>"
          class="menu-title">About</a>
      </li>

      <!-- Profile Link Setup -->
      <?php if (isLoggedIn()): ?>
        <li class="menu-category" style="opacity:1">
          <a href="profile.php?id=<?php echo getCurrentUserId(); ?>"
            class="menu-title">
            Profile
          </a>
        </li>
      <?php endif; ?>

      <!-- Visit Admin Panel After Login -->
      <?php if (isAdmin()): ?>
        <li class="menu-category">
          <a href="admin/post.php" class="menu-title">
            Admin Panel
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>