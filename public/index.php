<?php
// permet de ne pas mettre le chemin devant l'objet DBB lors de l'instanciation de l'objet
// creation alias
use Core\bdd\BDD;

// aller chercher ce qu'on a pris sur composer
use GuzzleHttp\Psr7\ServerRequest;
use Core\App;
// on demande de charger fonction pr utiliser le package qui sert à afficher réponse
use function Http\Response\send;

// tout ça pour avoir un autoload généré automatiquement + chemin parfaits car sur serveur c'est le bordel
include dirname(__DIR__)."/vendor/autoload.php";

$app=new App();
// appel methode statique, objet :: methode statique
$response =$app->run(ServerRequest::fromGlobals());
// installation interop : va transformer l'objet Reponse en qql chose interpretable par le client (peux pas echo un objet)
send($response);

// requete http ->ensemble d'informations qui se etrouvent dans var superglob
// pas objet car http pas protocole php donc besoin ServeurRequest pour mettre ses infos dans objet
// on passe ServeurRequest::fromGlobals en para, pour pas etre dependant ce que recoit run va implementer l'interface

// AUTOLOADER MAISON 
// autoloader->que pr objet, trouver le chemin sans taper tout les include à la main
// $class->va savoir que c'est le nom de l objet
// function load($class){
//     $fileName=$class.'.php';
//     if(file_exists("../controler/$fileName")){
//         require_once "../controler/$fileName";
//     }elseif(file_exists("../core/bdd/$fileName")){
//         require_once "../core/bdd/$fileName";
//     }
// }
// dire a php que j'ai créé mon autoloader et le lier à la demande sans d'autre processus
// spl_autoload_register('load');


// dirname pr chemin 
// __dir__ pr dossier actuel
// require dirname(__DIR__)."/view/header.php";

// aller chercher router
// pour pas erreur chargement require once
//require_once dirname(__DIR__)."/core/router.php";
// pour tester le router : http://localhost/loc_car/public/?page=addVehicule

// require dirname(__DIR__)."/core/bdd/BDD.php";

// $bdd= BDD::getInstance('localhost', 'loc_car', 'root', '');
// var_dump($bdd->connection);
// je peux faire ce que je veux comme avec l'objet PDO






// pour installer un package, copier coller le lien composer require..... ds terminal
//pr supprimer, meme lien mais replacer require par remove
?>




<?php
// require dirname(__DIR__)."/view/footer.php";

?>
