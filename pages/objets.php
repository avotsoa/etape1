<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/function.php';

// Récupération des catégories
$stmt = $pdo->query('SELECT * FROM categorie_objet');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<div class="container mt-5">
    <h2>Liste des objets</h2>
    <form method="get" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="categorie" class="form-select">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id_categorie']) ?>" <?= (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id_categorie']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Filtrer</button>
            </div>
        </div>
    </form>

<?php
// Récupération des objets (filtrés ou non)
$where = '';
$params = [];
if (!empty($_GET['categorie'])) {
    $where = 'WHERE o.id_categorie = ?';
    $params[] = $_GET['categorie'];
}
$sql = 'SELECT o.id_objet, o.nom_objet, c.nom_categorie, m.nom AS proprietaire,
        (
            SELECT e.date_retour FROM emprunt e 
            WHERE e.id_objet = o.id_objet AND e.date_retour >= CURDATE()
            ORDER BY e.date_retour ASC LIMIT 1
        ) AS date_retour
        FROM objet o
        JOIN categorie_objet c ON o.id_categorie = c.id_categorie
        JOIN membre m ON o.id_membre = m.id_membre
        ' . $where;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$objets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <?php if (count($objets) > 0): ?>
        <ul class="list-group">
            <?php foreach ($objets as $objet): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($objet['nom_objet']) ?></strong> —
                    Catégorie : <?= htmlspecialchars($objet['nom_categorie']) ?> —
                    Propriétaire : <?= htmlspecialchars($objet['proprietaire']) ?>
                    <?php if (!empty($objet['date_retour']) && $objet['date_retour'] >= date('Y-m-d')): ?>
                        <span class="badge bg-warning text-dark ms-2">Emprunté (Retour prévu : <?= htmlspecialchars($objet['date_retour']) ?>)</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">Aucun objet trouvé.</div>
    <?php endif; ?>
</div>
</div>
<?php include '../includes/footer.php'; ?> 