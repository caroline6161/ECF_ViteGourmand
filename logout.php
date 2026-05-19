<?php
session_start();
// On détruit toutes les variables de session
$_SESSION = array();
session_destroy();

// On redirige vers l'accueil
header('Location: index.php');
exit;