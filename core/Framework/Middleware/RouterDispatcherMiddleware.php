<?php
namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

/**
 * si une route a été matchée,appel la fonction liée à la route
 */
class RouterDispatcherMiddleware extends AbstractMiddleware{
    public function process(ServerRequestInterface $request)
    {
        $route=$request->getAttribute('_route');
        if(is_null($route)){
            return parent::process($request);
        }

        $response = call_user_func_array($route->getCallback(), [$request]);

        if($response instanceof ResponseInterface){
            return $response;
        }elseif(is_string($response)){
            return new Response(200, [], $response);
        }else{
            throw new Exception("Réponse du serveur invalide");
        }
    }
}