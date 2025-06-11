document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // accordion variables
  const accordionBtnElements = document.querySelectorAll('[data-accordion-btn]');
  const accordionElements = document.querySelectorAll('[data-accordion]');

  if (accordionBtnElements.length > 0 && accordionElements.length > 0) {
    for (let i = 0; i < accordionBtnElements.length; i++) {
      accordionBtnElements[i].addEventListener('click', function () {
        const clickedBtn = this.nextElementSibling.classList.contains('active');

        for (let i = 0; i < accordionElements.length; i++) {
          if (clickedBtn) break;

          if (accordionElements[i].classList.contains('active')) {
            accordionElements[i].classList.remove('active');
            accordionBtnElements[i].classList.remove('active');
          }
        }

        this.nextElementSibling.classList.toggle('active');
        this.classList.toggle('active');
      });
    }
  }
});