

# simple-crud
Réalisation d'un CRUD basique pour une application PHP.

La structure est la suivante :
- `add.php` Formulaire d'ajout et édition d'un élément
- `save.php` Traitement du formulaire
- `list.php` Affichage de tous les éléments
- `show.php` Affichage d'un élément
- `delete.php` Traitement de la suppression d'un élément

## Sommaire
- [Base de données](#base-de-donn%C3%A9es)
- [C : Create](#c--create)
- [INSERT: Traitement des fichiers](#insert-traitement-des-fichiers)
- [R : Read](#r--read)
- [Browse](#browse)
- [D : Delete](#d--delete)
- [U : Update](#u--update)

## Base de données
On créée la base de données dans PHPMyAdmin ou Mysql Workbench.
> **N'oubliez pas d'ajouter un ID !** Même si on vous propose une liste de champs, vérifiez qu'il existe un champ unique. Si ça n'est pas le cas ou s'il y a un doute (le champ "film" paraît unique, pourtant deux films risquent d'avoir le même titre...), alors vous créez un champ ID. Dans tous les cas, le champ unique choisi devra être une **PRIMARY KEY**. Si c'est un ID, pensez à **auto-incrémenter** ce champ.

## C : Create
### Front

Je créée mon formulaire dans une page add.php de cette façon :

```html
<!-- action : la page qui traitera mon formulaire -->
<!-- method : la méthode HTTP de transmission des données -->
<form action="save.php" method="post">

        <!-- Pour chaque champ nécessaire, je créée un label/input cohérent !-->
        <!-- Obligatoire: mettre un "name" pour chaque input. Idéalement, mettez comme "name" le même nom que son champ dans la BDD ! -->
        <label for="filmTitle">Titre du film</label>
        <input type="text" name="titre" id="filmTitle" required>

        ...

        <!-- Je n'oublie pas mon bouton type="submit" -->
        <button type="submit">Envoyer</button>
    </form>
```

> N'oubliez pas d'être cohérent par rapport au type de données! Utilisez des éléments appropriés (select, radio, checkbox...), des types aux inputs (number, password, email...) et des validations (ranges, min/max). Utilisez "required" lorsque nécessaire (cas non-null), des "value" pré-remplis si besoin, etc !

### Back

L'action de mon formulaire dans add.php pointe vers save.php. Ce sera la *logique* de mon formulaire, le back-end.

#### Récupération des données de formulaire
Mon formulaire (add.php) envoie à save.php les données via la méthode POST.
Je peux retrouver les données utilisateur dans `$_POST`.

> Pensez à débuguer $_POST ! Cela vous permet de vérifier que toutes les données du formulaire sont bien transmises et que les noms des champs (input name) soient corrects.
> Pour débuguer : `var_dump($_POST);`

#### Connexion à la BDD
J'ajoute une connexion à la base de données. Je ferai mes requêtes dans un bloc "try-catch" afin de récupérer les erreurs SQL éventuelles.

```php
$host       = 'localhost'; // Hôte de la base de données
$dbname     = 'nom_de_la_db'; // Nom de la bdd
$port       = '3306'; // Ou 3308 selon la configuration
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
```

#### Traitement et validation des données
Avant d'enregistrer en base de données, je valide mes données (qu'elles soient toutes correctes selon mon modèle de données) et je fais des traitements éventuels. C'est le but de la partie back-end ! Travailler les données avant de communiquer avec la base de données.

Même si mes données sont "validées" avec le HTML, on ne peut pas faire confiance aux validations du HTML : on doit tout de même valider en PHP. 

```php
if (empty($_POST['ma_variable'])) {
    echo "Attention, la variable ne peut pas être vide.";
}
elseif( /* VALIDATIONS SUR LA VARIABLE EN QUESTION */ ) {
    echo "Une erreur relative à cette validation";
    
}
elseif (/* D'autres validations si besoin...*/) {
    echo "D'autres erreurs propres aux autres validations...";
}
else {
    // Enfin, le cas où la variable passe tous les tests :
    // si ma variable est bonne, je met $_POST['ma_variable'] dans une nouvelle variable.
    $maVar = $_POST['ma_variable'];
}
```

> On met dans une nouvelle variable $maVar pour des tests plus tard : en effet, si $maVar existe, c'est que j'ai passé tous les tests propres à cette donnée !

> Pensez à rajouter des validations pour les VARCHAR ! ( strlen(...) )

#### Enregistrement des données

##### On vérifie nos données obligatoires
```php

// Si un de mes champs obligatoires est faux/null/false, alors on affiche une erreur.
// Même si ces trois variables ont déjà été testées en existence, on les teste de nouveau ici
// car on ne veut pas executer le script de BDD (dans le else) si une des 3 n'existe pas !

// A l'inverse, si je te testais pas de nouveau dans le if, et même si on affichait un message d'erreur plus haut (comme "attention le titre n'existe pas"), toutes les instructions du else auraient été executées (en effet sans tester, il n'y aurait pas de if/else, donc les instructions s'executent toutes seules).

if (empty($titre) || empty($dateDeSortie) || empty($realisateur) ) {
    echo "Attention, le titre, la date de sortie et le réalisateur sont obligatoires !";
}
```

##### Sinon (else => cas où les données existent), on enregistre en BDD

```php
else {
    // On utilise nos variables créées plutôt que les $_POST car
    // 1/ elles ont passé les validations ci-dessus
    // 2/ si elles n'ont pas été renseignées par l'utilisateur... elles existent et sont égales à null !
    // Ca me permet donc d'avoir une requête INSERT INTO qui contient systématiquement tous les champs dans table(champ1, champ2, ...), que l'utilisateur ait mis des données ou non.

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

    // Eventuellement, j'affiche la dernière erreur SQL si l'enregistrement ne s'effectue pas.
    var_dump( $res->errorInfo() );
}
```

## INSERT: Traitement des fichiers

Les fichiers dans les formulaires sont transmis par la variable `$_FILES`.

### Création du dossier Uploads

On créée à la racine du projet un dossier `/uploads` qui contiendra les fichiers envoyés par les utilisateurs.


### Modification du formulaire

Pour traiter les fichiers dans les formulaires, il est **obligatoire** de modifier le formulaire comme suit en ajoutant l'attribut `enctype="multipart/form-data"` :

```html
<form action="save.php" method="post" enctype="multipart/form-data">
    ...
</form>
```

### Input file
On ajoute au formulaire un input de type "file" en n'oubliant pas l'attribut "name" :

```html
...
    <label for="elementImage">Photo</label>
    <input type="file" name="photoElement" id="elementImage">  
```

### Récupération des données dans save.php

#### $_FILES

La variable $_FILES contient tous les fichiers uploadés via le formulaire :

```
array (size=1)
  'photoElement' => 
    array (size=5)
      'name' => string 'shoe04.jpg' (length=10)
      'type' => string 'image/jpeg' (length=10)
      'tmp_name' => string 'C:\wamp64\tmp\phpCCC7.tmp' (length=25)
      'error' => int 0
      'size' => int 3935
```

On a accès aux éléments suivants :

```php
$_FILES['photoElement']; // On utilise le "name" issu du formulaire
$_FILES['photoElement']['name']; // Contient le nom du fichier envoyé par le visiteur.
$_FILES['photoElement']['type']; // Indique le type du fichier envoyé. Si c'est une image gif par exemple, le type sera image/gif.
$_FILES['photoElement']['tmp_name']; // Juste après l'envoi, le fichier est placé dans un répertoire temporaire sur le serveur en attendant que votre script PHP décide si oui ou non il accepte de le stocker pour de bon. Cette variable contient l'emplacement temporaire du fichier.
$_FILES['photoElement']['error']; // Contient un code d'erreur permettant de savoir si l'envoi s'est bien effectué ou s'il y a eu un problème et si oui, lequel. La variable vaut 0 s'il n'y a pas eu d'erreur.
$_FILES['photoElement']['size']; // Indique la taille du fichier envoyé. Attention : cette taille est en octets. Il faut environ 1 000 octets pour faire 1 Ko, et 1 000 000 d'octets pour faire 1 Mo. Attention : la taille de l'envoi est limitée par PHP. Par défaut, impossible d'uploader des fichiers de plus de 8 Mo.
```

#### pathinfo()

On a aussi accès à la décomposition du nom du fichier avec ` pathinfo($_FILES['photoElement']['name'])`:

```
array (size=4)
  'dirname' => string '.' (length=1)
  'basename' => string 'shoe04.jpg' (length=10)
  'extension' => string 'jpg' (length=3)
  'filename' => string 'shoe04' (length=6)
```

> **Attention :** pathinfo() prend en argument le "name" du fichier envoyé ! Soit : `$_FILES['photoElement']['name']` et pas que `$_FILES['photoElement']`

On a accès aux éléments suivants :
```php
pathinfo($_FILES['photoElement']['name'])['dirname']; // Dossier dans lequel se trouve le fichier
pathinfo($_FILES['photoElement']['name'])['basename']; // nom complet du fichier
pathinfo($_FILES['photoElement']['name'])['extension']; // Extension du fichier
pathinfo($_FILES['photoElement']['name'])['filename']; // Nom du fichier sans extension
```

Comme il est difficile d'utiliser des noms de variables et fonctions aussi longs, on va utiliser des variables intermédiaires :

```php

// Je créée une variable pour le nom du fichier, qui est l'argument de pathinfo() :

$fileName = $_FILES['photoElement']['name'];

// Je peux donc utiliser pathinfo() comme suit : pathinfo($fileName);
// Comme je veux utiliser des clés dans le tableau que me retourne pathinfo(), je créée une autre variable intermédiaire :

$pathInfo = pathinfo($fileName);


// J'ai donc accès aux mêmes variables que ci-dessus de cette façon :

$pathInfo['dirname'];
$pathInfo['basename'];
$pathInfo['extension'];
$pathInfo['filename'];

```

### Validations de l'input

Comme tous les autres champs de formulaire, j'utilise le même bloc pour valider les données :

```php

/**
 * 1. VARIABLES INTERMEDIAIRES
 * Si j'ai besoin de variables pour les validations
 */

// Je liste les extensions autorisées
$extensionsAutorisees = ['jpg', 'jpeg', 'gif', 'png'];

/**
 * 2. TEST D'EXISTENCE
 * Je vérifie que ma variable existe dans le formulaire.
 * Comme c'est un champ non obligatoire, si elle n'existe pas, je 
 * retourne $image = null plutôt qu'une erreur.
 */

// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur.
// J'utile le NOM du fichier (name) et non le champ lui même ! En effet, le champ existe toujours, le nom n'existe que si on a une image.
if (empty($_FILES['imageChaussure']['name'])) {
    $image = null;
}

/**
 * 3. VALIDATIONS
 * J'effectue mes différentes validations sur mon input. C'est un input type=file,
 * il y a des validations propres aux fichiers à effectuer :
 */

// Est-ce qu'il y a eu une erreur lors de l'upload ?
elseif($_FILES['imageChaussure']['error'] !== 0) {
    echo "Attention, erreur lors de l'upload de l'image.";
}

// Testons si le fichier n'est pas trop gros
elseif ($_FILES['imageChaussure']['size'] >= 800000) {
    echo "Attention, l'image est trop grosse.";
}


/**
 * Testons si l'extension est autorisée
 * Rappel de ci-dessus, pour accéder à l'extension, j'utilise pathinfo() avec en argument
 * le nom de l'image : $_FILES['imageChaussure']['name'].
 * 
 * Mon pathinfo est donc : pathinfo($_FILES['imageChaussure']['name']).
 * 
 * J'ai besoin de l'extension du pathinfo ( ['extension'] ), j'y accède avec :
 * pathinfo($_FILES['imageChaussure']['name'])['extension']
 * 
 * !!! Attention aux parenthèses et crochets !
 */

elseif (!in_array( pathinfo($_FILES['imageChaussure']['name'])['extension'], $extensionsAutorisees) ) {
    echo "Attention, le fichier n'est pas autorisé.";
}

/**
 * 4. ENREGISTREMENT
 * Enfin, si je passe toutes les validations, au lieu d'avoir un $image = null comme au début,
 * je met ma donnée dans $image.
 */
else {

    // Pour ne pas avoir 2 fichiers identiques en noms, je créée un nom aléatoire que je peux préfixer si besoin.
    $nomAleatoire = "shoe_" . uniqid();

    // Pour rappel, l'extension est accessible avec pathinfo($_FILES['imageChaussure']['name'])['extension'].
    $extension = pathinfo($_FILES['imageChaussure']['name'])['extension'];

    // Le nom de mon image est : nom.extension
    $image = $nomAleatoire . "." . $extension;

    // Enfin, je déplace l'image de son emplacement temporaire ($_FILES['imageChaussure']['tmp_name'])
    // vers le dossier uploads que j'ai créé, avec le nouveau nom ($nomAleatoire).
    move_uploaded_file($_FILES['imageChaussure']['tmp_name'], 'uploads/' . $image );
}

```

---

## R : Read

### Templating
- Créer une base de template HTML
- Eventuellement mettre un CSS Bootstrap pour le visuel
- Comme on n'a pas de structure MVC, on mettra la logique dans le même fichier qui affichera ma liste (la vue : list.php)
> Cela veut dire que je n'importerai pas de fichier "controller", "helper", mais j'appelerai directement ma base de données et effectuerai mes requêtes dans le même fichier qui affichera les résultats.

### Logique
- => J'appelle ma BDD avant d'afficher le tableau
```php
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
```

- Je créée ma requête SQL (select * ...)
- J'execute la demande (query) auprès de la base de données ($bdd)
- Je créée un tableau intermédiaire qui contiendra mes résultats ($shoes)
- Tant que j'ai des lignes qui viennent de la bdd (while $donnees = $res->fetch() ), je les ajoute à mon tableau intermédiaire ($shoes[] = $donnees)
- Je fais un var_dump à l'issue du while pour vérifier que les données soient bien dans $shoes

### Restitution des résultats

- Je dessine en HTML mon rendu de résultats avec des données "en dur"
```html
<ul>
    <li>Marque - modèle</li>
    <li>Marque - modèle</li>
    <li>Marque - modèle</li>
    <li>Marque - modèle</li>
    <li>Marque - modèle</li>
    <li>Marque - modèle</li>
</ul>
```

- Une fois satisfait, je dessine mon HTML pour un cas unique (ici : 1 seul li) :
```html
<ul>
    <li>Marque - modèle</li>
</ul>
```

- Je trouve l'élément répétable, c'est à dire le HTML qui sera répété à chaque élément de mon array :
```html
<li>Marque - modèle</li>
```

- Une fois l'élément répétable trouvé, c'est lui que je mettrai dans ma boucle foreach ( = "pour chaque élément du tableau, je répète ce html") :
```php
<ul>
    <?php foreach($array as $a) { ?>
        <li><?= $a['marque']; ?> - <?= $a['modele']; ?></li>
    <?php } ?>
</ul>
```

### Ajout des liens
Pour accéder à la fiche et au lien "supprimer" d'un élément, j'ajoute des liens construits de cette façon :

```php
<a href="list.php?element=<?= $a['id']; ?>"> <?= $a['nom']; ?></a>
```

Je créée donc une variable `$_GET` nommée `element` prenant la valeur `$a['id']`.

> Je pourrai accéder à cette variable, depuis `list.php`, avec `$_GET['element']`.

---

## Browse

### Base de données
Je me connecte à la base de données dans le fichier fiche.php :

```php
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
```

Je rédige ma requête dans une variable pour récupérer l'élément choisi (SELECT * FROM table WHERE id = mon_id). Pour récupérer l'élément choisi, j'utilise la superglobale `$_GET` qui contient les données passées par l'URL (dans la page list.php, j'ai en effet appelé fiche.php?id=*** ).

```php
$requete = "SELECT * FROM shoes WHERE id = " . $_GET['id'];
```

Ensuite, je demande (->query()) ma requête ($requete) à la base de données ($bdd) et je récupère (->fetch()) le résultat de la requête SQL ($reponse) dans une variable ($element):

```php
$reponse = $bdd->query($requete);
$element = $reponse->fetch();
```

> Comme je sais que je n'aurai qu'une seule ligne de retournée (en effet, je fais un WHERE avec une clé primaire, ce qui m'assure de n'avoir qu'un résultat), je ne vais pas utiliser de while(...), mais je récupère la seule ligne directement dans une variable $element.

### Affichage des données

Maintenant que j'ai récupéré l'élément dans ma base de données et l'ai enregistré dans une variable `$element`, je peux l'utiliser pour accéder à ses valeurs :

#### Construction du HTML
On construit un template en HTML qui affichera notre élément.
```html
...
<ul>
    <li>Marque : </li>
    <li>Modèle : </li>
    <li>Taille : </li>
</ul>
...
``` 


#### Remplissage des données 

```php
...
<ul>
    <li>Marque : <?= $element['marque']; ?></li>
    <li>Modèle : <?= $element['modele']; ?></li>
    <li>Taille : <?= $element['taille']; ?></li>
</ul>
...
``` 

---

## D : Delete

J'ai passé en variable $_GET à la page delete.php (ex: `delete.php?element=5`) un ID d'élément à supprimer.

### Base de données
Je me connecte à la base de données dans le fichier delete.php :

```php
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
```

Comme il s'agit d'une donnée sensible, j'effectue des validations comme je le fais pour un INSERT :

```php
if (empty($_GET['element'])) {
    echo "Attention, il faut fournir un ID d'élément à supprimer.";
}

elseif (intval($_GET['element']) <= 0 ) {
    echo "Attention, l'ID doit être un entier valide.";
}
```

Une fois toutes les validations effectuées, dans le cas `else` (cas où toutes les validations sont passées), je fais ma requête de suppression de données :

```php

else {
    echo "L'id est correct, on supprime l'élément !<br>";

    // Je met ma requête dans une variable
    $requete = "DELETE FROM table WHERE id = " . $_GET['element'];

    // J'execute immédiatement la requête sans garder le résultat dans une variable.
    // En effet, comme ce n'est pas un SELECT, je n'ai pas de résultat retourné.
    // Donc, pas besoin de "$req = $bdd->query()", je fais directement "$bdd->query()".
    $bdd->query($requete);

    // J'ajoute un lien de retour vers ma liste.
    echo "<a href='list.php'>Retour</a>";
}

```

Je rédige ma requête dans une variable pour récupérer l'élément choisi (SELECT * FROM table WHERE id = mon_id). Pour récupérer l'élément choisi, j'utilise la superglobale `$_GET` qui contient les données passées par l'URL (dans la page list.php, j'ai en effet appelé fiche.php?id=*** ).

```php
$requete = "SELECT * FROM shoes WHERE id = " . $_GET['id'];
```

Ensuite, je demande (->query()) ma requête ($requete) à la base de données ($bdd) et je récupère (->fetch()) le résultat de la requête SQL ($reponse) dans une variable ($element):

```php
$reponse = $bdd->query($requete);
$element = $reponse->fetch();
```

> Comme je sais que je n'aurai qu'une seule ligne de retournée (en effet, je fais un WHERE avec une clé primaire, ce qui m'assure de n'avoir qu'un résultat), je ne vais pas utiliser de while(...), mais je récupère la seule ligne directement dans une variable $element.

### Affichage des données

Maintenant que j'ai récupéré l'élément dans ma base de données et l'ai enregistré dans une variable `$element`, je peux l'utiliser pour accéder à ses valeurs :

#### Construction du HTML
On construit un template en HTML qui affichera notre élément.
```html
...
<ul>
    <li>Marque : </li>
    <li>Modèle : </li>
    <li>Taille : </li>
</ul>
...
``` 


#### Remplissage des données 

```php
...
<ul>
    <li>Marque : <?= $element['marque']; ?></li>
    <li>Modèle : <?= $element['modele']; ?></li>
    <li>Taille : <?= $element['taille']; ?></li>
</ul>
...
``` 

## U : Update
Pour faire la partie Update, nous allons :
1. Créer un lien dans la page list.php vers add.php?id_element=5 par exemple
2. Modifier la page add.php, la page d'ajout d'un élément : si on a reçu un $_GET['id_element'], alors on est en mode édition et plus en mode création. Cela nous permet d'avoir 1 formulaire pour 2 actions (DRY : Dont Repeat Yourself).
3. Dans ce cas, on va faire un SELECT pour récupérer l'élément à éditer
4. On remplit les values par les valeurs de l'élément à éditer
5. On ajoute un champ input:hidden qui contiendra l'ID de l'élément édité, si et seulement si on a un élément à éditer (sinon, on est juste sur add.php, on n'a aucun ID à mettre dedans ;) )
6. On modifiera save.php : si on a reçu un $_POST['id_edition'], on fera une requête UPDATE plutôt qu'une requête INSERT INTO.


### Créer un lien dans list.php
Dans le tableau, on ajoute une action "Editer" qui pointe vers la page du formulaire add.php :
```php
...
    <a href="add.php?film=<?= $f['id']; ?>" class="btn btn-warning">Editer</a>
...
```

### Vérification dans add.php

#### Ajout d'un $film = null
Par défaut dans add.php, on aura une variable $film nulle :
```php
// add.php
$film = null;
...
```

Cela nous permettra d'avoir une variable à tester plus tard : si elle est nulle, c'est qu'on n'a pas de film à éditer.

#### Est-ce qu'il y a une variable $_GET['film'] ?

On vérifie si on a une variable $_GET['film'] : si oui, on vient d'un lien d'édition, sinon on vient du lien "Ajouter".

```php
// add.php
...
if (!empty($_GET['film'])) {
  
}
```
Pas besoin de else ici : en effet, si on a un film on va le chercher, sinon... on n'a rien à faire.

#### On va récupérer le film

```php
// add.php
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
```


Pour chacun des inputs du formulaire, on va ajouter une value si on a un film récupéré, grâce à un ternaire `<?= ($film) ? $film['titre'] : null; ?>` :
```php
<input class="form-control" type="text" name="titre" id="filmTitle" required value="<?= ($film) ? $film['titre'] : null; ?>">
```

Pour les select, c'est un peu différent : on ne doit pas remplir une value mais ajouter l'attribut "selected" sur le champ correspondant :

```php
<option value="horreur" <?= ($film) ? ($film['genre'] ==  'horreur' ? 'selected' : '') : ''; ?> >Horreur</option>
```

Voici comment on lit cette double ternaire : 
```
- Est-ce que $film existe ?
- - SI OUI :
- - - Est-ce que le genre est "horreur" ?
- - - - SI OUI : 
- - - - - On affiche 'selected'
- - - - SI NON :
- - - - - On n'affiche rien ''
- - SI NON :
- - - On n'affiche rien ''
```

Pour les champs File, c'est aussi différent : ils ne peuvent pas avoir une valeur par défaut, on ne modifie rien. Par contre, dans save.php, on testera si l'utilisateur a uploadé une nouvelle photo.

#### Champ input:hidden
Enfin, très important : on va ajouter un champ input:hidden uniquement si on est en édition. Cela nous permettra d'envoyer l'information à save.php qu'on est en mode UPDATE et plus en mode INSERT INTO. On ajoute, n'importe où dans le formulaire :

```php
// add.php
...
<?php if ($film) {?>
            <input type="hidden" name="id_edit" value="<?= $film['id']; ?>">
<?php } ?>
...
```

### Changements dans save.php
Dans la logique du formulaire, save.php, on va maintenant tester si on a reçu un $_POST['id_edit'] (ajouté au point précédent). Comme les validations des autres champs ne changent pas, c'est seulement la partie BDD que l'on change.

On fera aussi la validation de s'il y a une image ou non :


```php
// save.php
...

// Si on n'a pas de id_edit, alors on continue avec la requête d'origine :
if (empty($_POST['id_edit'])) {
    $req = "INSERT INTO films(titre, genre, duree, date_de_sortie, realisateur, acteur_principal, note, image)
            VALUES(:titre, :genre, :duree, :date_de_sortie, :realisateur, :acteur_principal, :note, :image)";

    $res = $bdd->prepare($req);

    $res->execute([
        'titre' => $titre,
        'genre' => $genre,
        'duree' => $duree,
        'date_de_sortie' => $dateDeSortie,
        'realisateur' => $realisateur,
        'acteur_principal' => $acteurPrincipal,
        'note' => $note,
        'image' => $image
    ]);

}

// Sinon, on va valider $_POST['id_edit'] comme n'importe quel champ
else {

    // Validation...
    if (intval($_POST['id_edit']) <= 0) {
        echo "L'ID de l'élément édité n'est pas valide.";
    }

    // Sinon, c'est OK on continue :
    else {
        $idEdit = $_POST['id_edit'];

        // On vérifie ICI que si l'on a reçu une image : si dans les validateurs au dessus on n'a pas eu d'image, on fait la requête sans le champ image :
        if ($image !== null) {
            $req = "UPDATE films 
                    SET(titre = :titre, genre = :genre, duree = :duree, date_de_sortie = :date_de_sortie, realisateur = :realisateur, acteur_principal = :acteur_principal, note = :note)
                    WHERE id = :id"; // IMPORTANT, on n'oublie pas le WHERE lors d'un UPDATE ou d'un DELETE !!!

                $res = $bdd->prepare($req);

                $res->execute([
                    'id'    => $idEdit, // On ajoute $idEdit à l'execute du UPDATE !
                    'titre' => $titre,
                    'genre' => $genre,
                    'duree' => $duree,
                    'date_de_sortie' => $dateDeSortie,
                    'realisateur' => $realisateur,
                    'acteur_principal' => $acteurPrincipal,
                    'note' => $note,
                ]);
        }

        // Sinon, c'est qu'on a une image, on fait la requête avec l'image :
        else {

            $req = "UPDATE films 
                    SET(titre = :titre, genre = :genre, duree = :duree, date_de_sortie = :date_de_sortie, realisateur = :realisateur, acteur_principal = :acteur_principal, note = :note, image = :image)
                    WHERE id = :id"; // IMPORTANT, on n'oublie pas le WHERE lors d'un UPDATE ou d'un DELETE !!!

                $res = $bdd->prepare($req);

                $res->execute([
                    'id'    => $idEdit, // On ajoute $idEdit à l'execute du UPDATE !
                    'titre' => $titre,
                    'genre' => $genre,
                    'duree' => $duree,
                    'date_de_sortie' => $dateDeSortie,
                    'realisateur' => $realisateur,
                    'acteur_principal' => $acteurPrincipal,
                    'note' => $note,
                    'image' => $image
                ]);
        }
    }
}


...
```