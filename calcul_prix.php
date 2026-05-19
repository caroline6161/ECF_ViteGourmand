<?php

function calculerPrixPrestation($prix_menu_min, $pers_min, $pers_choisi, $ville_livraison, $km_hors_bordeaux = 0) {
    
    
    if ($pers_choisi < $pers_min) {
        return ["erreur" => "Le nombre de personnes est inférieur au minimum requis de " . $pers_min . " personnes."];
    }

    
    $prix_unitaire_theorique = $prix_menu_min / $pers_min;
    $total_menus = $prix_unitaire_theorique * $pers_choisi;
    
   
    $reduction = 0;
    if ($pers_choisi >= ($pers_min + 5)) {
        $reduction = $total_menus * 0.10; 
        $total_menus = $total_menus - $reduction;
    }

  
    $frais_livraison = 0;
    if (strtolowertrim($ville_livraison) !== 'bordeaux') {
        $frais_livraison = 5.00 + (0.59 * $km_hors_bordeaux);
    }

  
    $total_general = $total_menus + $frais_livraison;

    return [
        "prix_base_menus" => $total_menus + $reduction,
        "reduction_groupe" => $reduction,
        "total_menus_final" => $total_menus,
        "frais_livraison" => $frais_livraison,
        "total_general" => $total_general
    ];
}

function strtolowertrim($texte) {
    return strtolower(trim(preg_replace('/\s+/', '', $texte)));
}

?>