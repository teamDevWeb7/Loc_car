<?php
// permet de ne pas mettre le chemin devant l'objet DBB lors de l'instanciation de l'objet
// creation alias
use Core\App;

// aller chercher ce qu'on a pris sur composer
use Core\bdd\BDD;
use App\Car\CarModule;
// use Core\Framework\Renderer\PHPRenderer;

// on demande de charger fonction pr utiliser le package qui sert à afficher réponse
use App\Home\HomeModule;


use App\User\UserModule;
use DI\ContainerBuilder;
use App\Admin\AdminModule;

use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Core\Framework\Renderer\TwigRenderer;
use Core\Framework\Middleware\RouterMiddleware;

// objet qui gere des objets
use Core\Framework\Middleware\NotFoundMiddleware;
use Core\Framework\Middleware\AdminAuthMiddleware;
use Core\Framework\Middleware\TrailingSlashMiddleware;
use Core\Framework\Middleware\RouterDispatcherMiddleware;
use Core\Framework\Middleware\UserAuthMiddleware;

// inclut autoloader de composer
require dirname(__DIR__)."/vendor/autoload.php";

// declare tab modules à charger
$modules = [
    HomeModule::class,
    CarModule::class,
    AdminModule::class,
    UserModule::class
];

// utilisation du builder du container de dependences, builder construit objet container de dep
// != container de dependences
$builder= new ContainerBuilder();
// ajout feuille def principale ds dossier racine
$builder->addDefinitions(dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');


// verif a def et si oui la prend 
foreach($modules as $module){
    if(!is_null($module::DEFINITIONS)){
        // si modules possedent une feuille de config perso, on l ajoute aussi
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// recup instance container de dep
$container=$builder->build();


// instancie application en lui donnant liste modules et container de dep
$app=new App($container, $modules);

// middlewares
$app->linkFirst(new TrailingSlashMiddleware())
    ->linkWith(new RouterMiddleware($container))
    ->linkWith(new AdminAuthMiddleware($container))
    ->linkWith(new UserAuthMiddleware($container))
    ->linkWith(new RouterDispatcherMiddleware())
    ->linkWith(new NotFoundMiddleware());


// si l index n est pas exe à partir de la CLI(command Line Interface)
if(php_sapi_name() !== 'cli'){
// recup response en lançant la methode run et en lui passant un objet ServerRequest
// rempli avec ttes les infos de la requete envoyée par la machine client
$response =$app->run(ServerRequest::fromGlobals());
// on renvoi la rep au server apres avoir transformé le retour de l'appli en une reponse comprehensible pour la machine client
send($response);
}

?>

<!-- 
etapes
maquette soit schema BDD
BDD->s interroger sur corps de metier t besoins
decomposer: ex machine caf
App
Container + config PHP DI
Database Factory


on commence par partie admin 
-->





