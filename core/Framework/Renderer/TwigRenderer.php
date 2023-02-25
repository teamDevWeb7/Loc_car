<?php
namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface{

    private $twig;
    private $loader;

    /**
     * s'attend à une instance de FileSystem loader et d' Environment
     *
     * @param FilesystemLoader $loader objet recence les chemins vers les diffs dossiers de vue
     * @param Environment $twig objet qui enregistre nos diffs extensions et qui permet de faire communiquer la vue et le controler
     */
    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        // chemin
        $this->loader= $loader;
        // var et choses logiques
        $this->twig=$twig;
    }

    /**
     * permet enregistrer un nv chemin vers un ensemble de vues
     *
     * @param string $namespace si $path est définit $namespace représente un raccourci ou un alias du chemin vers les vues, si $path pas def, contient chemin
     * @param string|null $path si definit contient chemin vers les vues qui seront enregistrées sous la value de $namespace
     * @return void
     */
    public function addPath(string $namespace, ?string $path=null):void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * permet afficher la vue demandée
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params=[]):string{
        return $this->twig->render($view.'.html.twig', $params);
    }

    /**
     * permet de rajouter var gloables qui sont communes à toutes les vues
     *
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function addGlobale(string $key, $value): void{
        $this->twig->addGlobal($key, $value);
    }
}


?>