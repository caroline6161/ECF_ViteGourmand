<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32)); // Génère un jeton unique
    $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $stmt = $pdo->prepare("UPDATE utilisateurs SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expiry, $email]);

    // Dans un vrai projet, on enverrait un mail ici avec le lien :
    // reset_confirm.php?token=$token
    echo "Un lien de réinitialisation a été généré (Jeton : $token)";
}
?>
<form method="POST">
    <input type="email" name="email" placeholder="Votre email" required>
    <button type="submit">Recevoir le lien</button>
</form>