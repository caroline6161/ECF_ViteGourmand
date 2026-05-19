<?php
// 1. Connexion à la base de données au tout début du script
require_once 'config/database.php';

// 2. Récupération des avis UNIQUEMENT s'ils ont le statut "valide"
try {
    // Requête simplifiée et 100% conforme à ta structure phpMyAdmin
    $requeteAvis = $pdo->query("
        SELECT commentaire, note, nom_client AS prenom 
        FROM avis 
        WHERE statut = 'valide'
        ORDER BY id DESC 
        LIMIT 3
    ");
    $avisValides = $requeteAvis->fetchAll();
} catch (Exception $e) {
    // Affichage de l'erreur au cas où (très utile pour l'examen)
    echo "<div class='alert alert-danger text-center m-3 small'>⚠️ Erreur SQL : " . $e->getMessage() . "</div>";
    $avisValides = [];
}

// 3. On inclut le header
include 'includes/header.php';
?>

<div class="py-5 text-center bg-white border-bottom" style="background: linear-gradient(rgba(26, 34, 56, 0.02), rgba(26, 34, 56, 0.05));">
    <div class="container py-5">
        <h6 class="text-uppercase small mb-3" style="color: var(--accent); letter-spacing: 3px; font-weight: 600;">Julie & José présentent</h6>
        <h1 class="display-3 mb-4" style="font-family: 'Playfair Display', serif; font-weight: 700;">Vite & Gourmand</h1>
        <p class="lead text-muted mx-auto mb-5" style="max-width: 600px; font-family: 'Inter', sans-serif; font-weight: 300;">
            L'excellence d'un traiteur d'exception combinée à la rapidité d'un service haut de gamme. Vos réceptions méritent la perfection.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="menus.php" class="btn btn-premium">Découvrir la Carte</a>
            <a href="register.php" class="btn btn-outline-dark rounded-0 px-4 py-3 text-uppercase small fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Devenir Membre</a>
        </div>
    </div>
</div>

<div class="container py-5 my-4">
    <div class="row align-items-center g-5">
        <div class="col-md-6">
            <h6 class="text-uppercase small mb-2" style="color: var(--accent); letter-spacing: 2px;">Notre Philosophie</h6>
            <h2 class="mb-4" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Une exigence rare, des produits d'exception.</h2>
            <div style="width: 50px; height: 2px; background: var(--accent);" class="mb-4"></div>
            <p class="text-muted" style="line-height: 1.8;">
                Fondé par Julie et José, deux passionnés d'art culinaire, <strong>Vite & Gourmand</strong> redéfinit le métier de traiteur. Nous sélectionnons des produits locaux de première qualité pour concevoir des menus gastronomiques uniques, livrés prêts à être dégustés.
            </p>
            <p class="text-muted" style="line-height: 1.8;">
                Que ce soit pour un dîner privé raffiné ou un grand événement d'entreprise, notre équipe déploie un savoir-faire millimétré pour ravir les palais les plus exigeants.
            </p>
        </div>
        <div class="col-md-6">
            <div class="p-5 bg-white border shadow-sm text-center">
                <h3 style="font-family: 'Playfair Display', serif;" class="mb-3">Une Réception en vue ?</h3>
                <p class="small text-muted mb-4">Commandez vos coffrets ou menus gastronomiques 24h à l'avance.</p>
                <div class="p-3 border-top border-bottom my-4" style="background: var(--bg-soft);">
                    <span class="d-block small text-uppercase text-muted" style="letter-spacing: 1px;">Téléphone Privé</span>
                    <span class="fs-4 fw-bold" style="color: var(--primary);">01 45 23 89 00</span>
                </div>
                <a href="login.php" class="text-decoration-none small fw-bold text-uppercase" style="color: var(--accent); letter-spacing: 1px;">Accéder à votre Espace Client &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family: 'Playfair Display', serif;">Ce que disent nos gourmands</h2>
            <p class="text-muted small">Des retours authentiques validés par notre équipe cuisine</p>
            <hr class="w-25 mx-auto" style="border-color: #c5a880;">
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (empty($avisValides)): ?>
                <div class="col-12 text-center">
                    <p class="text-secondary fst-italic">Aucun avis n'a encore été publié pour le moment. Soyez le premier à commander pour nous laisser votre retour !</p>
                </div>
            <?php else: ?>
                <?php foreach ($avisValides as $avis): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 text-center">
                            <div class="mb-2 text-warning" style="font-size: 1.2rem;">
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    echo ($i <= $avis['note']) ? '★' : '☆';
                                }
                                ?>
                            </div>
                            
                            <p class="card-text text-secondary small flex-grow-1" style="line-height: 1.6;">
                                "<?= htmlspecialchars($avis['commentaire']) ?>"
                            </p>
                            
                            <h6 class="mt-3 mb-0 text-dark font-weight-bold" style="letter-spacing: 1px;">
                                <?= htmlspecialchars($avis['prenom']) ?>.
                            </h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Client Vérifié</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
// On inclut le footer tout à la fin pour fermer proprement la page HTML
include 'includes/footer.php'; 
?>