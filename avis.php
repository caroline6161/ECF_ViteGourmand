<?php 
require_once 'config/database.php';
include 'includes/header.php'; 

$message = "";

// --- ENREGISTREMENT DE L'AVIS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_client']);
    $commentaire = trim($_POST['commentaire']);
    $note = (int)$_POST['note'];

    if (!empty($nom) && !empty($commentaire) && $note >= 1 && $note <= 5) {
        // On sauvegarde le nom écrit par l'utilisateur et on met un ID par défaut (1)
        $insert = $pdo->prepare("INSERT INTO avis (utilisateur_id, nom_client, commentaire, note) VALUES (1, ?, ?, ?)");
        $insert->execute([$nom, $commentaire, $note]);
        $message = "<div class='alert alert-success'>Merci ! Votre avis a bien été pris en compte.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs correctement.</div>";
    }
}

// --- RÉCUPÉRATION DES AVIS ---
$query = $pdo->query("SELECT * FROM avis ORDER BY date_publication DESC");
$les_avis = $query->fetchAll();
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h6 class="text-uppercase small" style="color: var(--accent); letter-spacing: 2px;">Témoignages</h6>
        <h2 class="display-5" style="font-family: 'Playfair Display', serif;">Ce qu'ils pensent de nous</h2>
        <div style="width: 60px; height: 3px; background: var(--accent); margin: 20px auto;"></div>
    </div>

    <div class="row g-5">
        <div class="col-md-4">
            <div class="bg-white p-4 shadow-sm border">
                <h4 class="mb-4" style="font-family: 'Playfair Display', serif;">Donnez votre avis</h4>
                
                <?= $message ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Votre Nom / Prénom</label>
                        <input type="text" name="nom_client" class="form-control rounded-0" placeholder="Ex: Jean Dupont" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Note</label>
                        <select name="note" class="form-select rounded-0" required>
                            <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (Très bon)</option>
                            <option value="3">⭐⭐⭐ (Moyen)</option>
                            <option value="2">⭐⭐ (Décevant)</option>
                            <option value="1">⭐ (À éviter)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-uppercase fw-bold">Votre Commentaire</label>
                        <textarea name="commentaire" rows="4" class="form-control rounded-0" required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-0 text-uppercase">Partager mon avis</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-4">
                <?php if (empty($les_avis)): ?>
                    <p class="text-muted italic px-3">Aucun avis pour le moment. Soyez le premier !</p>
                <?php else: ?>
                    <?php foreach ($les_avis as $a): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm bg-white p-4 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($a['nom_client'] ?? 'Anonyme') ?></h5>
                                    <span class="text-warning">
                                        <?= str_repeat('⭐', $a['note']) ?>
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">" <?= htmlspecialchars($a['commentaire']) ?> "</p>
                                <small class="text-uppercase text-muted d-block" style="font-size: 10px;">
                                    Posté le <?= date('d/m/Y', strtotime($a['date_publication'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>