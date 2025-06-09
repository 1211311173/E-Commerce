// Deal of the Day Slider JavaScript
// Based on Modern Banner Slider logic

class DealSlider {
  constructor(container) {
    this.container = container;
    this.wrapper = container.querySelector('.deal-slider-wrapper');
    this.slides = container.querySelectorAll('.deal-slide');
    this.prevBtn = container.querySelector('.deal-nav-btn.prev');
    this.nextBtn = container.querySelector('.deal-nav-btn.next');
    this.dotsContainer = container.querySelector('.deal-dots-container');
    
    this.currentSlide = 0;
    this.totalSlides = this.slides.length;
    this.autoPlayInterval = null;
    this.autoPlayDuration = 8000; // Longer duration for deals
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
      dot.className = 'deal-dot';
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
    
    // Keyboard navigation
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

    // Pause autoplay on hover
    let hoverTimeout;
    this.container.addEventListener('mouseenter', (e) => {
      if (e.target.closest('.deal-slider-container') === this.container) {
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
    });
    
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
    const dots = this.dotsContainer?.querySelectorAll('.deal-dot');
    if (dots) {
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === this.currentSlide);
      });
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

// Auto-initialize when DOM is loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeDealSliders);
} else {
  // DOM is already loaded
  setTimeout(initializeDealSliders, 100);
}

function initializeDealSliders() {
  const dealContainers = document.querySelectorAll('.deal-slider-container');
  
  dealContainers.forEach(container => {
    // Avoid double initialization
    if (!container.dataset.dealSliderInitialized) {
      container.dataset.dealSliderInitialized = 'true';
      new DealSlider(container);
    }
  });
}

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
  module.exports = DealSlider;
}
