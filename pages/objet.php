<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/function.php';

$id_objet = $_GET['id'] ?? null;
if (!$id_objet) {
    header('Location: objets.php');
    exit;
}

// Récupérer infos objet + propriétaire + catégorie
$stmt = $pdo->prepare('SELECT o.*, c.nom_categorie, m.nom AS proprietaire, m.id_membre FROM objet o JOIN categorie_objet c ON o.id_categorie = c.id_categorie JOIN membre m ON o.id_membre = m.id_membre WHERE o.id_objet = ?');
$stmt->execute([$id_objet]);
$objet = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$objet) {
    header('Location: objets.php');
    exit;
}

// Récupérer images
$stmt = $pdo->prepare('SELECT * FROM images_objet WHERE id_objet = ?');
$stmt->execute([$id_objet]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$image_principale = $images[0]['nom_image'] ?? 'default.png';

// Suppression d'une image si demandé et si propriétaire
if (isset($_POST['delete_image']) && isset($_SESSION['id_membre']) && $_SESSION['id_membre'] == $objet['id_membre']) {
    $img_id = $_POST['delete_image'];
    // Récupérer le nom de l'image
    $stmt = $pdo->prepare('SELECT nom_image FROM images_objet WHERE id_image = ? AND id_objet = ?');
    $stmt->execute([$img_id, $id_objet]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($img && $img['nom_image'] !== 'default.png') {
        @unlink('../uploads/' . $img['nom_image']);
    }
    // Supprimer de la base
    $stmt = $pdo->prepare('DELETE FROM images_objet WHERE id_image = ? AND id_objet = ?');
    $stmt->execute([$img_id, $id_objet]);
    // Vérifier s'il reste des images, sinon mettre default.png
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM images_objet WHERE id_objet = ?');
    $stmt->execute([$id_objet]);
    $nb = $stmt->fetchColumn();
    if ($nb == 0) {
        $stmt = $pdo->prepare('INSERT INTO images_objet (id_objet, nom_image) VALUES (?, ?)');
        $stmt->execute([$id_objet, 'default.png']);
    }
    header('Location: objet.php?id=' . $id_objet);
    exit;
}

// Récupérer historique des emprunts
$stmt = $pdo->prepare('SELECT e.*, m.nom FROM emprunt e JOIN membre m ON e.id_membre = m.id_membre WHERE e.id_objet = ? ORDER BY e.date_emprunt DESC');
$stmt->execute([$id_objet]);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<div class="container mt-5">
    <h2><?= htmlspecialchars($objet['nom_objet']) ?></h2>
    <div class="row">
        <div class="col-md-5">
            <img src="../uploads/<?= htmlspecialchars($image_principale) ?>" class="img-fluid mb-3 border" style="max-height:300px;object-fit:contain;" alt="Image principale">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($images as $idx => $img): ?>
                    <div class="position-relative d-inline-block">
                        <img src="../uploads/<?= htmlspecialchars($img['nom_image']) ?>" class="img-thumbnail" style="width:70px;height:70px;object-fit:cover;">
                        <?php if (isset($_SESSION['id_membre']) && $_SESSION['id_membre'] == $objet['id_membre']): ?>
                            <form method="post" action="" style="position:absolute;top:2px;right:2px;">
                                <input type="hidden" name="delete_image" value="<?= $img['id_image'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer l'image" onclick="return confirm('Supprimer cette image ?')">&times;</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-7">
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Catégorie :</strong> <?= htmlspecialchars($objet['nom_categorie']) ?></li>
                <li class="list-group-item"><strong>Propriétaire :</strong> <?= htmlspecialchars($objet['proprietaire']) ?></li>
            </ul>
            <h5>Historique des emprunts</h5>
            <?php if ($emprunts): ?>
                <ul class="list-group">
                    <?php foreach ($emprunts as $emp): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($emp['nom']) ?> — du <?= htmlspecialchars($emp['date_emprunt']) ?> au <?= htmlspecialchars($emp['date_retour']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="alert alert-info">Aucun emprunt pour cet objet.</div>
            <?php endif; ?>
        </div>
    </div>
    <a href="objets.php" class="btn btn-secondary mt-4">Retour à la liste</a>
</div>
<?php include '../includes/footer.php'; ?> 