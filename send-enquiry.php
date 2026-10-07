<?php
// -----------------------------------------------
// DOS Tints - Contact Form Handler
// -----------------------------------------------

$recipient_email = "dos.tints1@gmail.com";

// Honeypot anti-spam: real users leave 'website' empty; bots fill every field
if (!empty($_POST['website'])) {
    // Pretend success so the bot moves on, but don't email
    header("Location: thank-you.html");
    exit;
}

// Collect and sanitise inputs
$full_name     = isset($_POST['name'])    ? htmlspecialchars(strip_tags(trim($_POST['name'])))    : '';
$phone_number  = isset($_POST['phone'])   ? htmlspecialchars(strip_tags(trim($_POST['phone'])))   : '';
$email_address = isset($_POST['email'])   ? htmlspecialchars(strip_tags(trim($_POST['email'])))   : '';
$vehicle       = isset($_POST['vehicle']) ? htmlspecialchars(strip_tags(trim($_POST['vehicle']))) : '';
$service       = isset($_POST['service']) ? htmlspecialchars(strip_tags(trim($_POST['service']))) : '';
$message_text  = isset($_POST['message']) ? htmlspecialchars(strip_tags(trim($_POST['message']))) : 'Not provided';

// Basic validation
$errors = [];
if (empty($full_name)) {
    $errors[] = "Full name is required.";
}
if (empty($phone_number)) {
    $errors[] = "Phone number is required.";
}
if (empty($email_address) || !filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
}
if (empty($vehicle)) {
    $errors[] = "Vehicle details are required.";
}
if (empty($service)) {
    $errors[] = "Please select a service.";
}

// If validation fails, go back with errors
if (!empty($errors)) {
    $error_string = urlencode(implode(" | ", $errors));
    $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.html';
    header("Location: " . $back . "?status=error&message=" . $error_string);
    exit;
}

// Build the email
$subject  = "New Enquiry from $full_name – DOS Tints Website";
$message  = "You have received a new enquiry from your website.\n\n";
$message .= "----------------------------------------\n";
$message .= "CONTACT DETAILS\n";
$message .= "----------------------------------------\n";
$message .= "Name:     $full_name\n";
$message .= "Phone:    $phone_number\n";
$message .= "Email:    $email_address\n\n";
$message .= "----------------------------------------\n";
$message .= "ENQUIRY DETAILS\n";
$message .= "----------------------------------------\n";
$message .= "Vehicle:  $vehicle\n";
$message .= "Service:  $service\n\n";
$message .= "Message:\n$message_text\n\n";
$message .= "----------------------------------------\n";
$message .= "Sent: " . date('D, j M Y · H:i') . "\n";
$message .= "This email was sent from the contact form on the DOS Tints website.\n";

$headers  = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Reply-To: $email_address\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send the email
$mail_sent = mail($recipient_email, $subject, $message, $headers);

// Redirect to dedicated thank-you page (passing first name so it can be personalised)
if ($mail_sent) {
    $qs = http_build_query(['name' => $full_name]);
    header("Location: thank-you.html?" . $qs);
} else {
    $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.html';
    header("Location: " . $back . "?status=error&message=" . urlencode("Sorry, there was a problem sending your message. Please try calling us directly on 07599 000 899."));
}
exit;
?>
