<?php
include 'includes/header.php';
require_once 'config/database.php';

// Sécurité : réservé aux connectés
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$commande_id = isset($_GET['commande_id']) ? (int)$_GET['commande_id'] : 0;
$error = "";
$success = false;

// Sécurité : On vérifie que la commande existe, appartient à l'utilisateur ET est bien "terminée"
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE commande_id = ? AND user_id = ? AND statut = 'terminée'");
$stmt->execute([$commande_id, $user_id]);
$commande = $stmt->fetch();

if (!$commande) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Action impossible. Vous ne pouvez laisser un avis que sur une prestation validée et terminée.</div></div>";
    include 'includes/footer.php';
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = (int)$_POST['note'];
    $commentaire = htmlspecialchars(trim($_POST['commentaire']));

    if ($note < 1 || $note > 5) {
        $error = "Veuillez attribuer une note entre 1 et 5.";
    } elseif (empty($commentaire)) {
        $error = "Le commentaire ne peut pas être vide.";
    } else {
        // Insertion de l'avis
        $ins = $pdo->prepare("INSERT INTO avis (user_id, note, commentaire, statut_validation) VALUES (?, ?, ?, 'en_attente')");
        if ($ins->execute([$user_id, $note, $commentaire])) {
            $success = true;
        } else {
            $error = "Une erreur est survenue lors de l'enregistrement.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="bg-white p-5 border shadow-sm">
                <h3 style="font-family: 'Playfair Display', serif;" class="text-center mb-2">Votre Avis nous intéresse</h3>
                <p class="text-muted text-center small mb-4">Partagez votre expérience sur le menu : <strong><?= htmlspecialchars($commande['menu_titre']) ?></strong></p>
                <div style="width: 40px; height: 2px; background: var(--accent); margin: 0 auto 30px auto;"></div>

                <?php if ($success): ?>
                    <div class="alert alert-success rounded-0 text-center small mb-4">
                        Merci ! Votre avis a bien été transmis. Il sera visible sur le site dès sa modération par l'équipe de Julie & José.
                    </div>
                    <div class="text-center">
                        <a href="mon_espace.php" class="btn btn-premium btn-sm">Retourner à mon espace</a>
                    </div>
                <?php else: ?>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-0 small"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold">Note de l'expérience</label>
                            <select name="note" class="form-select rounded-0" required>
                                <option value="5">⭐⭐⭐⭐⭐ — Exceptionnel (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ — Très bon (4/5)</option>
                                <option value="3">⭐⭐⭐ — Satisfaisant (3/5)</option>
                                <option value="2">⭐⭐ — Moyen (2/5)</option>
                                <option value="1">⭐ — Insatisfaisant (1/5)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold">Votre commentaire</label>
                            <textarea name="commentaire" class="form-control rounded-0" rows="5" placeholder="Laissez vos impressions sur les saveurs, la livraison, la logistique..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-premium">Envoyer mon évaluation</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>