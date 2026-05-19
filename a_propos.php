<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/header.php'; 
?>

<div class="container py-5" style="min-height: 75vh;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 style="font-family: 'Playfair Display', serif;" class="mb-4">Julie & José</h2>
            <h5 class="text-muted mb-4">Traiteurs passionnés à Bordeaux depuis 27 ans.</h5>
            <p class="text-justify text-secondary" style="line-height: 1.8;">
                Depuis un quart de siècle, notre équipe met tout son savoir-faire et sa passion au service de vos événements. Que ce soit pour un mariage élégant, un séminaire d'entreprise ou un repas intimiste, nous sélectionnons rigoureusement des produits locaux et de saison pour ravir vos papilles.
            </p>
            <p class="text-secondary" style="line-height: 1.8;">
                Le professionnalisme de notre équipe, notre rigueur logistique et notre amour de la gastronomie font de <strong>ViteGourmand</strong> un partenaire de confiance pour tous vos moments de partage.
            </p>
        </div>
        <div class="col-md-6 text-center">
            <div class="border p-5 bg-light d-inline-block shadow-sm">
                <h3 style="font-family: 'Playfair Display', serif;">27 Ans d'Excellence</h3>
                <p class="text-muted small">Gastronomie & Service Premium</p>
                <hr class="w-50 mx-auto">
                <span class="badge bg-dark p-2">Équipe Professionnelle</span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>