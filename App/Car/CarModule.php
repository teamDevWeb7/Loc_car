<?php
namespace App\Car;

use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class CarModule extends AbstractModule{
    private Router $router;
    private RendererInterface $renderer;
    private $repository;
    public const DEFINTIONS =__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        // manager class qui gere entities, manager va dire à autre class de faire action
        $this->router=$router;
        $this->renderer=$renderer;

        // getRepo -> va chercher entité (schéma represente table) repo = class qui contient requete possible sur modele
        $this->repository= $manager->getRepository(Vehicule::class);

        $this->renderer->addPath('Car', __DIR__.DIRECTORY_SEPARATOR.'view');

        $this->router->get('/addVehicule', [$this, 'addVehicule'], 'Car.add');

        $this->router->get('/listCar', [$this, 'listCar'], 'Car.list');

        $this->router->post('/addVehicule', [$this, 'saveCar']);

        $this->router->get('/infoCar', [$this, 'infoCar'], 'Car.info');
    }

    public function addVehicule(): string{
        return $this->renderer->render('@Car/addVehicule');
    }

    public function saveCar(ServerRequestInterface $request): string{
        $data=$request->getParsedBody();
        // var_dump($data);
        return $this->renderer->render('@Car/addVehicule');
    }

    // avoir liste voitures ajoutées
    public function listCar(ServerRequestInterface $request): string{
        $voitures=[
            [
                "model"=>"206",
                "marque"=>"Peugeot",
                "couleur"=>"Bleu"
            ],
            [
                "model"=>"Golf",
                "marque"=>"VW",
                "couleur"=>"Vert"
            ]
        ];
        return $this->renderer->render('@Car/list', [
            "voitures"=>$voitures
        ]);
    }

    public function infoCar(ServerRequestInterface $request):string{
        $voiture= [
                "model"=>"206",
                "marque"=>"Peugeot",
                "couleur"=>"Bleu"
            ];

        return $this->renderer->render('@Car/infoCar', ["voiture"=>$voiture]);
    }


}

?>