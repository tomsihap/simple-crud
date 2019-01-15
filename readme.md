# simple-crud
Réalisation d'un CRUD basique pour une application PHP.

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
    $maVar = $_POST['titre'];
}
```

> On met dans une nouvelle variable $maVar pour des tests plus tard : en effet, si $maVar existe, c'est que j'ai passé tous les tests propres à cette donnée !

> Pensez à rajouter des validations pour les VARCHAR ! ( strlen(...) )

