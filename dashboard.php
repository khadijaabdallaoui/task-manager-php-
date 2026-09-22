<?php
session_start();
require 'config.php';

// Protection : si pas connecté, redirection vers login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ajouter une tâche
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['titre'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_limite = $_POST['date_limite'];

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, titre, description, date_limite) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $titre, $description, $date_limite]);
    header("Location: dashboard.php");
    exit();
}

// Supprimer une tâche
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    header("Location: dashboard.php");
    exit();
}

// Changer le statut
if (isset($_GET['statut']) && isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE tasks SET statut = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['statut'], $_GET['id'], $user_id]);
    header("Location: dashboard.php");
    exit();
}

// Récupérer les tâches
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY date_limite ASC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Tâches</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Bonjour, <?= htmlspecialchars($_SESSION['user_nom']) ?> 👋</h2>
        <a href="logout.php" class="logout">Déconnexion</a>

        <h3>Ajouter une tâche</h3>
        <form method="POST">
            <input type="text" name="titre" placeholder="Titre" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="date" name="date_limite">
            <button type="submit">Ajouter</button>
        </form>

        <h3>Mes tâches</h3>
        <?php if (count($tasks) === 0): ?>
            <p>Aucune tâche pour le moment.</p>
        <?php endif; ?>

        <?php foreach ($tasks as $task): ?>
            <div class="task">
                <strong><?= htmlspecialchars($task['titre']) ?></strong>
                (<?= $task['statut'] ?>)<br>
                <?= htmlspecialchars($task['description']) ?><br>
                <small>Date limite : <?= $task['date_limite'] ?></small><br>

                <a href="?statut=a_faire&id=<?= $task['id'] ?>">À faire</a> |
                <a href="?statut=en_cours&id=<?= $task['id'] ?>">En cours</a> |
                <a href="?statut=termine&id=<?= $task['id'] ?>">Terminé</a> |
                <a href="?delete=<?= $task['id'] ?>" onclick="return confirm('Supprimer cette tâche ?')">Supprimer</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
</html>