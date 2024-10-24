<?php
include 'commun.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - Site Peluches</title>

    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php afficherHeader(); ?>

    <div class="form-label mt-4">
        <form action="paiement.php" method="post">
            <label class="form-label mt-4">Veuillez entrer vos informations</label><br>

            <label class="form-label mt-4">Nom complet</label>
            <input type="text" class="form-control" style="width: 250px" name="nomComplet" required>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label class="form-label">Numéro de carte</label>
                    <input type="text" class="form-control" style="width: 60%;" maxlength=16 pattern="\d{16}" name="numeroCarte" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">CSV</label>
                    <input type="text" class="form-control" style="width: 100%" maxlength=3 pattern="\d{3}" name="csv" required>
                </div>
            </div>

            <label class="form-label mt-4">Date d'expiration</label>
            <?php
            // Récupérer la date actuelle
            $dateActuelle = date('Y-m');

            // Ajouter 3 mois à la date actuelle
            $dateMin = date('Y-m', strtotime("+3 months", strtotime($dateActuelle)));

            // Afficher la date après ajout de 3 mois
            echo '<input type="month" class="form-control" style="width: 10%" name="dateExpiration" min="' . $dateMin . '" required>';
            ?>

            <h3>Montant</h3>
            <p>
                <?php
                if (isset($_SESSION['total_panier'])) {
                    $totalCommande = $_SESSION['total_panier'];
                    echo "<strong>" . $totalCommande . " € </strong>";
                }
                ?>
            </p>

            <?php // Vide le panier
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                unset($_SESSION['panier']);
                unset($_SESSION['total_panier']);

                header('Location: index.php');
                exit();
            }
            ?>

            <form method="post" action="paiement.php">
                <button type="submit" class="btn btn-primary">Continuer</button>
            </form>
    </div>

    <?php afficherFooter(); ?>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>