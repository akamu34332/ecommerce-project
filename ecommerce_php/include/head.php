<?php

$isAdmin = basename($_SERVER['PHP_SELF']) === 'admin.php';

?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>E-commerce<?= $isAdmin ? ' - Administration' : '' ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha384-dyB6X8lnht3T0CfomDUNfy8sfn8kzefy5Kn5WpLa/1rRxF1RaWIlU5f3ldEVoF/" crossorigin="anonymous">
<link href="/ecommerce_php/assets/css/produit.css" rel="stylesheet">

<link rel="apple-touch-icon" sizes="180x180" href="/ecommerce_php/assets/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/ecommerce_php/assets/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/ecommerce_php/assets/favicon/favicon-16x16.png">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

<?php if (!$isAdmin): ?>
<script src="/ecommerce_php/assets/js/produit/counter.js" defer></script>
<?php endif; ?>
