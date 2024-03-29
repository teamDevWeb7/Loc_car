<?php
namespace Core\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;
use Core\Framework\Router\Route;

class Router{
    private FastRouteRouter $router;
    private array $routes=[];

    public function __construct(){
        $this->router=new FastRouteRouter();
    }

    // callable pour appellable, qql chose qu'on appelle
    public function get(string $path, $callable, string $name): void{
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
        $this->routes[]=$name;

    }

    // chemin existe deja en get alors pas besoin de $name
    public function post(string $path, $callable): void{
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST']));

    }


    // s'attend a requte
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

    public function generateUri(string $nameRoad, ?array $params=[]): ?string{
        return $this->router->generateUri($nameRoad, $params);
    }
}


?>