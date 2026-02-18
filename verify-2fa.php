<?php
session_start();
require_once __DIR__ . '/TwoFactorAuthLight.php';

if (empty($_SESSION['tfa_secret_temp']) || empty($_POST['code'])) {
    header('Location: setup-2fa.php');
    exit;
}

$tfa = new TwoFactorAuthLight();
$secret = $_SESSION['tfa_secret_temp'];
$userCode = $_POST['code'];

if ($tfa->verifyCode($secret, $userCode)) {
    // Connexion à la base pour enregistrer le secret
    $db = new SQLite3('tp-2fa.db', SQLITE3_OPEN_READWRITE);
    $stmt = $db->prepare("UPDATE users SET secret_2fa = :secret, twofa_enabled = 1 WHERE id = :id");
    $stmt->bindValue(':secret', $secret, SQLITE3_TEXT);
    $stmt->bindValue(':id', $_SESSION['user']['id'], SQLITE3_INTEGER);
    $stmt->execute();

    $_SESSION['user']['tfa_secret'] = $secret;
    unset($_SESSION['tfa_secret_temp']);
    echo "<h1>2FA activée avec succès !</h1><a href='login.php'>Retour</a>";
} else {
    echo "<h1>Code invalide !</h1><a href='setup-2fa.php'>Réessayer</a>";
}
?>
