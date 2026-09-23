<?php
session_start();
require 'config.php';

// Vérifie que l'utilisateur est connecté, sinon redirige vers la page de connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$erreur = "";

// Récupérer la liste des salles
$salles = $pdo->query("SELECT * FROM salles")->fetchAll();

// Réserver une salle
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['salle_id'])) {
    $salle_id = $_POST['salle_id'];
    $objet = $_POST['objet'];
    $date_reunion = $_POST['date_reunion'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];

    // Vérifie s'il existe déjà une réunion dans la même salle avec un horaire qui se chevauche
    $stmt = $pdo->prepare("SELECT * FROM reunions 
        WHERE salle_id = ? AND date_reunion = ? 
        AND (heure_debut < ? AND heure_fin > ?)");
    $stmt->execute([$salle_id, $date_reunion, $heure_fin, $heure_debut]);

    if ($stmt->rowCount() > 0) {
        $erreur = "Cette salle est déjà réservée sur ce créneau horaire.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reunions (salle_id, user_id, objet, date_reunion, heure_debut, heure_fin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$salle_id, $user_id, $objet, $date_reunion, $heure_debut, $heure_fin]);
        header("Location: dashboard.php");
        exit();
    }
}

// Supprimer une réservation
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM reunions WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    header("Location: dashboard.php");
    exit();
}

// Récupère toutes les réunions avec le nom de la salle et de l'utilisateur (jointure entre 3 tables)
$stmt = $pdo->query("SELECT reunions.*, salles.nom AS salle_nom, users.nom AS user_nom 
    FROM reunions 
    JOIN salles ON reunions.salle_id = salles.id 
    JOIN users ON reunions.user_id = users.id 
    ORDER BY date_reunion ASC, heure_debut ASC");
$reunions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation de Salles</title>
    <link rel="stylesheet" href="code.css">
</head>
<body>
    <div class="container">
        <h2>Bonjour, <?= htmlspecialchars($_SESSION['user_nom']) ?> 👋</h2>
        <a href="logout.php" class="logout">Déconnexion</a>

        <h3>Réserver une salle</h3>

        <?php if ($erreur): ?>
            <p class="error"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Salle :</label>
            <select name="salle_id" required>
                <?php foreach ($salles as $salle): ?>
                    <option value="<?= $salle['id'] ?>"><?= $salle['nom'] ?> (<?= $salle['capacite'] ?> places)</option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="objet" placeholder="Objet de la réunion" required>
            <input type="date" name="date_reunion" required>

            <label>Heure début :</label>
            <input type="time" name="heure_debut" required>

            <label>Heure fin :</label>
            <input type="time" name="heure_fin" required>

            <button type="submit">Réserver</button>
        </form>

        <h3>Toutes les réunions programmées</h3>

        <?php if (count($reunions) === 0): ?>
            <p>Aucune réunion programmée.</p>
        <?php endif; ?>

        <?php foreach ($reunions as $r): ?>
            <div class="task">
                <strong><?= htmlspecialchars($r['objet']) ?></strong><br>
                📍 <?= $r['salle_nom'] ?> — 👤 <?= htmlspecialchars($r['user_nom']) ?><br>
                📅 <?= $r['date_reunion'] ?> de <?= substr($r['heure_debut'],0,5) ?> à <?= substr($r['heure_fin'],0,5) ?>

                <?php if ($r['user_id'] == $user_id): ?>
                    | <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Annuler cette réservation ?')">Annuler</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>