<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$menu_id = (int)$_POST['menu_id'];
$adresse = htmlspecialchars(trim($_POST['adresse']));
$date_prestation = $_POST['date_prestation'];
$heure_prestation = $_POST['heure_prestation'];
$nb_personnes = (int)$_POST['nb_personnes'];
$distance = (float)$_POST['distance'];
$gsm = htmlspecialchars(trim($_POST['telephone']));

$stmt = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();

if (!$menu) {
    die("Menu invalide.");
}

if ($nb_personnes < $menu['nb_pers_min']) {
    die("Erreur : Le nombre de personnes est inférieur au minimum requis pour ce menu.");
}

if ($menu['stock_dispo'] <= 0) {
    die("Désolé, ce menu est actuellement en rupture de stock.");
}

$prix_repas = $nb_personnes * $menu['prix_base'];

if ($nb_personnes >= ($menu['nb_pers_min'] + 5)) {
    $prix_repas = $prix_repas * 0.90;
}

$frais_livraison = 0;
if ($distance > 0) {
    $frais_livraison = 5 + ($distance * 0.59);
}

$prix_total = $prix_repas + $frais_livraison;

$insert = $pdo->prepare("INSERT INTO commandes (user_id, menu_id, menu_titre, date_livraison, heure_livraison, adresse, distance, nb_personnes, prix_total, gsm_client, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')");

if ($insert->execute([$user_id, $menu_id, $menu['titre'], $date_prestation, $heure_prestation, $adresse, $distance, $nb_personnes, $prix_total, $gsm])) {
    
    $updateStock = $pdo->prepare("UPDATE menus SET stock_dispo = stock_dispo - 1 WHERE menu_id = ?");
    $updateStock->execute([$menu_id]);

    $to = $_SESSION['user_email'] ?? 'client@test.fr';
    $subject = "Confirmation de votre réservation - Vite & Gourmand";
    $message_mail = "Bonjour, Julie & José ont bien reçu votre demande de prestation pour le " . $date_prestation . ". Votre commande est en attente de validation par notre équipe.";
    
    $_SESSION['notification_mail'] = "📧 [Mail Envoyé à $to] : $message_mail";

    header('Location: mon_espace.php?success=1');
    exit;
} else {
    echo "Une erreur est survenue lors de la validation.";
}