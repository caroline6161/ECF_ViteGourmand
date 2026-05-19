<?php
include 'includes/header.php';
require_once 'config/database.php';

// Sécurité : Strictement réservé à l'Admin suprême (Rôle 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Accès interdit. Réservé à l'administrateur.</div></div>";
    include 'includes/footer.php';
    exit;
}

$msg = "";

// 1. ACTION : CRÉATION D'UN COMPTE EMPLOYÉ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_employe'])) {
    $email = htmlspecialchars(trim($_POST['email']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $password_clair = $_POST['password']; // Le mot de passe choisi par l'admin
    
    // On hache le mot de passe pour la sécurité MySQL
    $password_hash = password_hash($password_clair, PASSWORD_BCRYPT);
    
    // Vérification si l'email existe déjà
    $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $verif->execute([$email]);
    if ($verif->fetch()) {
        $msg = "<div class='alert alert-danger rounded-0 small'>Cet email est déjà utilisé.</div>";
    } else {
        // Insertion de l'employé (on assume que le role_id pour employé est 3)
        // Note : ajuste 'role_id' selon ton schéma de base
        $ins = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, est_actif) VALUES (?, ?, ?, 3, 1)");
        if ($ins->execute([$nom, $email, $password_hash])) {
            
            // Simulation de l'email imposée par José : l'employé reçoit un mail mais SANS le mot de passe
            $subject = "Création de votre accès employé - Vite & Gourmand";
            $message_mail = "Bonjour $nom, votre administrateur vous a créé un compte employé avec l'identifiant : $email. Pour des raisons de sécurité, votre mot de passe ne vous est pas communiqué par mail. Veuillez vous rapprocher de lui pour l'obtenir.";
            
            $_SESSION['notification_mail'] = "📧 [Mail RH Envoyé à $email] : $message_mail";
            $msg = "<div class='alert alert-success rounded-0 small'>Compte employé créé ! Alerte RH simulée.</div>";
        }
    }
}

// 2. ACTION : TOOGLE ACTIF / INACTIF (Rendre un compte inutilisable)
if (isset($_GET['toggle_id'])) {
    $emp_id = (int)$_GET['toggle_id'];
    
    // On inverse la valeur de est_actif (0 devient 1, 1 devient 0)
    $stmt = $pdo->prepare("UPDATE utilisateurs SET est_actif = 1 - est_actif WHERE id = ? AND role_id = 3");
    $stmt->execute([$emp_id]);
    header('Location: admin_employes.php');
    exit;
}

// Récupération de tous les comptes employés existants
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE role_id = 3 ORDER BY id DESC");
$stmt->execute();
$employes = $stmt->fetchAll();
?>

<div class="container py-5">
    <?php if (isset($_SESSION['notification_mail'])): ?>
        <div class="alert alert-info rounded-0 small font-monospace mb-4">
            <?= $_SESSION['notification_mail']; unset($_SESSION['notification_mail']); ?>
        </div>
    <?php endif; ?>
    
    <?= $msg ?>

    <div class="row g-5">
        <div class="col-lg-4">
            <div class="bg-white p-4 border shadow-sm">
                <h4 style="font-family: 'Playfair Display', serif;" class="mb-3">Nouvel Employé</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Nom de l'employé</label>
                        <input type="text" name="nom" class="form-control rounded-0" placeholder="Ex: Jean Logistique" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold">Email (Identifiant)</label>
                        <input type="email" name="email" class="form-control rounded-0" placeholder="Ex: jean@viteetgourmand.fr" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-uppercase fw-bold">Mot de passe provisoire</label>
                        <input type="password" name="password" class="form-control rounded-0" placeholder="À lui transmettre en main propre" required>
                    </div>
                    <button type="submit" name="creer_employe" class="btn btn-premium w-100">Créer l'accès</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="bg-white p-4 border shadow-sm">
                <h4 style="font-family: 'Playfair Display', serif;" class="mb-4">Gestion des Effectifs Employés</h4>
                
                <?php if(empty($employes)): ?>
                    <p class="text-muted small">Aucun compte employé créé pour l'instant.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Email / Username</th>
                                    <th>Statut</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($employes as $emp): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($emp['nom']) ?></strong></td>
                                    <td class="font-monospace small"><?= htmlspecialchars($emp['email']) ?></td>
                                    <td>
                                        <?php if($emp['est_actif'] == 1): ?>
                                            <span class="badge bg-success rounded-0 text-uppercase">Opérationnel</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-0 text-uppercase">Compte Révoqué</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if($emp['est_actif'] == 1): ?>
                                            <a href="admin_employes.php?toggle_id=<?= $emp['id'] ?>" class="btn btn-sm btn-outline-danger rounded-0">Désactiver l'accès</a>
                                        <?php else: ?>
                                            <a href="admin_employes.php?toggle_id=<?= $emp['id'] ?>" class="btn btn-sm btn-success rounded-0">Réactiver l'accès</a>
                                        <?php endif; ?>
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
</div>

<?php include 'includes/footer.php'; ?>