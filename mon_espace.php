<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$utilisateur_id = $_SESSION['user_id'];
$message_action = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_profil'])) {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));

    if (!empty($nom) && !empty($prenom)) {
        $updateUser = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ? WHERE id = ?");
        if ($updateUser->execute([$nom, $prenom, $utilisateur_id])) {
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $message_action = "<div class='alert alert-success rounded-0 small'>💾 Vos informations personnelles (Nom et Prénom) ont été mises à jour avec succès !</div>";
        } else {
            $message_action = "<div class='alert alert-danger rounded-0 small'>❌ Erreur lors de la mise à jour du profil.</div>";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler_commande'])) {
    $commande_id = (int)$_POST['commande_id'];

    $colonne_id = "id";
    try {
        $check = $pdo->prepare("SELECT statut FROM commandes WHERE id = ? AND utilisateur_id = ?");
        $check->execute([$commande_id, $utilisateur_id]);
    } catch (PDOException $e) {
        $colonne_id = "commande_id";
    }

    $checkCmd = $pdo->prepare("SELECT statut FROM commandes WHERE $colonne_id = ? AND utilisateur_id = ?");
    $checkCmd->execute([$commande_id, $utilisateur_id]);
    $cmd = $checkCmd->fetch();

    if ($cmd && $cmd['statut'] === 'En attente') {
        $annuler = $pdo->prepare("UPDATE commandes SET statut = 'Annulé' WHERE $colonne_id = ?");
        if ($annuler->execute([$commande_id])) {
            $message_action = "<div class='alert alert-warning rounded-0 small'>🛑 La commande #$commande_id a été annulée avec succès.</div>";
        }
    } else {
        $message_action = "<div class='alert alert-danger rounded-0 small'>❌ Impossible d'annuler cette commande.</div>";
    }
}

$stmtUser = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$utilisateur_id]);
$user = $stmtUser->fetch();

$stmt = $pdo->prepare("SELECT * FROM commandes WHERE utilisateur_id = ? ORDER BY date_commande DESC");
$stmt->execute([$utilisateur_id]);
$mes_commandes = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5" style="min-height: 75vh;">
    <?= $message_action ?>

    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3">
        <div>
            <h6 class="text-uppercase small text-muted mb-1" style="letter-spacing: 1px;">Espace Personnel</h6>
            <h2 style="font-family: 'Playfair Display', serif; color: #1a2536;">Bonjour, <?= htmlspecialchars($user['prenom'] ?? 'Client') ?> </h2>
        </div>
        <div>
            <a href="menus.php" class="btn btn-dark rounded-0 text-uppercase small fw-bold" style="background: #1a2536; font-size: 0.8rem;">
                + Nouvelle réservation
            </a>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-md-4">
            <div class="bg-light p-4 border shadow-sm">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif; color: #1a2536;">Mes Informations</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase text-muted fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control rounded-0" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase text-muted fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control rounded-0" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase text-muted fw-bold">Email (Non modifiable)</label>
                        <input type="email" class="form-control rounded-0 bg-white text-muted" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                    </div>
                    <button type="submit" name="modifier_profil" class="btn btn-outline-dark rounded-0 btn-sm text-uppercase fw-bold w-100 mt-4">
                        💾 Sauvegarder
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <h4 class="mb-4" style="font-family: 'Playfair Display', serif; color: #1a2536;">Historique de vos prestations</h4>

            <?php if (empty($mes_commandes)): ?>
                <div class="bg-white p-5 text-center border">
                    <span style="font-size: 3rem;">📋</span>
                    <p class="text-muted mt-3">Aucune réservation trouvée pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive bg-white border shadow-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark" style="background: #1a2536;">
                            <tr class="text-uppercase small" style="font-size: 0.75rem; letter-spacing: 1px;">
                                <th class="p-3">N°</th>
                                <th class="p-3">Date</th>
                                <th class="p-3">Total</th>
                                <th class="p-3">Statut</th>
                                <th class="p-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mes_commandes as $commande): ?>
                                <?php $c_id = $commande['id'] ?? $commande['commande_id']; ?>
                                <tr>
                                    <td class="p-3 fw-bold">#<?= $c_id ?></td>
                                    <td class="p-3 small text-muted"><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></td>
                                    <td class="p-3 fw-bold"><?= number_format($commande['total'], 2, ',', ' ') ?> €</td>
                                    <td class="p-3">
                                        <?php 
                                        $status = $commande['statut'];
                                        $badge_class = "bg-warning text-dark"; 
                                        if (in_array($status, ['Validé', 'Confirmé', 'Livré', 'Terminé'])) { $badge_class = "bg-success text-white"; }
                                        elseif (in_array($status, ['En cours', 'Préparation'])) { $badge_class = "bg-info text-white"; }
                                        elseif ($status === 'Annulé') { $badge_class = "bg-danger text-white"; }
                                        ?>
                                        <span class="badge rounded-0 text-uppercase font-monospace p-2 <?= $badge_class ?>" style="font-size: 0.65rem;">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="menu_detail.php?id=<?= $commande['menu_id'] ?? 1 ?>" class="btn btn-outline-secondary btn-sm rounded-0" style="font-size: 0.7rem;">
                                                📄 Fiche
                                            </a>
                                            
                                            <?php if ($status === 'En attente'): ?>
                                                <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette prestation ?');" style="display:inline;">
                                                    <input type="hidden" name="commande_id" value="<?= $c_id ?>">
                                                    <button type="submit" name="annuler_commande" class="btn btn-danger text-white btn-sm rounded-0" style="font-size: 0.7rem;">
                                                        🛑 Annuler
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-light btn-sm rounded-0 text-muted border" style="font-size: 0.7rem;" disabled title="Non annulable à ce stade">
                                                    🔒 Verrouillé
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>