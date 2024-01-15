<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste Interventions</title>

    <style>
        body {
        /* Ajoute de l'espace en haut de la page */
        padding-top: 20px;
        }
        #lstCasernes {
            width: 350px;
            margin-bottom: 20px;  /* Ajoute de l'espace en dessous de la liste */
        }
        #interventionsTable {
            margin-bottom: 20px;  /* Ajoute de l'espace en dessous du tableau */
        }
        #ajouterIntervention {
            display: block;  /* Fait en sorte que le bouton prenne toute la largeur de son conteneur */
            width: 100%;  /* Fait en sorte que le bouton prenne toute la largeur de son conteneur */
            text-align: center;  /* Centre le texte du bouton */
        }
    </style>
</head>
<body>

<style>
    #listeInterventions {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
</style>

        <?php

            if (isset($_POST['boutonFin']))
            {
                //$ajoutHeureFin = $pdo->modifHeureDeFin();
            }

            $lstIntervention = $pdo->interventions();

            if (!empty($lstIntervention))
            {
                echo'<table id="listeInterventions">';
                echo '<caption>Liste des interventions en cours</caption>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID de la Caserne</th>';
                echo '<th>Nom de la caserne</th>';
                echo '<th>N° de l\'intervention</th>';
                echo '<th>Lieu</th>';
                echo '<th>Description</th>';
                echo '<th>Début de l\'intervention</th>';
                echo '<th>Fin de l\'intervention</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                date_default_timezone_set('Europe/Paris');
                foreach ($lstIntervention as $intervention)
                {
                    echo '<tr>';
                    echo '<td>' . $intervention['iCis'] . '</td>';
                    echo '<td>' . $intervention['cNom'] . '</td>';
                    echo '<td>' . $intervention['iId'] . '</td>';
                    echo '<td>' . $intervention['iLieu'] . '</td>';

                    echo '<td>' . $intervention['iDescription'] . '</td>';
                    echo '<td>' . $intervention['iDate'] . '</td>';
                    // echo '<td> <button data-idIntervention="'.$intervention['iId'].'" data-idCaserne="'.$intervention['iCis'].'" data-iHeureDeFin="'.$intervention['iHeureFin'].'" id="boutonFin">Fin</button> </td>';

                    if ($intervention['iHeureFin'] !== null) {
                        echo '<td> <button data-idIntervention="'.$intervention['iId'].'" data-idCaserne="'.$intervention['iCis'].'" data-iHeureDeFin="1" id="boutonFin">Fin</button> </td>';
                    } else {
                        echo '<td> <button data-idIntervention="'.$intervention['iId'].'" data-idCaserne="'.$intervention['iCis'].'" data-iHeureDeFin="0" id="boutonFin">Fin</button> </td>';
                    }
                    
                    echo '</tr>'; 
                }

                echo '</tbody>';
                echo '</table>';
            }

            else
            {
                echo '<p>Aucune intervention en cours.</p>'; 
            }

        ?>


    <button size=30 style="text-align: center;"> <a href="index.php?choixTraitement=interventions&action=voir" title="interventions">AJOUTER UNE INTERVENTION</a></button>

    <script>
      document.addEventListener('DOMContentLoaded', function()
{
    // Obtenez tous les boutons avec l'id 'boutonFin'
    var buttons = document.querySelectorAll('#boutonFin');
    
    buttons.forEach(function(button) {
        var iHeureDeFin = button.getAttribute('data-iHeureDeFin');
        if (iHeureDeFin == "1")
        {
            button.disabled = true;
        }
        else
        {
            button.addEventListener('click', function()
            {
                var idCaserne = button.getAttribute('data-idCaserne');
                var idIntervention  =  button.getAttribute('data-idIntervention');
                console.log("l'id caserne est "+idCaserne+" et l'id intervention est "+idIntervention)

                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'controleurs/c_interventions.php?action=finIntervention&idIntervention=' + idIntervention + "&idCaserne=" + idCaserne);
                xhr.onload = function() {
                    if (xhr.status === 200)
                    {
                        // On grise le bouton pour indiquer que l'intervention est terminée
                        button.disabled = true;
                    }
                };
                xhr.send();
            });
        }
    });
});



    </script>
</body>
</html>