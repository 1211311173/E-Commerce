// Order History JavaScript Functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize order history functionality
    initOrderHistory();

    function initOrderHistory() {
        // Add smooth scrolling for better UX
        addSmoothScrolling();
        
        // Add search and filter functionality (for future use)
        initSearchFilter();
        
        // Add order status animations
        animateOrderStatuses();
        
        // Add click handlers for order items
        addOrderClickHandlers();
    }

    function addSmoothScrolling() {
        // Smooth scroll to sections
        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    function initSearchFilter() {
        // Create search filter functionality (for future implementation)
        const searchInput = document.querySelector('#order-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const orderItems = document.querySelectorAll('.order-item');
                
                orderItems.forEach(item => {
                    const orderText = item.textContent.toLowerCase();
                    if (orderText.includes(searchTerm)) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }

    function animateOrderStatuses() {
        // Animate order status badges on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statusBadge = entry.target.querySelector('.order-status');
                    if (statusBadge) {
                        statusBadge.style.animation = 'pulse 0.6s ease-out';
                    }
                }
            });
        }, observerOptions);

        const orderItems = document.querySelectorAll('.order-item');
        orderItems.forEach(item => {
            observer.observe(item);
        });
    }

    function addOrderClickHandlers() {
        // Add click handlers for order items to show more details
        const orderItems = document.querySelectorAll('.order-item');
        
        orderItems.forEach(item => {
            item.addEventListener('click', function() {
                // Toggle expanded view (for future implementation)
                this.classList.toggle('expanded');
                
                // Add visual feedback
                this.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
    }

    // Utility function to format dates
    function formatDate(dateString) {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    // Utility function to format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    // Function to show order details modal (for future implementation)
    function showOrderDetails(orderId) {
        // This would open a modal with detailed order information
        console.log('Show details for order:', orderId);
        
        // Future implementation:
        // - Fetch order details from server
        // - Display in modal
        // - Show tracking information
        // - Allow order actions (cancel, return, etc.)
    }

    // Function to track order (for future implementation)
    function trackOrder(orderId) {
        // This would open tracking information
        console.log('Track order:', orderId);
        
        // Future implementation:
        // - Redirect to tracking page
        // - Show tracking modal
        // - Display shipping updates
    }

    // Function to reorder items (for future implementation)
    function reorderItems(orderId) {
        // This would add order items back to cart
        console.log('Reorder items from order:', orderId);
        
        // Future implementation:
        // - Fetch order items
        // - Add to cart
        // - Redirect to cart page
        // - Show success message
    }

    // Add CSS animation classes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .order-item.expanded {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        .slide-in {
            animation: slideInUp 0.4s ease-out;
        }
    `;
    document.head.appendChild(style);
});

// Export functions for use in other files if needed
window.OrderHistory = {
    formatDate: function(dateString) {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('en-US', options);
    },
    
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },
    
    showOrderDetails: function(orderId) {
        console.log('Show details for order:', orderId);
    },
    
    trackOrder: function(orderId) {
        console.log('Track order:', orderId);
    },
    
    reorderItems: function(orderId) {
        console.log('Reorder items from order:', orderId);
    }
};
