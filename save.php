<?php

/**
 * Je vérifie que mes données soient bien transmises
 */
var_dump($_POST);


/**
 * Je me connecte à la base de données
 */

try {
        // Essaie de faire ce script...
        $bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8;port='.$port, $login, $password);
        return $bdd;
}
catch (Exception $e) {
    // Sinon, capture l'erreur et affiche la
    die('Erreur : ' . $e->getMessage());
}

/**
 * Je traite mes données : validations, transformations...
 */