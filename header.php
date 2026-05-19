<?php
// On démarre la session au tout début pour savoir si le client est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand | Traiteur d'Exception</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        /* Style de structure de la navbar */
        h1, h2, h3, .navbar-brand { 
            font-family: 'Playfair Display', serif; 
        }

        .navbar { 
            background-color: rgba(255, 255, 255, 0.95) !important; 
            backdrop-filter: blur(10px);
            padding: 20px 0;
        }

        .navbar-brand { 
            font-size: 1.5rem;
            letter-spacing: 2px;
            color: var(--primary) !important;
        }

        .nav-link { 
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--primary) !important;
            margin: 0 15px;
        }

        .btn-premium { 
            background-color: var(--primary); 
            color: white !important; 
            border-radius: 0; 
            padding: 12px 30px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: 0.4s;
            border: 1px solid var(--primary);
        }

        .btn-premium:hover { 
            background-color: transparent; 
            color: var(--primary) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">VITE & GOURMAND</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="menus.php">La Carte</a></li>
                <li class="nav-item"><a class="nav-link" href="avis.php">Avis</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a href="mon_espace.php" class="btn btn-outline-dark rounded-0 me-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                            👤 Mon Espace
                        </a>
                    </li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a href="admin_commandes.php" class="btn btn-outline-warning rounded-0 ms-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                                💼 Administration
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="btn btn-outline-dark btn-sm rounded-0 ms-2" href="logout.php">Déconnexion</a>
                    </li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Espace Client</a></li>
                    <li class="nav-item">
                        <a class="btn btn-premium ms-lg-3" href="register.php">Rejoindre le club</a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>