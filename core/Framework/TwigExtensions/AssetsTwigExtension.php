<?php
namespace Core\Framework\TwigExtensions;

use Exception;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class AssetsTwigExtension extends AbstractExtension{

    public function getFunctions(){
        return[
            // 1 nomme fonction, 2 lier function existante
            new TwigFunction('assets', [$this, 'asset'])
        ];
    }

    public function asset(string $path):string{

        $file = dirname(__DIR__, 3).'/public/'.$path;
        if(!file_exists($file)){
            throw new Exception("Le fichier $file n'existe pas.");
        }
        // s'assure que style vient du serveur et non pas de l'exterieur, securité pour eviter injection
        $path.='?'.filemtime($file);
        return $path;
    }
}