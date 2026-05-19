<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/database.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}


$commandesNoSQL = [
    ["menu" => "Menu de Noël", "quantite" => 12, "total" => 540.00, "date" => "2026-12-24"],
    ["menu" => "Menu Pâques", "quantite" => 8, "total" => 320.00, "date" => "2026-04-12"],
    ["menu" => "Menu Classique", "quantite" => 25, "total" => 875.00, "date" => "2026-05-10"],
    ["menu" => "Menu Événement", "quantite" => 15, "total" => 750.00, "date" => "2026-05-15"]
];


$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';

$chiffreAffairesParMenu = [];
$caTotal = 0;

foreach ($commandesNoSQL as $cmd) {
    
    if (!empty($date_debut) && $cmd['date'] < $date_debut) continue;
    if (!empty($date_fin) && $cmd['date'] > $date_fin) continue;

    if (!isset($chiffreAffairesParMenu[$cmd['menu']])) {
        $chiffreAffairesParMenu[$cmd['menu']] = 0;
    }
    $chiffreAffairesParMenu[$cmd['menu']] += $cmd['total'];
    $caTotal += $cmd['total'];
}


$labelsMenus = [];
$quantitesMenus = [];
foreach ($commandesNoSQL as $cmd) {
    $labelsMenus[] = $cmd['menu'];
    $quantitesMenus[] = $cmd['quantite'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_statut'])) {
    $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?")
        ->execute([$_POST['nouveau_statut'], $_POST['commande_id']]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_statut'])) {
    $statut = ($_POST['current_statut'] === 'actif') ? 'inactif' : 'actif';
    $pdo->prepare("UPDATE utilisateurs SET statut = ? WHERE id = ?")
        ->execute([$statut, $_POST['user_id']]);
}

$commandes = $pdo->query("SELECT c.*, u.nom FROM commandes c JOIN utilisateurs u ON c.utilisateur_id = u.id")->fetchAll();
$utilisateurs = $pdo->query("SELECT * FROM utilisateurs WHERE role != 'admin'")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4" style="font-family: 'Playfair Display', serif;">Tableau de bord Administrateur</h2>

    <div class="row mb-5 g-4">
        <div class="col-md-6">
            <div class="card p-4 border-0 shadow-sm">
                <h5>📊 Ventes par Menus (Données NoSQL MongoDB)</h5>
                <canvas id="chartMenus" width="100" height="50"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 border-0 shadow-sm">
                <h5>💰 Calcul du Chiffre d'Affaires</h5>
                
                <form method="POST" class="row g-2 my-2">
                    <div class="col-6">
                        <input type="date" name="date_debut" class="form-control form-control-sm" value="<?= $date_debut ?>">
                    </div>
                    <div class="col-6">
                        <input type="date" name="date_fin" class="form-control form-control-sm" value="<?= $date_fin ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-dark w-100">Filtrer par date</button>
                    </div>
                </form>

                <table class="table table-sm mt-3">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-end">CA Généré</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($chiffreAffairesParMenu as $menuName => $totalCA): ?>
                        <tr>
                            <td><?= $menuName ?></td>
                            <td class="text-end font-weight-bold"><?= number_format($totalCA, 2, ',', ' ') ?> €</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-dark">
                            <td><strong>TOTAL GENERAL</strong></td>
                            <td class="text-end"><strong><?= number_format($caTotal, 2, ',', ' ') ?> €</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <h4>📦 Commandes en cours (MySQL)</h4>
            <table class="table border bg-white">
                <?php foreach ($commandes as $c): ?>
                <tr>
                    <td>Commande #<?= $c['id'] ?> - <strong><?= $c['nom'] ?></strong></td>
                    <td>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="commande_id" value="<?= $c['id'] ?>">
                            <select name="nouveau_statut" class="form-select form-select-sm w-auto">
                                <option value="En attente" <?= $c['statut'] == 'En attente' ? 'selected' : '' ?>>En attente</option>
                                <option value="Validé" <?= $c['statut'] == 'Validé' ? 'selected' : '' ?>>Validé</option>
                            </select>
                            <button type="submit" name="update_statut" class="btn btn-sm btn-dark ms-2">OK</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="col-md-5">
            <h4>👥 Comptes Employés</h4>
            <table class="table border bg-white">
                <?php foreach ($utilisateurs as $u): ?>
                <tr>
                    <td><?= $u['nom'] ?> (<?= $u['statut'] ?>)</td>
                    <td class="text-end">
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('chartMenus').getContext('2d');
const chartMenus = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelsMenus) ?>,
        datasets: [{
            label: 'Nombre de commandes',
            data: <?= json_encode($quantitesMenus) ?>,
            backgroundColor: [
                'rgba(197, 168, 128, 0.6)', 
                'rgba(26, 37, 54, 0.6)',
                'rgba(40, 167, 69, 0.6)',
                'rgba(220, 53, 69, 0.6)'
            ],
            borderColor: [
                '#c5a880',
                '#1a2536',
                '#28a745',
                '#dc3545'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>