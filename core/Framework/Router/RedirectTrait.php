<?php

namespace Core\Framework\Router;

use GuzzleHttp\Psr7\Response;

trait RedirectTrait{
    // obj non instanciable, sert que pr fusion
    public function redirect(string $name, array $params=[]){
        $path=$this->router->generateUri($name, $params);
        // si fusionne avec objet qui a une prop router ça va, sinon fonctionne pas
        return (new Response)->withHeader('Location', $path);
    }
}