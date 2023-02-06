<?php
namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\TwigRenderer;

class TwigRendererFactory{
    public function __invoke(ContainerInterface $container): ?TwigRenderer{
        $loader=new FilesystemLoader($container->get('config.viewPath'));
        $twig=new Environment($loader, []);

        $extensions =$container->get("twig.extensions");
        foreach($extensions as $extension){
            $twig->addExtension($container->get($extension));
        }

        return new TwigRenderer($loader, $twig);
    }
}







?>