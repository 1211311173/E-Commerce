# E-commerce Website

## Introduction

This document provides an overview of an e-commerce website built using HTML, CSS, Bootstrap, JavaScript, PHP, and MySQL. The website consists of both user-side and admin-side functionalities, allowing customers to browse and purchase products while enabling administrators to manage inventory, orders, and customer data.

## Technologies Used

- HTML: Used for creating the structure and content of web pages.
- CSS: Utilized for styling the website and enhancing its visual appearance.
- Bootstrap: Used as a CSS framework to provide responsive design and pre-designed components.
- JavaScript: Implemented for client-side interactivity and dynamic functionality.
- PHP: Used as the server-side programming language to handle requests, process data, and interact with the database.
- MySQL/MariaDB: Utilized as the database management system to store and retrieve data related to products, orders, and customers.
- Stripe API: Integrated for secure payment processing and transaction handling.

## User-Side Functionality

The user-side of the e-commerce website includes the following key features:

1. Home Page:

- Displays featured products, promotions, and categories with modern carousel navigation.
- Includes a search bar to search for specific products.
- Provides navigation links to different product categories.
- Features Deal of the Day section with countdown timers and auto-slide functionality.

2. Product Listing:

- Displays a list of products with details such as name, price, and images.
- Enables users to filter and sort products based on different criteria.
- Allows users to view individual product details.
- Modern arrow-based navigation for category browsing.

3. Favorites System:

- Allows users to add/remove products to their favorites list.
- Displays favorite counter in the header showing total saved items.
- Dedicated favorites page showing all user-saved products.
- Real-time animations and notifications for favorite actions.

4. Shopping Cart:

- Modern card-based design with responsive layout.
- Allows users to add products to the cart for purchase.
- Displays the selected products, quantities, and total price.
- Provides options to modify quantities, remove items, or proceed to checkout.

5. User Authentication:

- Enables users to register, log in, and log out.
- Stores user information securely and provides personalized experiences.
- Session management for favorites and cart functionality.

6. Checkout Process:

- Fully integrated Stripe payment gateway for secure transactions.
- Supports multiple purchase flows (cart checkout and direct "Buy Now").
- Collects user information (shipping address, payment details) for order processing.
- Success and cancellation page handling.

## Admin-Side Functionality

The admin-side of the e-commerce website includes the following key features:

1. Product Management:

- Enables administrators to add, edit, and delete products.
- Allows administrators to manage product categories, prices, and inventory.

2. Order Management:

- Provides an overview of incoming orders.
- Enables administrators to update order statuses (e.g., processing, shipped, delivered).
- Generates order invoices and packing slips.

3. Customer Management:

- Stores and manages customer information, including registration details and order history.
- Enables administrators to view and edit customer profiles.

4. Inventory Management:

- Tracks product stock levels and alerts administrators of low stock.
- Provides insights into product performance and sales statistics.

## Recent Updates & Improvements

### Database System
- **Upgraded** from MariaDB 10.4.27 to latest version for enhanced security and performance
- **Implemented** proper database migration process with data preservation
- **Resolved** known security vulnerabilities (CVEs) from legacy database version

### Payment Integration
- **Integrated** Stripe payment gateway replacing non-functional checkout system
- **Implemented** secure payment processing for both cart and direct purchases
- **Added** proper error handling and session management for transactions
- **Note**: Order history requires public hosting for webhook integration

### Favorites System
- **Created** comprehensive favorites functionality with database integration
- **Added** real-time favorite counter in header navigation
- **Implemented** favorites page displaying user's saved items
- **Enhanced** with floating animations and visual feedback

### User Interface Modernization
- **Replaced** default horizontal scrollbars with custom arrow-based navigation
- **Implemented** auto-slide functionality for banner carousels and Deal of the Day
- **Redesigned** cart page from basic table layout to modern card-based design
- **Enhanced** responsive design for consistent experience across all devices
- **Added** custom scrollbar styling and smooth transitions

### Navigation & UX Improvements
- **Unified** navigation controls across all product sections
- **Added** intuitive arrow buttons for category browsing
- **Implemented** automatic slideshow features for better product showcase
- **Fixed** non-functional buttons by redirecting to coming-soon page

## Installation Requirements

### Prerequisites
- Apache or Nginx web server
- PHP 7.0 or later
- MariaDB/MySQL (latest version recommended)
- Composer (for Stripe PHP SDK)
- Git (optional)

### Additional Setup for Payment Integration
1. Create a Stripe account and obtain API keys
2. Create a `.env` file in the root directory with:
   ```
   STRIPE_SECRET_KEY=your_stripe_secret_key_here
   STRIPE_PUBLISHABLE_KEY=your_stripe_publishable_key_here
   ```
3. Install Stripe PHP SDK via Composer:
   ```
   composer install
   ```

## Conclusion

The e-commerce website now offers a modern, secure, and user-friendly shopping experience with integrated payment processing, favorites functionality, and responsive design. The recent updates have significantly improved both the visual appeal and functional capabilities of the platform, providing customers with an intuitive interface and administrators with robust management tools.

# E-commerce Website - Todo List

## Payment Method

- [x] Implement payment gateway integration (Stripe) to allow customers to make secure online payments.
- [x] Set up server-side validation and processing of payment transactions.
- [x] Test the payment functionality thoroughly, including successful and failed payment scenarios.
- [x] Handle and display appropriate error messages for payment failures.
- [x] Ensure the security and encryption of sensitive payment information.
- [ ] Implement order history functionality (requires public hosting for webhooks).

## Code Cleanup

- [x] Review the existing codebase for any unused or redundant code.
- [x] Remove commented-out code or debugging statements that are no longer needed.
- [x] Refactor code to follow best practices and coding standards.
- [x] Improve code readability by using meaningful variable and function names.
- [ ] Identify and resolve any code duplication or performance bottlenecks.

## Optimization

- [ ] Perform a thorough analysis of the website's performance using tools like Google PageSpeed Insights or Lighthouse.
- [ ] Optimize images by compressing them or using modern image formats like WebP.
- [ ] Minify CSS and JavaScript files to reduce their file sizes.
- [ ] Implement caching mechanisms for static resources to improve loading times.
- [ ] Evaluate and optimize database queries to improve overall site performance.
- [ ] Consider implementing content delivery networks (CDNs) to distribute website assets and reduce server load.

## Extra

- [ ] Implement social media sharing functionality to allow customers to share products or promotions.
- [x] Enhance the user interface by adding animations or transitions for a more engaging experience.
- [ ] Add customer reviews and ratings feature to enable users to provide feedback on products.
- [x] Implement a wishlist or favorites functionality for users to save products for future reference.
- [x] Enhance search functionality by implementing filters, sorting options, or predictive search suggestions.
- [ ] Integrate email notifications for order confirmations, shipment updates, and promotional campaigns.

Feel free to prioritize and modify this todo list based on the specific requirements and priorities of your e-commerce website.

# E-commerce Website - Credit Section

We would like to acknowledge the following repositories and their contributors for their valuable contributions to the development of our e-commerce website:

## Front-end Template

- Repository: [codewithsadee/anon-ecommerce-website](https://github.com/codewithsadee/anon-ecommerce-website)
- Contributors:
  - Sadee Samaraweera ([@codewithsadee](https://github.com/codewithsadee))

We utilized the front-end template from the above repository as the foundation for the user-side of our e-commerce website. This template provided a solid starting point with its well-structured HTML, CSS, and JavaScript code, along with pre-designed components and responsive design elements.

## Admin Panel Template

- Repository: [Bhabishya-123/E-commerce](https://github.com/Bhabishya-123/E-commerce)
- Contributors:
  - Bhabishya Sharma ([@Bhabishya-123](https://github.com/Bhabishya-123))

We utilized the admin panel template from the above repository to build the administrative interface of our e-commerce website. The provided template offered a user-friendly and intuitive design, along with necessary features and functionalities to manage products, orders, and customer information effectively.

We express our gratitude to the contributors of these repositories for their hard work, dedication, and open-source spirit, which greatly contributed to the development of our e-commerce website.

Please note that while we utilized these repositories as a starting point, we have made significant customizations, enhancements, and added our own code to modernize the website and tailor it to current e-commerce standards.

# E-commerce Website - Installation Guide

This installation guide will help you set up and run the e-commerce website on your local development environment. The guide assumes you have basic knowledge of web development and have the necessary software installed.

## Prerequisites

Before proceeding with the installation, make sure you have the following software installed on your system:

- Apache or Nginx web server
- PHP 7.0 or later
- MariaDB/MySQL database (latest version recommended)
- Composer (for Stripe PHP SDK)
- Git (optional)

## Installation Steps

Follow these steps to install and run the e-commerce website:

1. Clone the Repository:

   - If you have Git installed, open a terminal and run the following command:
     ```
     git clone https://github.com/ShahandFahad/E-Commerce.git
     ```
   - Alternatively, you can download the repository as a ZIP file from the following URL: [https://github.com/ShahandFahad/E-Commerce/archive/refs/heads/main.zip](https://github.com/ShahandFahad/E-Commerce/archive/refs/heads/main.zip)
   - Extract the contents of the ZIP file to a directory of your choice.

2. Configure Database:

   - Create a new MariaDB/MySQL database for the e-commerce website.
   - Import the provided SQL file (`database.sql`) into your newly created database. This file contains the necessary tables and sample data for the website.

3. Install Dependencies:

   - Navigate to the project directory and run:
     ```
     composer install
     ```
   - This will install the Stripe PHP SDK and other required dependencies.

4. Configure Environment Variables:

   - Create a `.env` file in the root directory.
   - Add your Stripe API credentials:
     ```
     STRIPE_SECRET_KEY=your_stripe_secret_key_here
     STRIPE_PUBLISHABLE_KEY=your_stripe_publishable_key_here
     ```

5. Update Configuration:

   - Open the `config.php` file located in the includes directory.
   - Update the database credentials (hostname, username, password, database name) to match your local setup.
   - Save the changes.

6. Start Web Server:

   - Start your Apache or Nginx web server and ensure it is running correctly.

7. Access the Website:
   - Open your web browser and navigate to the URL where the website is hosted (e.g., `http://localhost/E-Commerce`).
   - You should now see the home page of the e-commerce website.

## Admin Panel

To access the admin panel of the e-commerce website, follow these steps:

1. Open your web browser and navigate to the admin URL (e.g., `http://localhost/E-Commerce/login`).
2. Login using the default admin credentials:
   - Username: `admin`
   - Password: `adminfahad`
3. Once logged in, you can manage products, orders, customers, and other administrative tasks from the admin panel.

## Testing Payment Integration

To test the Stripe payment integration:

1. Use Stripe's test card numbers (e.g., 4242424242424242)
2. Add products to cart and proceed to checkout
3. Complete the payment process using test credentials
4. Verify successful payment processing

Congratulations! You have successfully installed and set up the e-commerce website on your local development environment. You can now customize the website, add your products, and explore its various features including the new favorites system, modern navigation, and secure payment processing.

Please note that this installation guide assumes a basic local development environment. For deploying the website to a production server, additional configuration and security considerations may apply, especially for payment gateway integration and SSL certificates.