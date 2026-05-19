<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
include 'includes/header.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $telephone = htmlspecialchars(trim($_POST['telephone']));

    if (!empty($nom) && !empty($email) && !empty($password)) {
        
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Désolé, cette adresse email est déjà enregistrée.";
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            try {
                
$ins = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, telephone, role_id, est_actif) VALUES (?, ?, ?, ?, 2, 1)");                
                $ins->execute([$nom, $email, $password_hash, $telephone]);                      
                $success = "Félicitations, votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.";
            } catch (PDOException $e) {
                $error = "Erreur de base de données : " . $e->getMessage();
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center my-5">
        <div class="col-md-6">
            <div class="bg-white p-5 border shadow-sm">
                <h3 style="font-family: 'Playfair Display', serif;" class="text-center mb-2">Créer un compte</h3>
                <p class="text-muted text-center small mb-4">Rejoignez le club Vite & Gourmand</p>
                <div style="width: 40px; height: 2px; background: var(--accent); margin: 0 auto 30px auto;"></div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-0 small mb-4"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success rounded-0 small mb-4"><?= $success ?></div>
                    <div class="d-grid mb-4">
                        <a href="login.php" class="btn btn-premium btn-lg">Aller à la page de connexion</a>
                    </div>
                <?php else: ?>

                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold">Nom Complet</label>
                            <input type="text" name="nom" class="form-control rounded-0" placeholder="Ex: Julie Dubois" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold">Adresse Email</label>
                            <input type="email" name="email" class="form-control rounded-0" placeholder="exemple@mail.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold">Téléphone (GSM)</label>
                            <input type="text" name="telephone" class="form-control rounded-0" placeholder="06 00 00 00 00" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold">Mot de passe</label>
                            <input type="password" name="password" class="form-control rounded-0" placeholder="••••••••" minlength="6" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-dark btn-lg rounded-0">M'inscrire</button>
                        </div>

                        <div class="text-center small mt-4">
                            <span class="text-muted">Vous avez déjà un compte ?</span><br>
                            <a href="login.php" style="color: var(--accent);" class="fw-bold text-decoration-none">Me connecter &rarr;</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>