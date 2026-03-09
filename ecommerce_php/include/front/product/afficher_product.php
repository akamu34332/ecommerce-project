<?php

if (empty($produits)) {
    echo '<div class="alert alert-info" role="alert">Aucun produit disponible.</div>';
} else {
    foreach ($produits as $produit) {
        $idProduit = htmlspecialchars($produit->id);
        $libelle = htmlspecialchars($produit->libelle);
        $description = htmlspecialchars($produit->description);
        $image = htmlspecialchars($produit->image);
        $dateCreation = date_format(date_create($produit->date_creation), 'Y/m/d');
        $prix = number_format($produit->prix, 2);
        $prixRemise = $produit->discount > 0
            ? number_format($produit->prix - ($produit->prix * $produit->discount / 100), 2)
            : null;
        ?>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <?php if ($produit->discount > 0): ?>
                    <span class="badge rounded-pill text-bg-warning w-25 position-absolute m-2" style="right:0;">
                        -<?= htmlspecialchars($produit->discount) ?>%
                    </span>
                <?php endif; ?>
                <img class="card-img-top w-75 mx-auto" src="../upload/produit/<?= $image ?>" alt="<?= $libelle ?>">
                <div class="card-body">
                    <a href="produit.php?id=<?= $idProduit ?>" class="btn stretched-link"></a>
                    <h5 class="card-title"><?= $libelle ?></h5>
                    <p class="card-text"><?= $description ?></p>
                    <p class="card-text"><small class="text-muted">Ajouté le : <?= $dateCreation ?></small></p>
                </div>
                <div class="card-footer bg-white">
                    <?php if ($prixRemise): ?>
                        <div class="h5">
                            <span class="badge rounded-pill text-bg-danger">
                                <strike><?= $prix ?> $</strike>
                            </span>
                        </div>
                        <div class="h5">
                            <span class="badge rounded-pill text-bg-success">Promo : <?= $prixRemise ?> $</span>
                        </div>
                    <?php else: ?>
                        <div class="h5">
                            <span class="badge rounded-pill text-bg-success"><?= $prix ?> $</span>
                        </div>
                    <?php endif; ?>
                    <div class="mt-2">
                        <?php
                        include '../include/front/counter.php';
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
?>
