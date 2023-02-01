<?php
namespace App\Home;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Core\Framework\AbstractClass\AbstractModule;

class HomeModule extends AbstractModule{
    // renderer interface pck si veut changer renderer apres ça evite modif de cet objet

    // __DIR__ represente dossier actuel
        public const DEFINITIONS = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

        private Router $router;
        private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        // construc va declarer route, enregistrer chemin, ensuite router et renderer pourront être utilisés pr charger bonne vue
        $this->router=$router;
        $this->renderer=$renderer;

        // dirname, parent du dossier ds lequel on est actuellement
        $this->renderer->addPath('home', __DIR__.DIRECTORY_SEPARATOR.'view');

        // on declare tab vide pck qd appel objet on donne objet et methode
        // rajouter route en get
        // 1 url, chemin
        // 2 function a appelé
        // 3 nom de la route
        $this->router->get('/', [$this, 'index'], 'accueil');
    }

    public function index(){
        return $this->renderer->render('@home/index', ['siteName' => 'JeVendsDesVoitures.com']);
    }
}


?>