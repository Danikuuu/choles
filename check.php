<?php
$stored_hash = '$2y$10$uDpT4Oxac4CCZ9K3FjZq2e/nxOCplMYE9W8rLBv2cgMgf6tp0Uh6S'; // Use the stored hash from your DB
$entered_password = 'admin';

if (password_verify($entered_password, $stored_hash)) {
    echo "Password matches!";
} else {
    echo "Password does NOT match!";
}
?>
