<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['update_statut'])) {
        $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?")
            ->execute([$_POST['nouveau_statut'], $_POST['commande_id']]);
    }
    
    if (isset($_POST['toggle_statut'])) {
        $statut = ($_POST['current_statut'] === 'actif') ? 'inactif' : 'actif';
        $pdo->prepare("UPDATE utilisateurs SET statut = ? WHERE id = ?")
            ->execute([$statut, $_POST['user_id']]);
    }
}

$commandes = $pdo->query("SELECT c.*, u.nom FROM commandes c JOIN utilisateurs u ON c.utilisateur_id = u.id")->fetchAll();
$utilisateurs = $pdo->query("SELECT * FROM utilisateurs WHERE role != 'admin'")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <h2>Tableau de bord Administrateur</h2>

    <h4 class="mt-5">Commandes en cours</h4>
    <table class="table border">
        <?php foreach ($commandes as $c): ?>
        <tr>
            <td>Commande #<?= $c['id'] ?> - <?= $c['nom'] ?></td>
            <td>
                <form method="POST" class="d-flex">
                    <input type="hidden" name="commande_id" value="<?= $c['id'] ?>">
                    <select name="nouveau_statut" class="form-select w-auto">
                        <option value="En attente" <?= $c['statut'] == 'En attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="Validé" <?= $c['statut'] == 'Validé' ? 'selected' : '' ?>>Validé</option>
                    </select>
                    <button type="submit" name="update_statut" class="btn btn-sm btn-dark ms-2">OK</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h4 class="mt-5">Gestion des Comptes Employés</h4>
    <table class="table border">
        <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= $u['nom'] ?> (<?= $u['statut'] ?>)</td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="current_statut" value="<?= $u['statut'] ?>">
                    <button type="submit" name="toggle_statut" class="btn btn-sm <?= ($u['statut'] == 'actif') ? 'btn-danger' : 'btn-success' ?>">
                        <?= ($u['statut'] == 'actif') ? 'Bloquer' : 'Activer' ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>