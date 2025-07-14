<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/function.php';

$erreur = '';
if (!empty($_POST['email']) && !empty($_POST['mdp'])) {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $stmt = $pdo->prepare('SELECT * FROM membre WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && $user['mdp'] === $mdp) {
        $_SESSION['id_membre'] = $user['id_membre'];
        $_SESSION['nom'] = $user['nom'];
        redirect('objets.php');
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
include '../includes/header.php';
?>
<div class="container mt-5">
    <h2>Connexion</h2>
    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="mdp" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mdp" name="mdp" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
        <a href="register.php" class="btn btn-link">Créer un compte</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?> 