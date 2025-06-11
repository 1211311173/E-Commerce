// Modern Banner Slider JavaScript

class ModernBannerSlider {
  constructor(container) {
    this.container = container;
    this.wrapper = container.querySelector('.modern-slider-wrapper');
    this.slides = container.querySelectorAll('.modern-slide');
    this.prevBtn = container.querySelector('.modern-nav-btn.prev');
    this.nextBtn = container.querySelector('.modern-nav-btn.next');
    this.dotsContainer = container.querySelector('.modern-dots-container');

    this.currentSlide = 0;
    this.totalSlides = this.slides.length;
    this.autoPlayInterval = null;
    this.autoPlayDuration = 5000;
    this.isTransitioning = false;

    this.init();
  }

  init() {
    if (this.totalSlides === 0) return;

    this.createDots();
    this.bindEvents();
    this.startAutoPlay();
    this.updateSlider();
  }
  createDots() {
    if (!this.dotsContainer) return;

    this.dotsContainer.innerHTML = '';

    for (let i = 0; i < this.totalSlides; i++) {
      const dot = document.createElement('div');
      dot.className = 'modern-dot';
      if (i === 0) dot.classList.add('active');

      dot.addEventListener('click', () => {
        if (!this.isTransitioning) {
          this.goToSlide(i);
        }
      });

      this.dotsContainer.appendChild(dot);
    }
  }

  bindEvents() {
    // Navigation buttons
    if (this.prevBtn) {
      this.prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!this.isTransitioning) {
          this.prevSlide();
        }
      });
    }

    if (this.nextBtn) {
      this.nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!this.isTransitioning) {
          this.nextSlide();
        }
      });
    }

    // Keyboard navigation - only when slider is focused
    this.container.addEventListener('keydown', (e) => {
      if (!this.isTransitioning) {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          this.prevSlide();
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          this.nextSlide();
        }
      }
    });

    // Make container focusable for keyboard events
    this.container.setAttribute('tabindex', '0');
    // Pause autoplay on hover - use more specific events and avoid conflicts
    let hoverTimeout;
    this.container.addEventListener('mouseenter', (e) => {
      if (e.target.closest('.modern-banner-container') === this.container) {
        clearTimeout(hoverTimeout);
        this.pauseAutoPlay();
      }
    });

    this.container.addEventListener('mouseleave', (e) => {
      if (!e.relatedTarget || !this.container.contains(e.relatedTarget)) {
        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
          this.startAutoPlay();
        }, 100);
      }
    });

    // Touch/swipe support
    this.addTouchSupport();
  }

  addTouchSupport() {
    let startX = 0;
    let isDragging = false;

    this.container.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      isDragging = true;
      this.pauseAutoPlay();
    });

    this.container.addEventListener('touchmove', (e) => {
      if (!isDragging) return;
      e.preventDefault();
    }, { passive: true });

    this.container.addEventListener('touchend', (e) => {
      if (!isDragging) return;

      const endX = e.changedTouches[0].clientX;
      const diffX = startX - endX;

      if (Math.abs(diffX) > 50) { // Minimum swipe distance
        if (diffX > 0) {
          this.nextSlide();
        } else {
          this.prevSlide();
        }
      }

      isDragging = false;
      this.startAutoPlay();
    });
  }

  goToSlide(index) {
    if (index === this.currentSlide || this.isTransitioning) return;

    this.isTransitioning = true;
    this.currentSlide = index;
    this.updateSlider();

    // Reset transition flag after animation
    setTimeout(() => {
      this.isTransitioning = false;
    }, 600);

    // Restart autoplay after manual navigation
    this.startAutoPlay();
  }

  nextSlide() {
    const nextIndex = (this.currentSlide + 1) % this.totalSlides;
    this.goToSlide(nextIndex);
  }

  prevSlide() {
    const prevIndex = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
    this.goToSlide(prevIndex);
  }

  updateSlider() {
    if (!this.wrapper) return;

    // Update slider position
    const translateX = -this.currentSlide * 100;
    this.wrapper.style.transform = `translateX(${translateX}%)`;

    // Update dots
    const dots = this.dotsContainer?.querySelectorAll('.modern-dot');
    if (dots) {
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === this.currentSlide);
      });
    }

    // Animate content
    this.animateSlideContent();
  }

  animateSlideContent() {
    const currentSlideElement = this.slides[this.currentSlide];
    const content = currentSlideElement?.querySelector('.modern-slide-content');

    if (content) {
      content.style.animation = 'none';
      setTimeout(() => {
        content.style.animation = 'slideInLeft 0.8s ease-out';
      }, 50);
    }
  }

  startAutoPlay() {
    this.pauseAutoPlay(); // Clear any existing interval

    this.autoPlayInterval = setInterval(() => {
      this.nextSlide();
    }, this.autoPlayDuration);
  }

  pauseAutoPlay() {
    if (this.autoPlayInterval) {
      clearInterval(this.autoPlayInterval);
      this.autoPlayInterval = null;
    }
  }

  // Public methods for external control
  pause() {
    this.pauseAutoPlay();
  }

  play() {
    this.startAutoPlay();
  }

  destroy() {
    this.pauseAutoPlay();
    // Remove event listeners if needed
  }
}

// Auto-initialize when DOM is loaded - use a more specific timing
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeSliders);
} else {
  // DOM is already loaded
  setTimeout(initializeSliders, 100);
}

function initializeSliders() {
  const bannerContainers = document.querySelectorAll('.modern-banner-container');

  bannerContainers.forEach(container => {
    // Avoid double initialization
    if (!container.dataset.sliderInitialized) {
      container.dataset.sliderInitialized = 'true';
      new ModernBannerSlider(container);
    }
  });
}

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ModernBannerSlider;
}
