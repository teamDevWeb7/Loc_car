<?php
namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface{

    private $twig;
    private $loader;

    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        // chemin
        $this->loader= $loader;
        // var et choses logiques
        $this->twig=$twig;
    }

    public function addPath(string $namespace, ?string $path=null):void
    {
        $this->loader->addPath($path, $namespace);
    }

    public function render(string $view, array $params=[]):string{
        return $this->twig->render($view.'.html.twig', $params);
    }

    public function addGlobale(string $key, $value): void{
        $this->twig->addGlobal($key, $value);
    }
}


?>