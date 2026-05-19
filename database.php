<?php
// Configuration standard pour XAMPP en local
$host = '127.0.0.1:3307'; // Utiliser l'IP directe est souvent plus stable que 'localhost'
$db   = 'ecf_vitegourmand';
$user = 'root';
$pass = ''; // Vide par défaut sur XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Affichage propre de l'erreur si la connexion échoue
     die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>