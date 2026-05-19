<?php
include 'includes/header.php';
require_once 'config/database.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$message = "";


if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'] === 'valider' ? 'valide' : 'refuse';
    
    $stmt = $pdo->prepare("UPDATE avis SET statut_validation = ? WHERE id = ?");
    if ($stmt->execute([$action, $id])) {
        $message = "<div class='alert alert-success rounded-0 small'>L'avis a été mis à jour avec le statut : $action.</div>";
    }
}


$stmt = $pdo->query("SELECT a.*, u.nom FROM avis a JOIN utilisateurs u ON a.user_id = u.id ORDER BY a.date_avis DESC");
$avis_liste = $stmt->fetchAll();
?>

<div class="container py-5">
    <h2 style="font-family: 'Playfair Display', serif;" class="mb-4">Modération des Avis Clients</h2>
    <?= $message ?>

    <?php if (empty($avis_liste)): ?>
        <p class="text-muted">Aucun avis déposé pour le moment.</p>
    <?php else: ?>
        <div class="table-responsive bg-white p-4 border shadow-sm">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Note</th>
                        <th>Commentaire</th>
                        <th>Statut actuel</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($avis_liste as $av): ?>
                    <tr>
                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($av['date_avis'])) ?></td>
                        <td><strong><?= htmlspecialchars($av['nom']) ?></strong></td>
                        <td class="text-warning"><?= str_repeat('⭐', $av['note']) ?></td>
                        <td class="small text-muted"><?= nl2br(htmlspecialchars($av['commentaire'])) ?></td>
                        <td>
                            <?php if($av['statut_validation'] === 'en_attente'): ?>
                                <span class="badge bg-warning text-dark rounded-0 text-uppercase">En attente</span>
                            <?php elseif($av['statut_validation'] === 'valide'): ?>
                                <span class="badge bg-success rounded-0 text-uppercase">Visible en ligne</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-0 text-uppercase">Masqué / Refusé</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end" style="min-width: 160px;">
                            <?php if($av['statut_validation'] === 'en_attente'): ?>
                                <a href="admin_avis.php?action=valider&id=<?= $av['id'] ?>" class="btn btn-sm btn-success rounded-0">Valider</a>
                                <a href="admin_avis.php?action=refuser&id=<?= $av['id'] ?>" class="btn btn-sm btn-outline-danger rounded-0">Refuser</a>
                            <?php else: ?>
                                <span class="text-muted small">Modéré</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>