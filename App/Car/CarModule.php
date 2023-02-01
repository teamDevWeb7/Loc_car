<?php
namespace App\Car;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\AbstractClass\AbstractModule;

class CarModule extends AbstractModule{
    private Router $router;
    private RendererInterface $renderer;
    public const DEFINTIONS =__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router=$router;
        $this->renderer=$renderer;

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