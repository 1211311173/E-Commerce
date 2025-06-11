document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // notification toast variables
  const notificationToastElement = document.querySelector('[data-toast]');
  const toastCloseBtnElement = document.querySelector('[data-toast-close]');

  // notification toast eventListener
  if (toastCloseBtnElement && notificationToastElement) {
    toastCloseBtnElement.addEventListener('click', function () {
      notificationToastElement.classList.add('closed');
    });
  }
});


