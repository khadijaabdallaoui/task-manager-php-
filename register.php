<?php
require 'config.php';
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $erreur = "Cet email est déjà utilisé.";
    } else {
        // Hasher le mot de passe et l'enregistrer
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $hash]);

        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Inscription</h2>

        <?php if ($erreur): ?>
            <p class="error"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nom :</label><br>
            <input type="text" name="nom" required><br>

            <label>Email :</label><br>
            <input type="email" name="email" required><br>

            <label>Mot de passe :</label><br>
            <input type="password" name="password" required><br>

            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>