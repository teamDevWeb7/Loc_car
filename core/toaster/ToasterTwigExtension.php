<?php
namespace Core\toaster;

use Twig\TwigFunction;
use Core\toaster\Toaster;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;

class ToasterTwigExtension extends AbstractExtension{

    private Toaster $toaster;

    public function __construct(ContainerInterface $container){
        $this->toaster= $container->get(Toaster::class);
    }

    public function getFunctions(){
        return [
            // twigFunction ->truc de twig va comprendre tout seul que ce sont les 2 functions d'ecrites plus bas, mais a gerer dans config
            new TwigFunction('hasToast', [$this, 'hasToast']),
            new TwigFunction('renderToast', [$this, 'render'], ['is_safe'=>['html']])
        ];
    }

    public function hasToast(): bool{
        return $this->toaster->hasToast();
    }

    public function render():array{
        return $this->toaster->renderToast();
    }
}





?>