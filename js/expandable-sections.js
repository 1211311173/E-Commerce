// Simple Arrow Navigation for Product Sections

document.addEventListener('DOMContentLoaded', function() {
    // Initialize navigation for each section
    const sections = ['new-arrivals', 'trending', 'top-rated'];
    
    sections.forEach(sectionName => {
        const container1 = document.getElementById(`${sectionName}-container-1`);
        const container2 = document.getElementById(`${sectionName}-container-2`);
        const arrow = document.getElementById(`${sectionName}-arrow`);
        
        if (!container1 || !container2 || !arrow) return;
        
        let currentPage = 1; // Start with page 1 (container 1 visible)
          // Initially show container 1, hide container 2
        container1.style.display = 'block';
        container1.style.setProperty('display', 'block', 'important');
        container2.style.display = 'none';
        container2.style.setProperty('display', 'none', 'important');
        arrow.innerHTML = '›'; // Right arrow initially
          arrow.addEventListener('click', function() {
            if (currentPage === 1) {
                // Switch to page 2
                container1.style.setProperty('display', 'none', 'important');
                container2.style.setProperty('display', 'block', 'important');
                arrow.innerHTML = '‹'; // Left arrow
                currentPage = 2;
            } else {
                // Switch back to page 1
                container2.style.setProperty('display', 'none', 'important');
                container1.style.setProperty('display', 'block', 'important');
                arrow.innerHTML = '›'; // Right arrow
                currentPage = 1;
            }
        });
        
        // Hide arrow if second container has no items
        if (container2.children.length === 0) {
            arrow.style.display = 'none';
        }
    });
});