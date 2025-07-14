<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/function.php';

// Récupération des catégories pour le select
$stmt = $pdo->query('SELECT * FROM categorie_objet');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$erreur = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nom_objet']) && !empty($_POST['categorie'])) {
        $nom_objet = $_POST['nom_objet'];
        $id_categorie = $_POST['categorie'];
        $id_membre = $_SESSION['id_membre'] ?? null;
        if (!$id_membre) {
            $erreur = "Vous devez être connecté pour ajouter un objet.";
        } else {
            // Insertion de l'objet
            $stmt = $pdo->prepare('INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES (?, ?, ?)');
            $stmt->execute([$nom_objet, $id_categorie, $id_membre]);
            $id_objet = $pdo->lastInsertId();

            // Gestion des images
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $has_image = false;
            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                        $filename = uniqid('img_') . '.' . $ext;
                        $dest = $upload_dir . $filename;
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $has_image = true;
                            // Enregistrement en base
                            $stmt = $pdo->prepare('INSERT INTO images_objet (id_objet, nom_image) VALUES (?, ?)');
                            $stmt->execute([$id_objet, $filename]);
                        }
                    }
                }
            }
            // Si aucune image, mettre une image par défaut
            if (!$has_image) {
                $stmt = $pdo->prepare('INSERT INTO images_objet (id_objet, nom_image) VALUES (?, ?)');
                $stmt->execute([$id_objet, 'default.png']);
            }
            $success = "Objet ajouté avec succès !";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }
}

include '../includes/header.php';
?>
<div class="container mt-5">
    <h2>Ajouter un objet</h2>
    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom_objet" class="form-label">Nom de l'objet</label>
            <input type="text" class="form-control" id="nom_objet" name="nom_objet" required>
        </div>
        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <select name="categorie" id="categorie" class="form-select" required>
                <option value="">Choisir une catégorie</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id_categorie']) ?>">
                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label">Images (vous pouvez en sélectionner plusieurs)</label>
            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter l'objet</button>
        <a href="objets.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?> 