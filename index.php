<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delite Apparels Pvt Ltd</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header>
        <img src="header-image.jpg" alt="Delite Apparels" class="header-image">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="account.php">My Account</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Welcome to Delite Apparels</h2>
        <p>Buy high-quality apparels with flexible payment options.</p>
        <div id="shop-container">
            <h3>Our Products</h3>
            <div id="product-list">
                <!-- Products loaded dynamically with JS -->
            </div>
        </div>
    </main>

    <footer>
        <p>Contact Us:</p>
        <p>Email: contact@deliteapparels.com</p>
        <p>Phone: +91 98765 43210</p>
        <p>Address: 123 Fashion Street, Mumbai, India</p>
        <p>Follow us on: 
            <a href="#">Facebook</a> | 
            <a href="#">Instagram</a> | 
            <a href="#">Twitter</a>
        </p>
        <p>&copy; 2025 Delite Apparels Pvt Ltd. All rights reserved.</p>
    </footer>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('get_products.php')
        .then(response => response.json())
        .then(data => {
            let productList = document.getElementById("product-list");
            data.forEach(product => {
                let productDiv = document.createElement("div");
                productDiv.classList.add("product");
                productDiv.innerHTML = `
                    <img src="${product.image}" alt="${product.name}" class="product-image">
                    <h4>${product.name}</h4>
                    <p>Price: $${product.price}</p>
                    <select id="payment-option-${product.id}">
                        <option value="30">Pay in 30 days</option>
                        <option value="60">Pay in 60 days</option>
                    </select>
                    <button onclick="buyNow(${product.id})">Buy Now</button>
                `;
                productList.appendChild(productDiv);
            });
        });
    });
    
    function buyNow(productId) {
        let paymentOption = document.getElementById(`payment-option-${productId}`).value;
        if (confirm("Are you sure you want to buy this product?")) {
            fetch('process_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, payment_term: paymentOption })
            })
            .then(response => response.json())
            .then(data => {
                alert("Order placed successfully! Order Number: " + data.order_id);
                sendNotification(data);
            });
        }
    }
    
    function sendNotification(order) {
        fetch('send_notification.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(order)
        });
    }
    </script>
</body>
</html>

<?php
// Include user authentication
require 'auth.php';

// Forgot password functionality
require 'forgot_password.php';

// Email and WhatsApp notification system
require 'send_email.php';
require 'send_whatsapp.php';

// Admin panel to modify product prices
require 'admin_panel.php';

// send_email.php - Ensure emails go to deliteapparels@gmail.com
require 'PHPMailer/PHPMailerAutoload.php';
function sendOrderEmail($orderDetails) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com'; // Replace with a valid Gmail
    $mail->Password = 'your-email-password'; // Use App Password if 2FA is enabled
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your-email@gmail.com', 'Delite Apparels');
    $mail->addAddress('deliteapparels@gmail.com');
    $mail->Subject = 'New Order Confirmation';
    $mail->Body = "Order Number: " . $orderDetails['order_id'] . "\nName: " . $orderDetails['name'] . "\nPhone: " . $orderDetails['phone'] . "\nEmail: " . $orderDetails['email'] . "\nPrice: " . $orderDetails['price'];

    if(!$mail->send()) {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 'Message sent successfully!';
    }
}
?>
