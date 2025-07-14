<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/function.php';

$id_membre = $_GET['id'] ?? null;
if (!$id_membre) {
    header('Location: objets.php');
    exit;
}
// Infos membre
$stmt = $pdo->prepare('SELECT * FROM membre WHERE id_membre = ?');
$stmt->execute([$id_membre]);
$membre = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$membre) {
    header('Location: objets.php');
    exit;
}
// Objets du membre, regroupés par catégorie
$stmt = $pdo->prepare('SELECT o.*, c.nom_categorie, (SELECT nom_image FROM images_objet WHERE id_objet = o.id_objet LIMIT 1) AS image_principale FROM objet o JOIN categorie_objet c ON o.id_categorie = c.id_categorie WHERE o.id_membre = ? ORDER BY c.nom_categorie, o.nom_objet');
$stmt->execute([$id_membre]);
$objets = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Regroupement par catégorie
$groupes = [];
foreach ($objets as $o) {
    $groupes[$o['nom_categorie']][] = $o;
}
include '../includes/header.php';
?>
<div class="container mt-5">
    <h2>Fiche membre</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3"><?= htmlspecialchars($membre['nom']) ?></h4>
            <p><strong>Email :</strong> <?= htmlspecialchars($membre['email']) ?></p>
            <p><strong>Ville :</strong> <?= htmlspecialchars($membre['ville']) ?></p>
            <p><strong>Date de naissance :</strong> <?= htmlspecialchars($membre['date_naissance']) ?></p>
        </div>
    </div>
    <h5>Objets du membre (par catégorie)</h5>
    <?php if ($groupes): ?>
        <?php foreach ($groupes as $cat => $objs): ?>
            <div class="mb-3">
                <h6 class="bg-light p-2 border rounded">Catégorie : <?= htmlspecialchars($cat) ?></h6>
                <div class="row g-2">
                    <?php foreach ($objs as $o): ?>
                        <div class="col-md-3">
                            <div class="card h-100">
                                <img src="../uploads/<?= htmlspecialchars($o['image_principale'] ?? 'default.png') ?>" class="card-img-top" style="height:120px;object-fit:cover;">
                                <div class="card-body p-2">
                                    <div class="fw-bold mb-1"><?= htmlspecialchars($o['nom_objet']) ?></div>
                                    <a href="objet.php?id=<?= $o['id_objet'] ?>" class="btn btn-sm btn-outline-primary w-100">Voir fiche</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Ce membre n'a pas encore d'objet.</div>
    <?php endif; ?>
    <a href="objets.php" class="btn btn-secondary mt-4">Retour à la liste</a>
</div>
<?php include '../includes/footer.php'; ?> 