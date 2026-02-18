<?php
$password = 'Vivelephp!2026';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Mot de passe clair : " . $password . PHP_EOL;
echo "Mot de passe hashé  : " . $hash . PHP_EOL;
