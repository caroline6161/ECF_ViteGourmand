<?php
include 'includes/header.php';
require_once 'config/database.php';

// --- RECUPERATION DES FILTRES DEPUIS L'URL ---
$prix_max = isset($_GET['prix_max']) ? (float)$_GET['prix_max'] : null;
$prix_min_fourchette = isset($_GET['prix_min_f']) ? (float)$_GET['prix_min_f'] : null;
$prix_max_fourchette = isset($_GET['prix_max_f']) ? (float)$_GET['prix_max_f'] : null;
$theme = isset($_GET['theme']) ? trim($_GET['theme']) : '';
$regime = isset($_GET['regime']) ? trim($_GET['regime']) : '';
$nb_pers = isset($_GET['nb_pers_min']) ? (int)$_GET['nb_pers_min'] : null;

// --- SÉCURISATION & CONSTRUCTION DE LA REQUÊTE SQL ---
$sql = "SELECT * FROM menus WHERE 1=1";
$params = [];

if ($prix_max) {
    $sql .= " AND prix_base <= ?";
    $params[] = $prix_max;
}
if ($prix_min_fourchette && $prix_max_fourchette) {
    $sql .= " AND prix_base BETWEEN ? AND ?";
    $params[] = $prix_min_fourchette;
    $params[] = $prix_max_fourchette;
}
if (!empty($theme)) {
    $sql .= " AND theme = ?";
    $params[] = $theme;
}
if (!empty($regime)) {
    $sql .= " AND regime = ?";
    $params[] = $regime;
}
if ($nb_pers) {
    $sql .= " AND nb_pers_min >= ?";
    $params[] = $nb_pers;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$menus = $stmt->fetchAll();
?>

<div class="container-fluid py-5">
    <div class="row px-xl-5">
        <div class="col-lg-3 mb-4">
            <div class="bg-white p-4 shadow-sm border">
                <h4 style="font-family: 'Playfair Display', serif;" class="mb-4">Affiner la recherche</h4>
                <form method="GET" action="menus.php">
                    
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Prix maximum</label>
                        <input type="number" name="prix_max" class="form-control rounded-0" value="<?= $prix_max ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Fourchette de prix (€)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="prix_min_f" placeholder="Min" class="form-control rounded-0" value="<?= $prix_min_fourchette ?>">
                            <input type="number" name="prix_max_f" placeholder="Max" class="form-control rounded-0" value="<?= $prix_max_fourchette ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Par Thème</label>
                        <select name="theme" class="form-select rounded-0">
                            <option value="">Tous</option>
                            <option value="Ocean" <?= $theme == 'Ocean' ? 'selected' : '' ?>>Océan</option>
                            <option value="Gastronomie" <?= $theme == 'Gastronomie' ? 'selected' : '' ?>>Gastronomie</option>
                            <option value="Nature" <?= $theme == 'Nature' ? 'selected' : '' ?>>Nature</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Régime Alimentaire</label>
                        <select name="regime" class="form-select rounded-0">
                            <option value="">Tous</option>
                            <option value="Vegan" <?= $regime == 'Vegan' ? 'selected' : '' ?>>Vegan / Végétarien</option>
                            <option value="Sans Gluten" <?= $regime == 'Sans Gluten' ? 'selected' : '' ?>>Sans Gluten</option>
                            <option value="Classique" <?= $regime == 'Classique' ? 'selected' : '' ?>>Traditionnel</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-uppercase fw-bold">Pour au moins (pers.)</label>
                        <input type="number" name="nb_pers_min" class="form-control rounded-0" value="<?= $nb_pers ?>">
                    </div>

                    <button type="submit" class="btn btn-dark rounded-0 w-100 text-uppercase fw-bold" style="background:#1a2536;">Filtrer la carte</button>
                    <a href="menus.php" class="btn btn-link w-100 text-center text-muted small mt-2 text-decoration-none">Réinitialiser</a>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row g-4">
                <?php if (empty($menus)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Aucun menu d'exception ne correspond à vos filtres actuels.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($menus as $menu): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 bg-white border shadow-sm rounded-0">
                                <div class="card-body p-4 d-flex flex-column">
                                    <span class="badge bg-light text-dark align-self-start mb-2 rounded-0 font-monospace small" style="border: 1px solid #ddd;"><?= htmlspecialchars($menu['theme'] ?? 'Gastronomique') ?></span>
                                    <h4 class="card-title" style="font-family: 'Playfair Display', serif; color: #1a2536;"><?= htmlspecialchars($menu['titre']) ?></h4>
                                    <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($menu['description'], 0, 120)) ?>...</p>
                                    
                                    <div class="bg-light p-3 my-3 rounded-0 small text-muted border-start border-dark border-3">
                                        <div class="mb-1">⚠️ <strong>Allergènes :</strong> <?= htmlspecialchars($menu['allergenes'] ?? 'Aucun signalé') ?></div>
                                        <div class="mb-1">📦 <strong>Stock :</strong> <?= $menu['stock_dispo'] ?> dispo(s)</div>
                                        <div>👥 <strong>Minimum :</strong> <?= $menu['nb_pers_min'] ?> personnes</div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="fs-5 fw-bold" style="color: #1a2536;"><?= number_format($menu['prix_base'], 2, ',', ' ') ?> €<span class="small text-muted" style="font-size:0.75rem;">/pers</span></span>
                                        <a href="menu_detail.php?id=<?= $menu['menu_id'] ?? $menu['id'] ?>" class="btn btn-outline-dark btn-sm rounded-0 text-uppercase small" style="font-size: 0.75rem; font-weight: 600;">📄 Voir le Menu</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>