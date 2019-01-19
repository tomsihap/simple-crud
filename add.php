<?php

$film = null;

if (!empty($_GET['film'])) {
    // On a un $_GET['id'] ! On essaie donc d'éditer un élément.
    // On va récupérer l'élément à éditer. Même code que pour show.php !
    
    // Bien sûr, en "prod", on n'hésite pas à mettre des validations de $_GET, éventuellement des tests d'authentification...

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

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ajout de films</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

</head>
<body>

    <form action="save.php" method="post" enctype="multipart/form-data">

        <?php if ($film) {?>
            <input type="hidden" name="id_edit" value="<?= $film['id']; ?>">
        <?php } ?>

        <label for="filmTitle">Titre du film</label>
        <input class="form-control" type="text" name="titre" id="filmTitle" value="<?= ($film) ? $film['titre'] : null; ?>" required>

        <label for="filmGenre">Genre</label>
        <select class="form-control" name="genre" id="filmGenre">
            <option value="horreur" <?= ($film) ? $film['genre'] ==  'horreur' ? 'selected' : '' : ''; ?> >Horreur</option>
            <option value="comédie" <?= ($film) ? $film['genre'] ==  'comédie' ? 'selected' : '' : ''; ?> >Comédie</option>
            <option value="humour" <?= ($film) ? $film['genre'] ==  'humour' ? 'selected' : '' : ''; ?> >Humour</option>
            <option value="thriller" <?= ($film) ? $film['genre'] ==  'thriller' ? 'selected' : '' : ''; ?> >Thriller</option>
        </select>

        <label for="filmDuree">Durée</label>
        <input class="form-control" type="time" name="duree" id="filmDuree" value="<?= ($film) ? $film['duree'] : null; ?>">

        <label for="filmDate">Année de sortie</label>
        <input class="form-control" type="number" name="date_de_sortie" id="filmDate" min="1890" max="2500" value="<?= ($film) ? $film['date_de_sortie'] : null; ?>" required>

        <label for="filmReal">Réalisateur</label>
        <input class="form-control" type="text" name="realisateur" id="filmReal" value="<?= ($film) ? $film['realisateur'] : null; ?>" required>

        <label for="filmActeurPrincipal">Acteur principal</label>
        <input class="form-control" type="text" name="acteur_principal" id="filmActeurPrincipal" value="<?= ($film) ? $film['acteur_principal'] : null; ?>">

        <label for="filmNote">Note</label>
        <input class="form-control" type="number" name="note" id="filmNote" min="1" max="5" value="<?= ($film) ? $film['note'] : null; ?>">

        <label for="filmImage">Affiche</label>
        <input type="file" name="affiche" id="filmImage" class="form-control">

        <button class="btn btn-success" type="submit">Envoyer</button>
    </form>
    
</body>
</html>