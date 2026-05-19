<?php
/**
 * FONCTION DE CALCUL DU PRIX DE LA PRESTATION (CONFORME ENONCÉ ECF)
 * Calcule le total des menus, les réductions grands groupes et les taxes kilométriques.
 */
function calculerPrixPrestation($prix_menu_min, $pers_min, $pers_choisi, $ville_livraison, $km_hors_bordeaux = 0) {
    
    // 1. Vérification du nombre minimum de personnes requis
    if ($pers_choisi < $pers_min) {
        return ["erreur" => "Le nombre de personnes est inférieur au minimum requis de " . $pers_min . " personnes."];
    }

    // 2. Calcul du prix de base par personne
    // L'énoncé indique : "Le prix pour le nombre de personne minimale"
    $prix_unitaire_theorique = $prix_menu_min / $pers_min;
    $total_menus = $prix_unitaire_theorique * $pers_choisi;
    
    // 3. Règle métier : Réduction de 10% pour les grands groupes
    // "Réduction de 10% si 5 personnes de plus que le nombre minimum indiqué"
    $reduction = 0;
    if ($pers_choisi >= ($pers_min + 5)) {
        $reduction = $total_menus * 0.10; // 10% de remise
        $total_menus = $total_menus - $reduction;
    }

    // 4. Règle métier : Calcul des frais de livraison hors Bordeaux
    // "5 euros (majoré de 59 centimes par kilomètre parcouru) si pas dans la ville de bordeaux"
    $frais_livraison = 0;
    if (strtolowertrim($ville_livraison) !== 'bordeaux') {
        $frais_livraison = 5.00 + (0.59 * $km_hors_bordeaux);
    }

    // 5. Total Général
    $total_general = $total_menus + $frais_livraison;

    return [
        "prix_base_menus" => $total_menus + $reduction,
        "reduction_groupe" => $reduction,
        "total_menus_final" => $total_menus,
        "frais_livraison" => $frais_livraison,
        "total_general" => $total_general
    ];
}

// Petite fonction outil pour nettoyer le texte des villes
function strtolowertrim($texte) {
    return strtolower(trim(preg_replace('/\s+/', '', $texte)));
}

// =============================================================
// EXEMPLE DE TEST POUR LE JURY (A titre d'illustration)
// =============================================================
/*
$simulation = calculerPrixPrestation(350, 10, 15, 'Mérignac', 12);
print_r($simulation);
*/
?>