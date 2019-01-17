<?php

/**
 * Je vérifie que mes données soient bien transmises
 */
var_dump($_POST);

$host       = 'localhost'; // Hôte de la base de données
$dbname     = 'phpcourse'; // Nom de la bdd
$port       = '3308'; // Ou 3308 selon la configuration
$login      = 'root'; // Par défaut dans WAMP
$password   = ''; // Par défaut dans WAMP (pour MAMP : 'root')
/**
 * Je me connecte à la base de données
 */

try {
    // Essaie de faire ce script...
    $bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8;port='.$port, $login, $password);
}
catch (Exception $e) {
    // Sinon, capture l'erreur et affiche la
    die('Erreur : ' . $e->getMessage());
}

/**
 * Je traite mes données : validations, transformations...
 */

// Mes données non-null (required) sont : titre, date_de_sortie et realisateur.
// Je valide dont leur existence.

/**
 * Validation de l'existence du titre
 */
if (empty($_POST['titre'])) {
    echo "Attention, le titre ne peut pas être vide.";
}
elseif( strlen($_POST['titre']) > 50 ) {
    echo "Attention, le titre ne peut pas être plus grand que 50 caractères.";
}
else {
    $titre = $_POST['titre'];
}

/**
 * Validation de l'existence de la date de sortie
 * Validation du type de données de la date de sortie
 */
if (empty($_POST['date_de_sortie'])) {
    echo "Attention, la date de sortie ne peut pas être vide.";
}

/**
 * Pour date de sortie, je dois vérifier que la date est bien un INT valide !
 */
else if( intval($_POST['date_de_sortie']) < 1890 || intval($_POST['date_de_sortie']) > 2500 ) {
    echo "Attention, la date doit être une année valide (comprise entre 1890 et 2500).";
}

else {
    // Comme ma date de sortie existe et est bien un int valide, je le transforme en une date valide.
    // En effet, le champ dans ma BDD est un type "date".
    // Le format DATE pour MySQL est le suivant: Y-m-d (2019-01-01),
    // je complète donc mon année avec "-01-01".
    $dateDeSortie = $_POST['date_de_sortie'] . "-01-01";
}


/**
 * Validation de l'existence du réalisateur
 */
if (empty($_POST['realisateur'])) {
    echo "Attention, le realisateur ne peut pas être vide.";
}
elseif( strlen($_POST['realisateur']) > 50 ) {
    echo "Attention, le nom du réalisateur ne peut pas être supérieur à 50 caractères.";
}
else {
    $realisateur = $_POST['realisateur'];
}

/**
 * Validation des autres variables :
 * Cette fois, si la valeur est "empty", je ne retourne pas d'erreur mais une valeur "null" dans la
 * nouvelle variable créée.
 */


/**
 * Validation du genre :
 * On a un select contenant plusieurs genres, pour être valide le genre doit appartenir aux données proposées.
 * Pour cela, on fait un "in_array" pour vérifier que le genre est dans les genres proposés.
 */

$genresValides = ['horreur', 'comédie', 'humour', 'thriller'];

// Cas où le genre est vide. Cas valide.
if (empty($_POST['genre'])) {
    $genre = null;
}

// Sinon, si le genre est rempli, il doit appartenir à l'array $genresValides.
elseif( !in_array($_POST['genre'], $genresValides) ) {
    echo "Le genre n'est pas valide.";
}

// Si on n'a pas l'erreur précédente, alors le genre est valide.
else {
    $genre = $_POST['genre'];
}

/**
 * Validation de la durée
 * La durée est facultative.
 * Si la durée existe, elle doit être transformée en un INT car son champ en BDD est un SMALLINT.
 */
// Cas où la durée est vide. Cas valide.
if (empty($_POST['duree'])) {

    $duree = null;
}

// Sinon, on vérifie que le format est de type "hh:mm" avec une Regex :
elseif (!preg_match("/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/", $_POST['duree'])) {
    echo "La durée n'a pas une valeur valide.";
}
// Sinon, on a une durée et son format est valide.
else {

    // Maintenant qu'on a notre donnée valide, on va la convertir en un INT de minutes !
    // On prend les éléments AVANT ":" (les heures) et on les multiplie par 60.
    // On ajoutera ensuite les minutes (après ":").

    // substr($valeur, $debut, $fin) permet d'extraire d'une string les caractères commençant à 
    // $debut (inclus) jusqu'à $fin (non-inclus).
    // Pour l'heure de HH:MM, on veut les caractères 0 et 1 (HH).
    $hours = substr($_POST['duree'], 0, 2);

    // Pour les minutes de HH:MM, on veut les caractères 3 et 4 (MM)
    $minutes = substr($_POST['duree'], 3, 5);

    // J'ajoute mes heures*60 à mes minutes.
    // J'utilise intval() afin d'être sûr de traiter des INT et non des string.

    $duree = intval($hours)*60 + intval($minutes);
}

/**
 * Validation de l'acteur principal
 */
if (empty($_POST['acteur_principal'])) {
    $acteurPrincipal = null;
}
elseif (strlen($_POST['acteur_principal']) > 50 ) {
    echo "Attention, le nom de l'acteur est trop long (max 50 caractères).";
}
else {
    $acteurPrincipal = $_POST['acteur_principal'];
}

/**
 * Validation de la note
 * La note est facultative.
 * La note doit être un INT compris entre 0 et 5.
 */

// Cas où la note est vide. Cas valide.
if (empty($_POST['note'])) {
    $note = null;
}
// Sinon, vérifie que la note est un INT (intval)
elseif (intval($_POST['note']) <= 0) {
    echo "La note n'a pas un format valide.";
}
// Si la date est un INT, on vérifie qu'elle est dans le range valide (1 à 5)
elseif ($_POST['note'] < 1 || $_POST['note'] > 5) {
    echo "La note doit être comprise entre 1 et 5.";
}
// Sinon, on a une note valide !
else {
    $note = $_POST['note'];
}


/**
 * ENREGISTREMENT EN BDD
 */

// Si un de mes champs obligatoires est faux/null/false, alors on affiche une erreur.
// Même si ces trois variables ont déjà été testées en existence, on les teste de nouveau ici
// car on ne veut pas executer le script de BDD (dans le else) si une des 3 n'existe pas.
// A l'inverse, sans re-tester, même si on affichait un message d'erreur plus haut
// (comme "attention le titre n'existe pas"), toutes les instructions du else auraient été 
// executées (en effet sans tester, il n'y aurait pas de if/else)
if (empty($titre) || empty($dateDeSortie) || empty($realisateur) ) {
    echo "Attention, le titre, la date de sortie et le réalisateur sont obligatoires !";
}

else {

    // VERSION 1 : J'échappe mes données à la main et je remplis les $fields et $values selon si 
    // le champ a été rempli par l'utilisateur

    /* $fields = "titre, date_de_sortie, realisateur";
    $values = '"'. htmlspecialchars($titre) . '", "' . htmlspecialchars($dateDeSortie) . '", "' . htmlspecialchars($realisateur) . '"';

    if ($genre) { 
        $fields .= ", genre";
        $values .= ', "' . htmlspecialchars($genre) . '"';

    }
    if ($duree) { 
        $fields .= ", duree";
        $values .= ', "' . htmlspecialchars($duree) . '"';
    }
    if ($note) { 
        $fields .= ", note";
        $values .= ', "' . htmlspecialchars($note) . '"';
    }
    if ($acteurPrincipal) { 
        $fields .= ", acteur_principal";
        $values .= ', "' . htmlspecialchars($acteurPrincipal) . '"';
    }

    $req = 'INSERT INTO films('.$fields.') VALUES ('.$values.')';

    $bdd->query($req); */


    // VERSION 2 : On utilise un prepare/execute pour échapper les variables.
    // On utilise nos variables créées plutôt que les $_POST car 1/ elles ont passé les validations ci-dessus
    // et 2/ si elles n'ont pas été renseignées par l'utilisateur... elles sont égales à null !

    $req = "INSERT INTO films(titre, genre, duree, date_de_sortie, realisateur, acteur_principal, note, code)
            VALUES(:titre, :genre, :duree, :date_de_sortie, :realisateur, :acteur_principal, :note)";

    $res = $bdd->prepare($req);

    $res->execute([
        'titre' => $titre,
        'genre' => $genre,
        'duree' => $duree,
        'date_de_sortie' => $dateDeSortie,
        'realisateur' => $realisateur,
        'acteur_principal' => $acteurPrincipal,
        'note' => $note
    ]);

    // Eventuellement, j'affiche la dernière erreur SQL
    var_dump( $res->errorInfo()  );
}

