<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/function.php';
include '../includes/header.php';
?>
<div class="container mt-5">
    <h2>Inscription</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="date_naissance" class="form-label">Date de naissance</label>
            <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">Genre</label>
            <select class="form-control" id="genre" name="genre" required>
                <option value="H">Homme</option>
                <option value="F">Femme</option>
                <option value="Autre">Autre</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" class="form-control" id="ville" name="ville" required>
        </div>
        <div class="mb-3">
            <label for="mdp" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mdp" name="mdp" required>
        </div>
        <div class="mb-3">
            <label for="image_profil" class="form-label">Image de profil</label>
            <input type="file" class="form-control" id="image_profil" name="image_profil">
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
        <a href="login.php" class="btn btn-link">Déjà inscrit ?</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?> 