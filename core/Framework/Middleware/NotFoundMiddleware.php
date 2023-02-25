<?php

namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * si la requete arrive ici une erreur 404 est émise
 * Il est possible de rediriger vers une page
 */
class NotFoundMiddleware extends AbstractMiddleware{

    public function process(ServerRequestInterface $request)
    {
        return new Response(404, [], "Page introuvable");
    }
}