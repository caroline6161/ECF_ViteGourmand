<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmtMenu = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
$stmtMenu->execute([$menu_id]);
$menu = $stmtMenu->fetch();

if (!$menu) {
    header('Location: menus.php');
    exit;
}

$user_nom = $_SESSION['user_nom'] ?? 'Client';
$user_prenom = $_SESSION['user_prenom'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';
$user_telephone = $_SESSION['user_telephone'] ?? $_SESSION['user_gsm'] ?? '';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    $adresse = htmlspecialchars(trim($_POST['adresse']));
    $ville = htmlspecialchars(trim($_POST['ville']));
    $distance = (float)$_POST['distance_km'];
    $date_prestation = $_POST['date_prestation'];
    $heure_prestation = $_POST['heure_prestation'];
    $nb_personnes = (int)$_POST['nb_personnes'];

    if ($nb_personnes < $menu['nb_pers_min']) {
        $message = "<div class='alert alert-danger rounded-0 small'>❌ Erreur : Le nombre de personnes ne peut pas être inférieur au minimum requis de " . $menu['nb_pers_min'] . " personnes.</div>";
    } else {
        $prix_menus_base = $menu['prix_base'] * $nb_personnes;
        
        $reduction = 0;
        if ($nb_personnes >= ($menu['nb_pers_min'] + 5)) {
            $reduction = $prix_menus_base * 0.10;
        }
        
        $frais_livraison = 0;
        if (strtolower($ville) !== 'bordeaux') {
            $frais_livraison = 5 + (0.59 * $distance);
        }

        $total_final = $prix_menus_base - $reduction + $frais_livraison;
        $statut = 'En attente';

        try {
            $insert = $pdo->prepare("INSERT INTO commandes (id_utilisateur, menu_id, total, statut, date_commande) VALUES (?, ?, ?, ?, NOW())");
            $insert->execute([$_SESSION['user_id'], $menu_id, $total_final, $statut]);
            
            echo "<script>alert('🎉 Commande validée avec succès ! Redirection vers votre espace.'); window.location.href='mon_espace.php';</script>";
            exit;
        } catch (PDOException $e) {
            try {
                $insert = $pdo->prepare("INSERT INTO commandes (utilisateur_id, menu_id, total, statut, date_commande) VALUES (?, ?, ?, ?, NOW())");
                $insert->execute([$_SESSION['user_id'], $menu_id, $total_final, $statut]);
                
                echo "<script>alert('🎉 Commande validée avec succès ! Redirection vers votre espace.'); window.location.href='mon_espace.php';</script>";
                exit;
            } catch (PDOException $ex) {
                $message = "<div class='alert alert-danger rounded-0 small'>❌ Erreur BDD : " . $ex->getMessage() . "</div>";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-2 text-center" style="font-family: 'Playfair Display', serif; color: #1a2536;">Finaliser votre Prestation</h2>
    <p class="text-center text-muted small mb-5">Veuillez compléter et vérifier les détails logistiques et financiers de votre commande.</p>

    <?= $message ?>

    <div class="row g-5">
        <div class="col-lg-7">
            <div class="bg-white p-4 border shadow-sm">
                <form method="POST" id="formCommande">
                    <h5 class="border-bottom pb-2 mb-4 text-uppercase small fw-bold text-secondary" style="letter-spacing: 1px;">1. Informations Personnelles (Auto-remplies)</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Nom</label>
                            <input type="text" class="form-control rounded-0 bg-light" value="<?= htmlspecialchars($user_nom) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Prénom</label>
                            <input type="text" class="form-control rounded-0 bg-light" value="<?= htmlspecialchars($user_prenom) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Adresse Email</label>
                            <input type="email" class="form-control rounded-0 bg-light" value="<?= htmlspecialchars($user_email) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">GSM (Téléphone)</label>
                            <input type="text" class="form-control rounded-0 bg-light" value="<?= htmlspecialchars($user_telephone) ?>" readonly>
                        </div>
                    </div>

                    <h5 class="border-bottom pb-2 mb-4 text-uppercase small fw-bold text-secondary" style="letter-spacing: 1px;">2. Détails de la Livraison & Prestation</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label small text-uppercase fw-bold">Adresse de l'événement</label>
                            <input type="text" name="adresse" class="form-control rounded-0" placeholder="Ex: 12 Rue des Capucins" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold">Ville</label>
                            <input type="text" name="ville" id="villeInput" class="form-control rounded-0" placeholder="Ex: Bordeaux, Merignac..." required>
                        </div>
                        <div class="col-md-6" id="blocDistance" style="display: none;">
                            <label class="form-label small text-uppercase fw-bold text-danger">Distance de Bordeaux (en KM)</label>
                            <input type="number" name="distance_km" id="distanceInput" class="form-control rounded-0" value="0" min="0" step="0.1">
                            <span class="text-muted" style="font-size: 0.75rem;">Taxe hors Bordeaux (+5€ + 0.59€/km)</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold">Date de la Prestation</label>
                            <input type="date" name="date_prestation" class="form-control rounded-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold">Heure souhaitée</label>
                            <input type="time" name="heure_prestation" class="form-control rounded-0" required>
                        </div>
                    </div>

                    <h5 class="border-bottom pb-2 mb-4 text-uppercase small fw-bold text-secondary" style="letter-spacing: 1px;">3. Configuration de la Formule</h5>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Nombre de personnes</label>
                        <input type="number" name="nb_personnes" id="nbPersInput" class="form-control rounded-0" value="<?= $menu['nb_pers_min'] ?>" min="<?= $menu['nb_pers_min'] ?>" required>
                        <span class="text-muted small">Minimum obligatoire : <strong><?= $menu['nb_pers_min'] ?> personnes</strong>.</span>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" name="valider_commande" class="btn btn-dark rounded-0 text-uppercase fw-bold py-3" style="background: #1a2536; letter-spacing: 1px;">
                            🤝 Confirmer et Réserver la Prestation
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="bg-light p-4 border shadow-sm sticky-top" style="top: 20px;">
                <h4 class="mb-4 text-center" style="font-family: 'Playfair Display', serif; color: #1a2536;">Récapitulatif Financier</h4>
                
                <div class="p-3 bg-white border mb-4">
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($menu['titre']) ?></h6>
                    <p class="small text-uppercase tracking-wider mb-0 text-muted" style="font-size: 0.7rem; color: #c5a880 !important;"><?= htmlspecialchars($menu['theme']) ?></p>
                    <hr class="my-2">
                    <span class="small text-muted">Tarif unitaire : <?= number_format($menu['prix_base'], 2, ',', ' ') ?> € / pers.</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Sous-total Menus :</span>
                    <span class="fw-bold" id="txtSousTotal">0,00 €</span>
                </div>

                <div class="d-flex justify-content-between mb-2 text-success" id="blocRemise" style="display: none !important;">
                    <span>Remise Commerciale (10%) :</span>
                    <span class="fw-bold" id="txtRemise">- 0,00 €</span>
                </div>

                <div class="d-flex justify-content-between mb-3 text-danger">
                    <span>Frais de Livraison :</span>
                    <span class="fw-bold" id="txtLivraison">0,00 €</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center my-3">
                    <span class="h5 mb-0 fw-bold" style="color: #1a2536;">Total Estimé TTC :</span>
                    <span class="h4 mb-0 fw-bold text-dark" id="txtTotalFinal">0,00 €</span>
                </div>

                <div class="bg-white p-3 border mt-4 border-warning">
                    <small class="text-muted d-block mb-1">⚠️ <strong>Rappel des conditions :</strong></small>
                    <small class="text-danger font-monospace" style="font-size: 0.75rem; display: block; line-height: 1.4;">
                        Ce menu nécessite une réservation validée à l'avance. Toute non-restitution du matériel prêté sous 10 jours ouvrés entraînera des pénalités forfaitaires de 600 € conformément aux CGV.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const prixUnitaire = <?= (float)$menu['prix_base'] ?>;
const nbPersMin = <?= (int)$menu['nb_pers_min'] ?>;

const villeInput = document.getElementById('villeInput');
const blocDistance = document.getElementById('blocDistance');
const distanceInput = document.getElementById('distanceInput');
const nbPersInput = document.getElementById('nbPersInput');

const txtSousTotal = document.getElementById('txtSousTotal');
const blocRemise = document.getElementById('blocRemise');
const txtRemise = document.getElementById('txtRemise');
const txtLivraison = document.getElementById('txtLivraison');
const txtTotalFinal = document.getElementById('txtTotalFinal');

function calculerPrestation() {
    let nbPers = parseInt(nbPersInput.value) || nbPersMin;
    if(nbPers < nbPersMin) nbPers = nbPersMin;

    let sousTotal = prixUnitaire * nbPers;
    txtSousTotal.innerText = sousTotal.toFixed(2).replace('.', ',') + " €";

    let remise = 0;
    if (nbPers >= (nbPersMin + 5)) {
        remise = sousTotal * 0.10;
        blocRemise.style.setProperty('display', 'flex', 'important');
        txtRemise.innerText = "- " + remise.toFixed(2).replace('.', ',') + " €";
    } else {
        blocRemise.style.setProperty('display', 'none', 'important');
    }

    let livraison = 0;
    let ville = villeInput.value.trim().toLowerCase();
    
    if (ville !== "" && ville !== "bordeaux") {
        blocDistance.style.display = "block";
        let km = parseFloat(distanceInput.value) || 0;
        livraison = 5 + (0.59 * km);
    } else {
        blocDistance.style.display = "none";
    }
    txtLivraison.innerText = livraison.toFixed(2).replace('.', ',') + " €";

    let totalFinal = sousTotal - remise + livraison;
    txtTotalFinal.innerText = totalFinal.toFixed(2).replace('.', ',') + " €";
}

villeInput.addEventListener('input', calculerPrestation);
distanceInput.addEventListener('input', calculerPrestation);
nbPersInput.addEventListener('input', calculerPrestation);

calculerPrestation();
</script>

<?php include 'includes/footer.php'; ?>