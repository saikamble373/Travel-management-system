<?php
function sendNotification($email, $subject, $message) {
    // In a production app, use PHPMailer or an SMTP service like SendGrid, or SMS API like Twilio.
    // For now, we mock the email by appending it to a log file.
    
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "--- EMAIL NOTIFICATION ---\n";
    $logEntry .= "Date: $timestamp\n";
    $logEntry .= "To: $email\n";
    $logEntry .= "Subject: $subject\n";
    $logEntry .= "Message: $message\n";
    $logEntry .= "--------------------------\n\n";
    
    // Log file path (relative to includes folder)
    $logFile = __DIR__ . "/../sent_emails.log";
    
    // Write contents to log
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Uncomment the next line to actually attempt a server mail() call.
    // @mail($email, $subject, $message, "From: noreply@travelmate.com");
}
?>
