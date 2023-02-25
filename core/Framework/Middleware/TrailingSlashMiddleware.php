<?php
namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

// premier middleware retire les shlashs à la fin de l'url si il en a un

class TrailingSlashMiddleware extends AbstractMiddleware{

    // surcharge de methode : on garde ce qu'il y a dedans et on rajoute des choses

    public function process(ServerRequestInterface $request)
    {
                $uri=$request->getUri()->getPath();
                // url->string->tableau
                // dernier index c'est 0-1
            if(!empty($uri) && $uri[-1]==='/' && $uri != '/'){
                // si uri pas vide et que dernier carac=/ et que uri entiere pas = juste /
                // renvoie pas reponse pour pas etre dependant de guzzle mais une repsonse qui implemente la response interface
                // withStatus pour redirection, withheader avec soustraction du /
                return (new Response())->withStatus(301)->withHeader('Location', substr($uri, 0, -1));
            }
            return parent::process($request);
    }
}






?>