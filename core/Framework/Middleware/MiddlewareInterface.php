<?php
namespace Core\Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

// Interface sert à définir signatures methodes

interface MiddlewareInterface{
    public function linkWith(MiddlewareInterface $middleware):MiddlewareInterface;
    public function process(ServerRequestInterface $request);
}


?>