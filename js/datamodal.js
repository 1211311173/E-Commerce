document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // modal variables
    const modalElement = document.querySelector('[data-modal]');
    const modalCloseBtnElement = document.querySelector('[data-modal-close]');
    const modalCloseOverlayElement = document.querySelector('[data-modal-overlay]');

    // modal function
    const modalCloseFunc = function () {
        if (modalElement) modalElement.classList.add('closed');
    }

    // modal eventListener
    if (modalCloseBtnElement) {
        modalCloseBtnElement.addEventListener('click', modalCloseFunc);
    }
    if (modalCloseOverlayElement) {
        modalCloseOverlayElement.addEventListener('click', modalCloseFunc);
    }
});



