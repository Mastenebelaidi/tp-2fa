<?php
session_start();
// Si l'utilisateur est déjà connecté, on le redirige
if (isset($_SESSION['user'])) {
    if (!empty($_SESSION['user']['tfa_secret'])) {
        header('Location: check-2fa.php');
    } else {
        header('Location: setup-2fa.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Connexion</h2>
    <?php if(isset($_GET['error'])) echo "<p style='color:red'>Erreur d'authentification</p>"; ?>
    <form action="check-login.php" method="POST">
        <input type="email" name="email" value="user@test.fr" required><br><br>
        <input type="password" name="password" value="Vivelephp!2026" required><br><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
