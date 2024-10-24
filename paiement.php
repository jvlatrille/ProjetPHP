<?php
    // Inclusion des fichiers nécessaires 
    include 'commun.php';

    session_start(); //On démarre la session 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Peluches</title>

    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php afficherHeader();?>

    <div class="form-label mt-4">
        <form action="paiement.php">
            <label class="form-label mt-4">Veuillez rentrer vos informations</label><br>
            <label class="form-label mt-4">Nom complet</label>
            <input type="text" class="form-control" style="width: 250px">
            <div class="row mt-2">
                <div class="col-md-4">
                    <label class="form-label">Numéro de carte</label>
                    <input type="numeric" class="form-control" style="width: 60%;" maxlength=16 pattern="\d{16}" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Csv</label>
                    <input type="numeric" class="form-control" style="width: 100%" maxlength=3 pattern="\d{3}" required">
                </div>
            </div>
            <label class="form-label mt-4">Date</label>
            <?php
                // Récupérer la date actuelle
                $dateActuelle = date('Y-m');
                
                // Ajouter 3 mois à la date actuelle
                $dateMin = date('Y-m', strtotime("+3 months", strtotime($dateActuelle)));
                
                // Afficher la date après ajout de 3 mois
                echo "Date dans 3 mois : " . $dateMin;
                
                echo '<input type="month" class="form-control" style="width: 10%" min='.$dateMin.'>';
            ?>
        </form>
    </div>

    <div>
        <h3>Montant</h3>
        <p>
            <?php
            if (isset($_SESSION['total_panier'])) {
                $totalCommande = $_SESSION['total_panier'];
                
                echo "<strong>" . number_format($totalCommande, 2) . " € </strong>";
            }?>
        </p>
        <button class="btn btn-primary">Continuer</button>
        
    </div>


    <?php afficherFooter();?>

       
    
    
    
</body>
</html>
    






