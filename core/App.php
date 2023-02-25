<?php
namespace Core;
// objet general pour la gestion du site ->manager des objets

// sur composer on a recup guzzlehttp/psr7
// copie le lien, colle dans terminal

// use Core\Framework\Renderer\PHPRenderer;

use Core\Framework\Middleware\AbstractMiddleware;
use Core\Framework\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Router\Router;
use Exception;
use Psr\Container\ContainerInterface;

class App{

    private Router $router;
    // liste modules instanciés
    private array $modules;

    private ContainerInterface $container;

    private MiddlewareInterface $middleware;

    /**
     * Initialise la liste des modules et enregistre le container
     *
     * @param ContainerInterface $container
     * @param array $modules
     */
    public function __construct(ContainerInterface $container, array $modules= [])
    {
        // charger modules et instancier
        $this->router= $container->get(Router::class);
        // $dependencies['renderer']->addGlobale('router', $this->router);

        // foreach($modules as $module){
        //     $this->modules[]= new $module($this->router, $dependencies['renderer']);
        // }

        foreach($modules as $module){
            $this->modules[]=$container->get($module);
        }

        $this->container=$container;

    }

        /**
         * traite requete serveur en
         * lançant chaine responsabilité -> middleware
         *
         * @param ServerRequestInterface $request
         * @return ResponseInterface
         */
        public function run(ServerRequestInterface $request) : ResponseInterface{

            return $this->middleware->process($request);
        }


        /**
         * enregistre le premier middleware de la chaine de responsabilité
         *
         * @param MiddlewareInterface $middleware
         * @return MiddlewareInterface
         */
        public function linkFirst(MiddlewareInterface $middleware):MiddlewareInterface{
            $this->middleware=$middleware;
            return $middleware;
        }


        /**
         * retourne instance de PHP DI
         *
         * @return ContainerInterface
         */
        public function getContainer():ContainerInterface{
            return $this->container;
        }
}


?>