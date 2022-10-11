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

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $success = $db->createUser($name, $email, $password);
        if ($success) {
            redirect('user.php');
        }
    } catch (AuthenticationException $e) {
        // pass
    } catch (DuplicateEmailException $e) {
        // pass
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Interpromos - Inscription</title>
</head>

<body>
    <h1>Inscription</h1>
    <form action="register.php" method="POST">
        <label for="name">Nom</label>
        <input type="text" name="name" id="name" required />
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required />
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required />
        <input type="submit" name="register" value="S'inscrire" />
    </form>
</body>

</html>