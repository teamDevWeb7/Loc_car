<?php
namespace Core\Framework\Renderer;

// interface->possibilité remplacer renderer et use un autre système
// ou si veut tronsformer en API
// api = Renvoie données peut être contacté par autre chose -appli ou site qui veut données. Ne renvoie pas de vue

interface RendererInterface{
    // ajouter chemins, lier chemins à namespace
    public function addPath(string $namespace, ?string $path=null):void;
    // afficher
    public function render(string $view, array $params=[]):string;
}


?>