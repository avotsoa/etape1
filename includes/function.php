<?php
// Fichier pour les fonctions utilitaires

function is_logged_in() {
    return isset($_SESSION['id_membre']);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
} 