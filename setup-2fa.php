<?php
ini_set('session.cache_limiter', 'nocache');
session_cache_limiter('nocache');
session_start();
date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/phpqrcode/qrlib.php';
require_once __DIR__ . '/TwoFactorAuthLight.php';

if (empty($_SESSION['user'])) {
    header('Location: login.php?error=expired');
    exit;
}

$tfa = new TwoFactorAuthLight();
if (empty($_SESSION['tfa_secret_temp'])) {
    $_SESSION['tfa_secret_temp'] = $tfa->createSecret();
}

$secret = $_SESSION['tfa_secret_temp'];
$email = $_SESSION['user']['email'];
$otpauthUrl = $tfa->getQRCodeUrl("MonApp:$email", $secret);

$qrFile = __DIR__ . '/qrcode.png';
QRcode::png($otpauthUrl, $qrFile);

echo "<h2>Configuration 2FA</h2>";
echo "<p>Scannez ce code avec LastPass Authenticator :</p>";
echo "<img src='qrcode.png?v=".time()."'><br>";
echo "<code>Code secret : $secret</code>";
echo "<form method='POST' action='verify-2fa.php'>";
echo "<input type='text' name='code' maxlength='6' required>";
echo "<button type='submit'>Valider</button></form>";
?>
