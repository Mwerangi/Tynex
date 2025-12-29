<?php
/**
 * Simple contact form handler
 * Replace your-email@example.com with your actual email address
 */

// Configuration
$receiving_email = 'your-email@example.com'; // CHANGE THIS!

// Security headers
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Sanitize and validate inputs
$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone = filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING);
$subject = filter_var($_POST['subject'] ?? '', FILTER_SANITIZE_STRING);
$message = filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING);

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['error' => implode(', ', $errors)]);
    exit;
}

// Prepare email
$email_subject = "Tynex Contact Form: " . $subject;
$email_body = "You have received a new message from Tynex website contact form.\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
if (!empty($phone)) {
    $email_body .= "Phone: $phone\n";
}
$email_body .= "\nMessage:\n$message\n";

$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
if (mail($receiving_email, $email_subject, $email_body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent. Thank you!']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send message. Please try again later.']);
}
?>
