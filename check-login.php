<?php
session_start();

// Protection HTTPS (sauf en localhost)
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
if (!$isLocalhost && empty($_SERVER['HTTPS'])) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Méthode POST requise");

// Récupération et nettoyage
$email = trim((string)$_POST['email']);
$password = trim((string)$_POST['password']);

if (empty($email) || empty($password)) die("Champs vides");

// Connexion à la base SQLite
$db = new SQLite3('tp-2fa.db', SQLITE3_OPEN_READWRITE);

// Vérification de l'utilisateur
$stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
$stmt->bindValue(':email', $email, SQLITE3_TEXT);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

// Vérification du hash
if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); // Sécurité
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'email' => (string)$user['email'],
        'tfa_secret' => (string)($user['secret_2fa'] ?? '')
    ];
    unset($_SESSION['tfa_secret_temp']); // Nettoyage

    // Redirection selon le statut 2FA
    header('Location: ' . (empty($user['secret_2fa']) ? 'setup-2fa.php' : 'check-2fa.php'));
} else {
    header('Location: login.php?error=badlogin');
}
