<?php

namespace Core\Framework\Middleware;

use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;

/**
 * check que l url de la requete corresponde à une route, si oui on enregistre la route dans $request
 * recup attribut de la method get
 * sinon on passe la requete au middlexare suivant sans modif ds le but quelle finisse ds les notFoundMiddleware
 * a besoin du router pour fonctionner(on donne container mais on passe par lui pour acceder au router)
 */
class RouterMiddleware extends AbstractMiddleware{

    private ContainerInterface $container;
    
    public function __construct(ContainerInterface $container){
        $this->container=$container;
    }

    public function process(ServerRequestInterface $request){
        $router=$this->container->get(Router::class);
        $route=$router->match($request);
        if(is_null($route)){
            // appeler la méthode du parent en ne prenant pas en compte ce qui a été surchargé
            // dans ce cas on passe au middleware suivant
            return parent::process($request);
        }
        $params=$route->getParams();

        $request= array_reduce(array_keys($params), function ($request, $key) use ($params){
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $request=$request->withAttribute('_route', $route);

        return parent::process($request);
    }

}