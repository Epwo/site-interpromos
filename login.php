<?php

/**
 * PHP version 8.1.11
 * 
 * @author Youn Mélois <youn@melois.dev>
 */

require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/redirect.php';
require_once LIBRARY_PATH . '/exceptions.php';

$db = new Database();

// redirect to the user page if the user is already logged in
if (isset($_COOKIE[ACCESS_TOKEN_NAME])) {
    $access_token = $_COOKIE[ACCESS_TOKEN_NAME];
    $success = $db->verifyUserAccessToken($access_token);
    if ($success) {
        redirect('user.php');
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $success = $db->connectUser($email, $password);
        if ($success) {
            redirect('user.php');
        }
    } catch (AuthenticationException $e) {
        // pass
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Interpromos - Connexion</title>
</head>

<body>
    <h1>Connexion</h1>
    <form action="login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required />
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required />
        <input type="submit" name="login" value="Se connecter" />
    </form>
    <a href="register.php">Créer un compte</a>
</body>

</html>