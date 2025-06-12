document.addEventListener("DOMContentLoaded", function () {
  updateFavoritesCount();

  checkIfProductIsFavorited();

  document.querySelectorAll(".heart-icon").forEach((icon, index) => {
    icon.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      const productId = this.dataset.productId;
      const isFavorited = this.classList.contains("favorited");

      fetch("ajax/favorites.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "action=check_login",
      })
        .then((response) => response.json())
        .then((data) => {
          if (!data.is_logged_in) {
            if (
              confirm(
                "Please login to add items to favorites. Redirect to login page?"
              )
            ) {
              const currentUrl = encodeURIComponent(window.location.href);
              window.location.href = `login.php?redirect=${currentUrl}`;
            }
            return;
          }

          if (isFavorited) {
            removeFromFavorites(productId, this);
          } else {
            addToFavorites(productId, this);
          }
        });
    });
  });
});

function checkIfProductIsFavorited() {
  const heartIcon = document.querySelector(".heart-icon[data-product-id]");
  if (!heartIcon) return;

  const productId = heartIcon.dataset.productId;

  fetch("ajax/favorites.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=check&product_id=${productId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.is_favorited) {
        heartIcon.classList.add("favorited");
        heartIcon.querySelector("ion-icon").setAttribute("name", "heart");
      }
    });
}

function addToFavorites(productId, icon) {
  icon.classList.add("loading");

  fetch("ajax/favorites.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=add&product_id=${productId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      icon.classList.remove("loading");

      if (data.success) {
        icon.classList.add("favorited");
        icon.querySelector("ion-icon").setAttribute("name", "heart");

        updateFavoritesCount();
        showFloatingAnimation(icon, "success", "+1");
      } else {
        showFloatingAnimation(icon, "error", "!");
      }
    });
}

function removeFromFavorites(productId, icon) {
  icon.classList.add("loading");

  fetch("ajax/favorites.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=remove&product_id=${productId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      icon.classList.remove("loading");

      if (data.success) {
        icon.classList.remove("favorited");
        icon.querySelector("ion-icon").setAttribute("name", "heart-outline");
        updateFavoritesCount();
        showFloatingAnimation(icon, "remove", "-1");
      } else {
        showFloatingAnimation(icon, "error", "!");
      }
    });
}

function updateFavoritesCount() {
  fetch("ajax/favorites.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "action=count",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.querySelectorAll(".favorites-count").forEach((counter) => {
          counter.textContent = data.count;
        });

        const headerFavCount = document.querySelector(
          '.header-user-actions .action-btn[title="Favorites"] .count'
        );
        if (headerFavCount) {
          headerFavCount.textContent = data.count;
        }
      }
    });
}

function showFloatingAnimation(element, type, text) {
  const rect = element.getBoundingClientRect();
  const centerX = rect.left + rect.width / 2;
  const centerY = rect.top + rect.height / 2;

  const floatingEl = document.createElement("div");
  floatingEl.textContent = text;
  floatingEl.className = `floating-animation floating-${type}`;

  if (!document.querySelector("#floating-animation-styles")) {
    const styles = document.createElement("style");
    styles.id = "floating-animation-styles";
    styles.textContent = `
            .floating-animation {
                position: fixed;
                font-size: 20px;
                font-weight: bold;
                pointer-events: none;
                z-index: 10000;
                opacity: 1;
                transform: translateX(-50%);
                animation: floatUp 1.5s ease-out forwards;
            }
            
            .floating-success {
                color: #28a745;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            }
            
            .floating-remove {
                color: #dc3545;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            }
            
            .floating-error {
                color: #fd7e14;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            }
            
            @keyframes floatUp {
                0% {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0px) scale(1);
                }
                20% {
                    opacity: 1;
                    transform: translateX(-50%) translateY(-10px) scale(1.2);
                }
                100% {
                    opacity: 0;
                    transform: translateX(-50%) translateY(-50px) scale(0.8);
                }
            }
            
            /* Heart pulse animation for added effect */
            .heart-pulse {
                animation: heartPulse 0.6s ease-out;
            }
            
            @keyframes heartPulse {
                0% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.3);
                }
                100% {
                    transform: scale(1);
                }
            }
        `;
    document.head.appendChild(styles);
  }

  floatingEl.style.left = centerX + "px";
  floatingEl.style.top = centerY + "px";

  document.body.appendChild(floatingEl);

  if (type === "success" || type === "remove") {
    element.classList.add("heart-pulse");
    setTimeout(() => {
      element.classList.remove("heart-pulse");
    }, 600);
  }

  setTimeout(() => {
    if (floatingEl.parentNode) {
      floatingEl.remove();
    }
  }, 1500);
}
