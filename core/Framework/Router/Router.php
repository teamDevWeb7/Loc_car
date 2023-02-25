<?php
namespace Core\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;
use Core\Framework\Router\Route;

class Router{
    private FastRouteRouter $router;
    private array $routes=[];

    /**
     * instancie un fastRouteRouter et l enregistre
     */
    public function __construct(){
        $this->router=new FastRouteRouter();
    }

    //rajouter routes dispo en get et en post


    // callable pour appellable, qql chose qu'on appelle
    public function get(string $path, $callable, string $name): void{
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
        $this->routes[]=$name;

    }

    // chemin existe deja en get alors pas besoin de $name
    public function post(string $path, $callable, string $name=null): void{
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));

    }


    // s'attend a requte
    /**
     * check que url correspondent à url deja enregistrée, (get ou post) et le nom de la route
     * si oui retourne un objet route qui correspond
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request):?Route{

        $result=$this->router->match($request);
        if($result->isSuccess()){
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }
        return null;
    }

    /**
     * genere des uri = fin url en fonction de son nom
     * [optionnel]: On peut ajouter un [] de params
     *
     * @param string $nameRoad
     * @param array|null $params [optionnel]
     * @return string|null
     */
    public function generateUri(string $nameRoad, ?array $params=[]): ?string{
        return $this->router->generateUri($nameRoad, $params);
    }
}


?>