let fields = document.querySelectorAll('.child-fields input, .Address-field input'); // Adjusted selector
let error = document.querySelector('.error-ms');

// Replace 'YOUR_STRIPE_PUBLISHABLE_KEY' with your actual Stripe publishable key
//pk_test_51ROXnPQIbExyQfzAGo5fFRQ3Pq4QwZNQjL0jHVIkZOOpswgP15H67z5MkcNcpHOxKH2mB37Qo4WqMS7P9CO7Rh1C00iPfSiVRK
const stripe = Stripe('YOUR_STRIPE_PUBLISHABLE_KEY'); 

async function checkFields() {
    let allFieldsFilled = true;
    let customerDetails = {
        name: `${document.querySelector('.child-fields1 input').value} ${document.querySelector('.child-fields3 input').value}`,
        address: {
            line1: document.querySelector('.child-fields4 input').value,
            line2: document.querySelector('.child-fields5 input').value, // Assuming street/road can be line2 or part of line1
            city: document.querySelector('.child-fields6 input').value,
            postal_code: document.querySelector('.child-fields7 input').value,
            country: document.querySelector('.Address-field input').value, // Assuming this is a country code like 'US' or 'GB'
        },
        phone: document.querySelector('.child-fields8 input').value,
        email: document.querySelector('.child-fields9 input').value,
    };

    fields.forEach(field => {
        if (field.value.trim() === '') {
            allFieldsFilled = false;
        }
    });

    if (!allFieldsFilled) {
        error.innerHTML = "Please fill all fields.";
        return;
    } else {
        error.innerHTML = ""; // Clear previous errors
    }

    // Create an object with data to send to the server
    const checkoutData = {
        customer_details: customerDetails,
        // items: [] // This will be populated from the session on the server-side
    };

    try {
        // Make a POST request to your backend to create a checkout session
        const response = await fetch('create-checkout-session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(checkoutData),
        });

        const session = await response.json();

        if (session.id) {
            // Redirect to Stripe Checkout
            const result = await stripe.redirectToCheckout({ sessionId: session.id });
            if (result.error) {
                // If `redirectToCheckout` fails due to a browser or network
                // error, display the localized error message to your customer.
                error.innerHTML = result.error.message;
            }
        } else if (session.error) {
            error.innerHTML = "Could not initiate payment: " + session.error;
        } 
         else {
            error.innerHTML = "Could not initiate payment. Please try again.";
        }
    } catch (e) {
        error.innerHTML = "An error occurred: " + e.message;
        console.error("Checkout error:", e);
    }
}