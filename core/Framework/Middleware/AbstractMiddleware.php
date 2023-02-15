<?php
namespace Core\Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractMiddleware implements MiddlewareInterface{
    
    protected MiddlewareInterface $next;

    public function linkWith(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->next=$middleware;
        return $middleware;
        // si retourne middleware je pourrai linker ce middleware
    }

    public function process(ServerRequestInterface $request)
    {
        // on appelle middleware suivant et on lui demande d'exe sa responsabilité
        return $this->next->process($request);
    }
}





?>