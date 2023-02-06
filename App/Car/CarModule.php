<?php
namespace App\Car;

use Model\Entity\Vehicule;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;
use GuzzleHttp\Psr7\Response;

class CarModule extends AbstractModule{
    private Router $router;
    private RendererInterface $renderer;
    private $repository;
    private EntityManager $manager;
    public const DEFINTIONS =__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        // manager class qui gere entities, manager va dire à autre class de faire action
        $this->router=$router;
        $this->renderer=$renderer;

        // getRepo -> va chercher entité (schéma represente table) repo = class qui contient requete possible sur modele
        $this->repository= $manager->getRepository(Vehicule::class);

        $this->manager=$manager;

        $this->renderer->addPath('Car', __DIR__.DIRECTORY_SEPARATOR.'view');

        $this->router->get('/addVehicule', [$this, 'addVehicule'], 'Car.add');

        $this->router->get('/listCar', [$this, 'listCar'], 'Car.list');

        $this->router->post('/addVehicule', [$this, 'addCar']);

        $this->router->get('/infoCar', [$this, 'infoCar'], 'Car.info');

        // {:id} dire qu'on a var dynamique, d pr chiffre, + pr nbr
        $this->router->get('/update/{id:[\d]+}', [$this, 'update'], 'car.update');
    }

    public function addCar(ServerRequestInterface $request){
        $method= $request->getMethod();

        if($method === 'POST'){
            $data=$request->getParsedBody();
            $vehicule = new Vehicule();
            $vehicule->setModel($data['modele'])
                        ->setMarque($data['marque'])
                        ->setColor($data['couleur']);

            // enregistrer ds bdd = prepare
            $this->manager->persist($vehicule);

            // flush = execute
            $this->manager->flush();

            return (new Response)
                ->withHeader('Location', '/listCar');
        }
        return $this->renderer->render('@Car/addVehicule');
    }


    // avoir liste voitures ajoutées
    public function listCar(ServerRequestInterface $request): string{
        $voitures= $this ->repository->findAll();
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

    public function update(ServerRequestInterface $request): string{

        return "";
    }


}

?>