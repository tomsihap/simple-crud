<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

</head>
<body>

    <form action="save.php" method="post">
        <label for="filmTitle">Titre du film</label>
        <input class="form-control" type="text" name="titre" id="filmTitle" required>

        <label for="filmGenre">Genre</label>
        <select class="form-control" name="genre" id="filmGenre">
            <option value="horreur">Horreur</option>
            <option value="comédie">Comédie</option>
            <option value="humour">Humour</option>
            <option value="thriller">Thriller</option>
        </select>

        <label for="filmDuree">Durée</label>
        <input class="form-control" type="time" name="duree" id="filmDuree">

        <label for="filmDate">Année de sortie</label>
        <input class="form-control" type="number" name="date_de_sortie" id="filmDate" min="1890" max="2500" required>

        <label for="filmReal">Réalisateur</label>
        <input class="form-control" type="text" name="realisateur" id="filmReal" required>

        <label for="filmActeurPrincipal">Acteur principal</label>
        <input class="form-control" type="text" name="acteur_principal" id="filmActeurPrincipal">

        <label for="filmNote">Note</label>
        <input class="form-control" type="number" name="note" id="filmNote" min="1" max="5">

        <button class="btn btn-success" type="submit">Envoyer</button>
    </form>
    
</body>
</html>