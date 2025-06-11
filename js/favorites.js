document.addEventListener('DOMContentLoaded', function () {
    // Initialize favorites count
    updateFavoritesCount();

    // Add click handlers to all heart icons
    document.querySelectorAll('.heart-icon').forEach(icon => {
        icon.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.productId;

            // Skip if it's the header heart icon (productId = 0)
            if (productId === '0') {
                window.location.href = 'favorites.php';
                return;
            }

            const isFavorited = this.classList.contains('favorited');

            // Check if user is logged in
            fetch('ajax/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=check_login'
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.is_logged_in) {
                        // Redirect to login page
                        window.location.href = 'login.php';
                        return;
                    }

                    // If logged in, proceed with favorite action
                    if (isFavorited) {
                        removeFromFavorites(productId, this);
                    } else {
                        addToFavorites(productId, this);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred');
                });
        });
    });
});

function addToFavorites(productId, icon) {
    fetch('ajax/favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                icon.classList.add('favorited');
                icon.querySelector('ion-icon').setAttribute('name', 'heart');
                updateFavoritesCount();
                showToast(data.message);
            } else {
                showToast(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred');
        });
}

function removeFromFavorites(productId, icon) {
    fetch('ajax/favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                icon.classList.remove('favorited');
                icon.querySelector('ion-icon').setAttribute('name', 'heart-outline');
                updateFavoritesCount();
                showToast(data.message);
            } else {
                showToast(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred');
        });
}

function updateFavoritesCount() {
    fetch('ajax/favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=count'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update all favorites counters
                document.querySelectorAll('.favorites-count').forEach(counter => {
                    counter.textContent = data.count;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 100);

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
} 