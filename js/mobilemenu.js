document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // mobile menu variables
  const mobileMenuOpenBtnElements = document.querySelectorAll('[data-mobile-menu-open-btn]');
  const mobileMenuElements = document.querySelectorAll('[data-mobile-menu]');
  const mobileMenuCloseBtnElements = document.querySelectorAll('[data-mobile-menu-close-btn]');
  const overlayElement = document.querySelector('[data-overlay]');

  if (mobileMenuOpenBtnElements.length > 0 &&
    mobileMenuElements.length > 0 &&
    mobileMenuCloseBtnElements.length > 0 &&
    overlayElement) {

    for (let i = 0; i < mobileMenuOpenBtnElements.length; i++) {
      // mobile menu function
      const mobileMenuCloseFunc = function () {
        mobileMenuElements[i].classList.remove('active');
        overlayElement.classList.remove('active');
      }

      mobileMenuOpenBtnElements[i].addEventListener('click', function () {
        mobileMenuElements[i].classList.add('active');
        overlayElement.classList.add('active');
      });

      mobileMenuCloseBtnElements[i].addEventListener('click', mobileMenuCloseFunc);
      overlayElement.addEventListener('click', mobileMenuCloseFunc);
    }
  }
});

