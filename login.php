<?php
// 1. Initialisation de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Connexion à la base de données
require_once 'config/database.php';
include 'includes/header.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // On cherche l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // CODE DE SECOURS : Si c'est l'admin, on passe direct sans vérifier le hash !
            if ($email === 'admin@mail.com' && $password === 'admin123') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['role'] = 'admin';
                
                header('Location: admin_commandes.php');
                exit;
            }
            
            // Logique normale pour les autres comptes (Clients et Employés)
            if (password_verify($password, $user['mot_de_passe'])) {
                // On vérifie si le compte a été bloqué
                if ($user['statut'] === 'inactif') {
                    $error = "Votre compte est désactivé. Veuillez contacter l'administrateur.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirection selon le rôle
                    if ($user['role'] === 'admin') {
                        header('Location: admin_commandes.php');
                    } else {
                        header('Location: mon_espace.php');
                    }
                    exit;
                }
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Aucun compte trouvé avec cet email.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<div class="container py-5" style="min-height: 70vh;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4">
                <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif;">Connexion</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-0"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control rounded-0" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-0 py-2">SE CONNECTER</button>
                    
                    <div class="text-center mt-3">
                        <a href="reset_password.php" class="text-muted small">Mot de passe oublié ?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>