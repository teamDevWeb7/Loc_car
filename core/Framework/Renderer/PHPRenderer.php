<?php
namespace Core\Framework\Renderer;

// charger la view
class PHPRenderer implements RendererInterface{
    // pour avoir vue generale
    // si ne fait pas à chaq fois que path est nulle doit rentrer manuellement __MAIN
    const DEFAULT_NAMESPACE ="__MAIN";
    // s'attend à namespace et à un chemin optionnel
    // crea de namspace par defaut que l'on est sur de ne pas use pour un autre namespace

    // pr enregistrer mes chemins
    private array $paths=[];

    private array $globals=[];

    public function __construct(string $defaultPath= null){
        if (!is_null($defaultPath)){
            $this->addPath($defaultPath);
        }
    }
    
// donner chemin jusque dossier
    public function addPath(string $namespace, ?string $path=null):void{
        if(is_null ($path)){
            // si path null ca veut dire que enregistré dans namespace
            $this->paths[self::DEFAULT_NAMESPACE]=$namespace;
        }else{
            $this->paths[$namespace]=$path;
        }
    }

    // render -> donner chemin jusqu'à fichier
    // maniere dt j'ai l'intention d'call method
    // @blog, namespace blog
    // $renderer->render('@blog/addVehicule')
    // layout, vue globale, pas d'arobase
     // $renderer->render('header')
    //  $rendere->render('test', ['name'=>'Cedric'])
    public function render(string $view, array $params=[]):string{
        // doit deter si a namspace ou pas ->@? pour deter la vue
        if($this->hasNamespace($view)){
            $path=$this->replaceNamespace($view).'.php';
        }else{
            // renvoie au dossier view, ensuite on dirige plus precisement sur celui qu'on veut -> $view.php
            // directory_separator=/
            // windows veut /
            // linux veut \
            // directory_separator s'adapte donc à l'environnement
            $path=$this->paths[self::DEFAULT_NAMESPACE].DIRECTORY_SEPARATOR.$view.'.php';
        }
        // buffering=mettre en pause l'exe du script pr attendre un certain moment pr envoyer resultat
        ob_start();
        $renderer=$this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();

    }
    private function hasNamespace(string $view):bool{
        // chaine caract = tableau = si index 0 =@ renvoie true
        return $view[0]==='@';
    }
    private function replaceNamespace(string $view): string{
        // fonction qui recup blog substr recup apres @ et avant /
        // strpos sort 1ere fois que croise un certain caract
        $namespace=substr($view, 1, strpos($view, '/')-1);
        // remplacer @blog par reel chemin 
        // str replace (bout qu'on veut remplacer, par quoi, chaine carac avec quoi on travaille )
        $str=str_replace('@'.$namespace, $this->paths[$namespace], $view);
        // double \ pour echappement 
        return str_replace('/', '\\', $str);

    }

    public function addGlobale(string $key, $value):void{
        $this->globals[$key]=$value;
    }
}



?>