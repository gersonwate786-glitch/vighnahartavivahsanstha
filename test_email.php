<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'databaseConn.php';
$DatabaseCo = new DatabaseConn();

echo "<h2>Email Configuration Test</h2>";

// Get email settings from database
$SQL_STATEMENT_EMAIL = $DatabaseCo->dbLink->query("SELECT * FROM email_setting WHERE email_config_id='1'");
$row = mysqli_fetch_object($SQL_STATEMENT_EMAIL);

if(!$row) {
    echo "<p style='color:red;'><b>ERROR:</b> Email settings not found in database!</p>";
    die;
}

echo "<p><b>Email Settings from Database:</b></p>";
echo "Host: " . $row->host . "<br>";
echo "Email: " . $row->email . "<br>";
echo "Port: " . $row->port . "<br>";
echo "Email Name: " . $row->email_name . "<br>";
echo "Encryption: " . $row->enc_type . "<br><br>";

// Test PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(!file_exists('phpmailer/vendor/autoload.php')) {
    echo "<p style='color:red;'><b>ERROR:</b> PHPMailer not found! Check phpmailer folder.</p>";
    die;
}

require 'phpmailer/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = $row->host;
    $mail->SMTPAuth = true;
    $mail->Username = $row->email;
    $mail->Password = $row->email_password;
    $mail->Port = $row->port;
    $mail->SMTPSecure = $row->enc_type;
    
    $mail->setFrom($row->email, $row->email_name);
    $mail->addAddress('test@gmail.com', 'Test User');
    $mail->isHTML(true);
    $mail->Subject = 'Email Test from Your Matrimony Site';
    $mail->Body = 'This is a test email to verify your email configuration is working.';
    
    if($mail->send()) {
        echo "<p style='color:green;'><b>SUCCESS:</b> Email sent successfully!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'><b>ERROR:</b> {$mail->ErrorInfo}</p>";
    echo "<p><b>Debug Output:</b></p>";
    echo "<pre>" . $mail->SMTPDebug . "</pre>";
}
?>
