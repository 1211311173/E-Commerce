// Simple Category Scroller JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const categoryContainer = document.querySelector('.category-item-container');
    
    if (!categoryContainer) return;
    
    // Ensure smooth scrolling
    categoryContainer.style.scrollBehavior = 'smooth';
    
    // Make sure all items are properly positioned for full scroll access
    categoryContainer.style.display = 'flex';
    categoryContainer.style.overflowX = 'auto';
    categoryContainer.style.overflowY = 'hidden';
});