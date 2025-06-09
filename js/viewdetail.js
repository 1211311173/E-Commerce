let btn_product_decrement = document.querySelector(".btn_product_decrement");
let btn_product_increment = document.querySelector(".btn_product_increment");
let change_qty = document.getElementById("p_qty");

btn_product_decrement.addEventListener("click", function () {
  if (change_qty.value == 1) {
    change_qty.value = 1;
  } else {
    change_qty.value = change_qty.value - 1;
  }
});
btn_product_increment.addEventListener("click", function () {
  change_qty.value = parseInt(change_qty.value) + 1;
});

// Function to redirect to login page with return URL
function redirectToLogin() {
  // Get current URL components
  const pathname = window.location.pathname;
  const search = window.location.search;
  
  // Get just the PHP filename
  const filename = pathname.substring(pathname.lastIndexOf('/') + 1);
  
  // Construct the full relative path
  const redirectPath = filename + search;
  
  // Build login URL
  const loginUrl = "login.php?redirect=" + encodeURIComponent(redirectPath);
  
  // Navigate to login
  window.location.href = loginUrl;
}
