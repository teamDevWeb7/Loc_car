<?php
namespace Core\Framework\Router;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


/**
 * extension twig permettant d'appeler des fonctions definies du router à l'inté des vues twig
 */
class RouterTwigExtension extends AbstractExtension{

    private Router $router;

    /**
     * recup et enregistre instance du router
     *
     * @param Router $router
     */
    public function __construct(Router $router){
        $this->router=$router;
    }


    /**
     * declare fonctions dispo côté vue
     *
     * @return void
     */
    public function getFunctions()
    {
        return[
            new TwigFunction('path', [$this, 'path'])
        ];
    }

    // 1 nom de la route, 2 tableau contient paramètres
    /**
     * fait appel à la methode generateUri() du router et return son result
     *
     * @param string $name
     * @param array $params [optionnel]
     * @return string
     */
    public function path(string $name, array $params =[]): string{
        return $this->router->generateUri($name, $params);
    }
}





?>