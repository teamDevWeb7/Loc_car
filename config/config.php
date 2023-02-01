<?php

use Core\bdd\DatabaseFactory;
use Core\Framework\Renderer\TwigRendererFactory;
use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Doctrine\ORM\EntityManager;

return [
    "doctrine.user"=>"root",
    "doctrine.dbname"=>"loc_car",
    "doctrine.mdp"=>"",
    "doctrine.driver"=>"pdo_mysql",
    "doctrine.devMode"=>true,

    "config.viewPath"=>dirname(__DIR__).DIRECTORY_SEPARATOR.'view',
    Router::class=>\DI\create(),
    RendererInterface::class=>\DI\factory(TwigRendererFactory::class),
    EntityManager::class=>\DI\factory(DatabaseFactory::class)
];

?>