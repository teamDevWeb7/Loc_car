<?php
namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\TwigRenderer;

class TwigRendererFactory{
    /**
     * methode magique
     * appelée au moment où l'on essaye d'use l'objet comme si il s'agissait d'une fonction
     * ex: $twig= TwigRendererFactory()
     * ne precise pas que je vux instancier l'objet
     * le systeme va appeler la methode invoke
     *
     * @param ContainerInterface $container
     * @return TwigRenderer|null
     */
    public function __invoke(ContainerInterface $container): ?TwigRenderer{
        $loader=new FilesystemLoader($container->get('config.viewPath'));
        $twig=new Environment($loader, []);

        //recup la liste des extensions twig à charger
        $extensions =$container->get("twig.extensions");
        // boucle sur la liste d'extensions et ajout à Twig
        foreach($extensions as $extension){
            $twig->addExtension($container->get($extension));
        }

        return new TwigRenderer($loader, $twig);
    }
}







?>