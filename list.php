
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Liste de films</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

</head>
<body>
    
    <ul>
        <li><a href="add.php">Ajouter</a></li>
    </ul>

        <?php

            $host       = 'localhost';  // Hôte de la base de données
            $dbname     = 'phpcourse';  // Nom de la bdd
            $port       = '3308';       // Ou 3308 selon la configuration
            $login      = 'root';       // Par défaut dans WAMP
            $password   = '';           // Par défaut dans WAMP (pour MAMP : 'root')

            try {
                $bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8;port='.$port, $login, $password);
            }
            catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }

            // Ma requête à la BDD
            $request = 'SELECT * FROM films';

            // Je questionne (->query()) l'instance de base de données ($bdd) avec ma requête ($request)
            $response = $bdd->query($request);

            // Array qui contiendra les données requêtées
            $films = [];

            // Tant que j'arrive à aller chercher (fetch) des lignes qui rentreront dans $donnees :
            while($donnees = $response->fetch() ) {

                // Je met ma ligne ($donnees) dans mon tableau $shoes
                $films[] = $donnees;
            }


            // Fonction de traitement de texte si besoin
            function cropText($text, $tailleMax) {
                
                if ( strlen($text) <= $tailleMax ) {
                    return $text;
                }

                else {
                    return substr($text, 0, $tailleMax-3) . '...';
                }
            }

        ?>

        <table class="table">

            <tr>
                <th>Titre</th>
                <th>Genre</th>
                <th>Durée</th>
                <th>Année de sortie</th>
                <th>Réalisateur</th>
                <th>Acteur principal</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
                <?php foreach($films as $f) { ?>
                    <tr>
                        <td><?= $f['titre']; ?></td>
                        <td><?= $f['genre']; ?></td>
                        <td><?= $f['duree']; ?></td>
                        <!-- Ici, j'utilise "date()" pour forcer l'affichage en Y (year) -->
                        <!-- J'utilise aussi strtotime() pour passer en argument à date le TIMESTAMP de la date de sortie -->
                        <td><?= date('Y', strtotime($f['date_de_sortie'])); ?></td>
                        <td><?= $f['realisateur']; ?></td>
                        <td><?= $f['acteur_principal']; ?></td>
                        <td><?= $f['note']; ?>/5</td>
                        <td>
                            <a href="show.php?film=<?= $f['id']; ?>" class="btn btn-primary">Voir</a>
                            <a href="delete.php?film=<?= $f['id']; ?>" class="btn btn-danger">Supprimer</a>
                        </td>
                    </tr>
                <?php } ?>

        </table>

</body>
</html>