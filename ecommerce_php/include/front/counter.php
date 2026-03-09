<div>
    <?php
    $idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;
    $qty = $_SESSION['panier'][$idUtilisateur][$idProduit] ?? 0;
    $button = $qty == 0 ? '<i class="fa fa-light fa-cart-plus"></i>' : '<i class="fa-solid fa-pencil"></i>';
    ?>
    <?php if ($idUtilisateur !== 0): ?>
        <form method="post" class="counter d-flex align-items-center" action="ajouter_panier.php">
            <button type="button" class="btn btn-secondary mx-2 counter-moins" onclick="decrementQuantity(this)">-</button>

            <input type="hidden" name="id" value="<?= htmlspecialchars($idProduit) ?>">
            <input class="form-control w-25 text-center" type="number" name="qty" id="qty" value="<?= $qty ?>" max="99" min="0" required>

            <button type="button" class="btn btn-secondary mx-2 counter-plus" onclick="incrementQuantity(this)">+</button>

            <button class="btn btn-success btn-sm mx-2" type="submit" name="ajouter">
                <?= $button ?>
            </button>

            <?php if ($qty != 0): ?>
                <button formaction="supprimer_panier.php" class="btn btn-danger btn-sm mx-1" type="submit" name="supprimer">
                    <i class="fa-solid fa-trash"></i>
                </button>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            Vous devez être connecté pour acheter ce produit. 
            <strong><a href="../connexion.php" class="alert-link">Connexion</a></strong>
        </div>
    <?php endif; ?>
</div>

<script>
    function decrementQuantity(button) {
        const qtyInput = button.nextElementSibling; 
        const currentValue = parseInt(qtyInput.value, 10) || 0;

        if (currentValue > 0) {
            qtyInput.value = currentValue - 1; 
        }
    }

    function incrementQuantity(button) {
        const qtyInput = button.previousElementSibling; 
        const currentValue = parseInt(qtyInput.value, 10) || 0;

        if (currentValue < 99) {
            qtyInput.value = currentValue + 1; 
        }
    }
</script>
