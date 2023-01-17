<?php
// autoloader->que pr objet, trouver le chemin sans taper tout les include à la main
// $class->va savoir que c'est le nom de l objet
function load($class){
    $fileName=$class.'.php';
    if(file_exists("../controler/$fileName")){
        require_once "../controler/$fileName";
    }elseif(file_exists("../core/bdd/$fileName")){
        require_once "../core/bdd/$fileName";
    }
}
// dire a php que j'ai créé mon autoloader et le lier à la demande sans d'autre processus
spl_autoload_register('load');


// dirname pr chemin 
// __dir__ pr dossier actuel
require dirname(__DIR__)."/view/header.php";

// aller chercher router
// pour pas erreur chargement require once
require_once dirname(__DIR__)."/core/router.php";
// pour tester le router : http://localhost/loc_car/public/?page=addVehicule

// require dirname(__DIR__)."/core/bdd/BDD.php";

$bdd= BDD::getInstance('localhost', 'loc_car', 'root', '');
var_dump($bdd->connection);
// je peux faire ce que je veux comme avec l'objet PDO
?>




<?php
require dirname(__DIR__)."/view/footer.php";

?>
