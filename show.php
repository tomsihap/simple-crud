<?php

// var_dump($_GET);

$host       = 'localhost'; // Hôte de la base de données
$dbname     = 'phpcourse'; // Nom de la bdd
$port       = '3308'; // Ou 3308 selon la configuration
$login      = 'root'; // Par défaut dans WAMP
$password   = ''; // Par défaut dans WAMP (pour MAMP : 'root')

try {
    // Essaie de faire ce script...
    $bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8;port='.$port, $login, $password);
}
catch (Exception $e) {
    // Sinon, capture l'erreur et affiche la
    die('Erreur : ' . $e->getMessage());
}

$requete = "SELECT * FROM films WHERE id = " . $_GET['film'];

$res = $bdd->query($requete);

$film = $res->fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $film['titre'] ?> (<?= date('Y', strtotime($film['date_de_sortie']));?>)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

</head>
<body style="background:url('uploads/<?= $film['image'];?>') no-repeat; background-size:100%">
    
    <div class="container" style="background-color: rgba(255, 255, 255, 0.3)">
        <div class="row">
            <div class="col-12">

            <h1><?= $film['titre']; ?> (<?=date('Y', strtotime($film['date_de_sortie']));?>)</h1>
            <small>Réalisé par <?= $film['realisateur']; ?> avec <?= $film['acteur_principal']; ?> en premier rôle.</small>

            <hr>
            <blockquote class="blockquote">
                <p class="mb-0">
                    Un oeuvre de type <?= $film['genre']; ?>, d'une durée de <?= $film['duree']; ?> minutes.
                </p>
                <footer class="blockquote-footer">
                    La critique a attribué <cite title="Source Title"><?= $film['note']; ?> canards sur 5 aux Palmes d'Or de Hanoï à ce film.</cite>
                    </footer>
            </blockquote>

                <a href="list.php">Retour à la liste</a>
            </div>
        </div>
    </div>
</body>
</html>
