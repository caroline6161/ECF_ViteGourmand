<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';

$message_status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $titre = htmlspecialchars(trim($_POST['titre']));
    $description = htmlspecialchars(trim($_POST['description']));

    if (!empty($email) && !empty($titre) && !empty($description)) {
        
        
        $message_status = "<div class='alert alert-success rounded-0 small text-center'> Votre message a été simulé et envoyé par mail avec succès à l'équipe logistique !</div>";
    } else {
        $message_status = "<div class='alert alert-danger rounded-0 small text-center'>❌ Veuillez remplir tous les champs du formulaire.</div>";
    }
}
?>

<div class="container py-5">
    <div class="row align-items-center mb-5 pb-5 border-bottom">
        <div class="col-md-6">
            <h6 class="text-uppercase small text-muted mb-2" style="letter-spacing: 2px; color: #c5a880 !important;">Notre Histoire</h6>
            <h2 class="mb-4" style="font-family: 'Playfair Display', serif; color: #1a2536; font-size: 2.5rem;">Julie & José, Traiteurs depuis 27 ans</h2>
            <p class="lead text-muted" style="line-height: 1.8;">
                Installés au cœur de Bordeaux depuis un quart de siècle, nous mettons notre passion culinaire au service de vos plus grands moments. Qu'il s'agisse d'un repas intimiste de Noël, d'un banquet de Pâques ou d'un séminaire d'entreprise d'envergure, nous façonnons une carte vivante et en constante évolution.
            </p>
            <p class="text-muted" style="line-height: 1.7;">
                Notre philosophie repose sur la sélection rigoureuse de produits locaux de la région Nouvelle-Aquitaine et sur une rigueur d'exécution sans faille. Notre équipe de cuisine et notre service logistique hautement qualifié garantissent la réussite gastronomique de votre événement.
            </p>
        </div>
        <div class="col-md-6 text-center">
            <div class="bg-light p-5 border shadow-sm text-secondary rounded-0 font-monospace" style="height: 350px; display: flex; align-items: center; justify-content: center;">
                <div>
                    <span style="font-size: 5rem;">🧑‍🍳✨👩‍🍳</span>
                    <h4 class="mt-3" style="font-family: 'Playfair Display', serif; color: #1a2536;">Savoir-Faire & Tradition</h4>
                    <p class="small text-uppercase tracking-wider text-muted">Équipe Professionnelle Bordelaise</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="bg-white p-4 p-md-5 border shadow-sm">
                <h4 class="text-center mb-2" style="font-family: 'Playfair Display', serif; color: #1a2536;">Formulaire de Contact</h4>
                <p class="text-center text-muted small mb-4">Une question sur un menu ou une prestation ? Écrivez-nous.</p>
                
                <?= $message_status ?>

                <form method="POST" action="contact.php">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold text-muted">Votre Adresse Email</label>
                        <input type="email" name="email" class="form-control rounded-0" placeholder="exemple@mail.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold text-muted">Sujet de votre demande</label>
                        <input type="text" name="titre" class="form-control rounded-0" placeholder="Ex: Demande d'informations logistiques" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-uppercase fw-bold text-muted">Description de votre message</label>
                        <table class="w-100">
                            <tr>
                                <td>
                                    <textarea name="description" class="form-control rounded-0" rows="5" placeholder="Détaillez votre besoin ici..." required></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark rounded-0 text-uppercase fw-bold py-2" style="background: #1a2536; letter-spacing: 1px;">
                            ✉️ Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>