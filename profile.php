<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$erreur = "";

// Récupérer les infos actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Mettre à jour les infos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    // Vérifier que l'email n'est pas déjà utilisé par un autre compte
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);

    if ($stmt->rowCount() > 0) {
        $erreur = "Cet email est déjà utilisé par un autre compte.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $user_id]);

        // Mettre à jour la session avec le nouveau nom
        $_SESSION['user_nom'] = $nom;
        $message = "Profil mis à jour avec succès.";

        // Recharger les infos à jour
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="code.css">
</head>
<body>
    <div class="container">
        <h2>Mon Profil</h2>
        <a href="dashboard.php">← Retour au tableau de bord</a>

        <?php if ($message): ?>
            <p style="color:green;"><?= $message ?></p>
        <?php endif; ?>

        <?php if ($erreur): ?>
            <p class="error"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nom :</label><br>
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br>

            <label>Email :</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>