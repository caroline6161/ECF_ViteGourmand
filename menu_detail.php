<?php
include 'includes/header.php';
require_once 'config/database.php';

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();
?>

<div class="container py-5" style="background-color: #f8f9fa;">
    
    <div class="bg-white border shadow-sm p-5 mx-auto" style="max-width: 900px;">
        
        <div class="row">
            <div class="col-12 mb-4 border-bottom pb-3">
                <span class="badge bg-secondary text-uppercase rounded-0 mb-2"><?= htmlspecialchars($menu['theme']) ?></span>
                <h1 class="fw-bold" style="font-family: 'Playfair Display', serif; color: #1a2536;"><?= htmlspecialchars($menu['titre']) ?></h1>
            </div>

            <div class="col-lg-7">
                <h5 class="text-uppercase text-secondary mb-3" style="font-size: 0.9rem; letter-spacing: 1px;">Description</h5>
                <p class="text-muted" style="line-height: 1.8; font-size: 1.1rem;"><?= nl2br(htmlspecialchars($menu['description'])) ?></p>
            </div>

            <div class="col-lg-5">
                <div class="bg-light p-4 border">
                    <h6 class="fw-bold text-uppercase mb-3">Informations pratiques</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2">⚠️ <strong>Allergènes :</strong> <?= htmlspecialchars($menu['allergenes'] ?? 'Aucun') ?></li>
                        <li class="mb-2">📦 <strong>Stock :</strong> <?= $menu['stock_dispo'] ?> unités</li>
                        <li class="mb-2">👥 <strong>Minimum :</strong> <?= $menu['nb_pers_min'] ?> personnes</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4 border-top align-items-center">
            <div class="col-md-6">
                <span class="text-muted">Prix :</span>
                <h3 class="fw-bold mb-0"><?= number_format($menu['prix_base'], 2, ',', ' ') ?> € / pers.</h3>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="commande.php?id=<?= $menu['menu_id'] ?>" class="btn btn-dark btn-lg rounded-0 px-5">
                    RÉSERVER CETTE FORMULE
                </a>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>