// Initialize countdown timers for all deal of the day items
function initializeCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');

    countdowns.forEach(countdown => {
        const endTime = new Date(countdown.dataset.endTime).getTime();

        // Update countdown every second
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const distance = endTime - now;

            // If countdown is finished
            if (distance < 0) {
                clearInterval(timer);
                countdown.innerHTML = '<p class="countdown-expired">Offer has ended!</p>';
                return;
            }

            // Calculate time units
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Update display
            countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
            countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
            countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
            countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
        }, 1000);
    });
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCountdowns);
} else {
    initializeCountdowns();
} 