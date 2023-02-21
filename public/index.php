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

require dirname(__DIR__)."/vendor/autoload.php";


$modules = [
    HomeModule::class,
    CarModule::class,
    AdminModule::class,
    UserModule::class
];

// utilisation de php DI
$builder= new ContainerBuilder();
// chemin de definitions
$builder->addDefinitions(dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');


// verif a def et si oui la prend 
foreach($modules as $module){
    if(!is_null($module::DEFINITIONS)){
        $builder->addDefinitions($module::DEFINITIONS);
    }
}


$container=$builder->build();



$app=new App($container, $modules);


$app->linkFirst(new TrailingSlashMiddleware())
    ->linkWith(new RouterMiddleware($container))
    ->linkWith(new AdminAuthMiddleware($container))
    ->linkWith(new UserAuthMiddleware($container))
    ->linkWith(new RouterDispatcherMiddleware())
    ->linkWith(new NotFoundMiddleware());


if(php_sapi_name() !== 'cli'){

$response =$app->run(ServerRequest::fromGlobals());
send($response);
}

?>
