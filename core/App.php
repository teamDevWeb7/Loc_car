<?php
namespace Core;
// objet general pour la gestion du site ->manager des objets

// sur composer on a recup guzzlehttp/psr7
// copie le lien, colle dans terminal

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class App{

        public function run(ServerRequestInterface $request) : ResponseInterface{
                // recup url sans le localhost
            $uri=$request->getUri()->getPath();
                // url->string->tableau
                // dernier index c'est 0-1
            if(!empty($uri) && $uri[-1]==='/' && $uri != '/public/'){
                // si uri pas vide et que dernier carac=/ et que uri entiere pas = juste /
                // renvoie pas reponse pour pas etre dependant de guzzle mais une repsonse qui implemente la response interface
                // withStatus pour redirection, withheader avec soustraction du /
                return (new Response())->withStatus(301)->withHeader('Location', substr($uri, 0, -1));
            }return new Response(200, [], $uri);
        }
}

?>