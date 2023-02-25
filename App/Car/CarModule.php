<?php
namespace App\Car;


// sert juste à appeler et diriger vers marque et car action qui contiennent les méthodes





// use Model\Entity\Marque;
// use Core\toaster\Toaster;
// use Model\Entity\Vehicule;
use App\Car\Action\CarAction;
use App\Car\Action\MarqueAction;
// use GuzzleHttp\Psr7\Response;
// use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
// use Core\Session\SessionInterface;
use Psr\Container\ContainerInterface;
// use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

/**
 * @inheritDoc
 */
class CarModule extends AbstractModule{
    private Router $router;
    private RendererInterface $renderer;
    // private $repository;
    // private EntityManager $manager;

 
    public const DEFINITIONS =__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    // private $marqueRepository;
    // private Toaster $toaster;
    // private SessionInterface $session;


    /**
     * declare les routes et les methodes dispo pour ce module, definit le chemin vers le dossier de vues du module
     * definit eventuellement des var globales a toutes les vues
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        // manager class qui gere entities, manager va dire à autre class de faire action
        
        // router pr declarer les routes
        $this->router=$container->get(Router::class);
        // renderer pr declarer les vues
        $this->renderer=$container->get(RendererInterface::class);
        // ensemble actions possibles
        $carAction=$container->get(CarAction::class);
        $marqueAction=$container->get(MarqueAction::class);


        // getRepo -> va chercher entité (schéma represente table) repo = class qui contient requete possible sur modele
        // $this->repository= $manager->getRepository(Vehicule::class);

        // enregistrer new on use le manager
        // $this->manager=$manager;

        // declaration du chemin des vues sous le namespace Car
        $this->renderer->addPath('Car', __DIR__.DIRECTORY_SEPARATOR.'view');




        $this->router->get('/admin/addVehicule', [$carAction, 'addCar'], 'Car.add');

        $this->router->get('/admin/listCar', [$carAction, 'listCar'], 'Car.list');

        $this->router->get('/user/listVehicules', [$carAction, 'listeCar'], 'user.list');

        $this->router->post('/admin/listCar', [$carAction, 'listCar']);

        $this->router->post('/admin/addVehicule', [$carAction, 'addCar']);

        $this->router->get('/user/infoCar/{id:[\d]+}', [$carAction, 'infoCar'], 'Car.info');

        $this->router->get('/admin/infoCar/{id:[\d]+}', [$carAction, 'infoCarAdmin'], 'Car.infos');

        // {:id} dire qu'on a var dynamique, d pr chiffre, + pr nbr
        $this->router->get('/admin/update/{id:[\d]+}', [$carAction, 'update'], 'car.update');

        $this->router->post('/admin/update/{id:[\d]+}', [$carAction, 'update']);

        $this->router->get('/admin/delete/{id:[\d]+}', [$carAction, 'remove'], 'car.delete');

        $this->router->get('/admin/addMarque', [$marqueAction, 'addMarque'], 'marque.add');

        $this->router->post('/admin/addMarque', [$marqueAction, 'addMarque']);

        $this->router->get('/admin/marqueList', [$marqueAction, 'marqueList'], 'marque.list');

        $this->router->get('/admin/marqueDelete/{id:[\d]+}', [$marqueAction, 'remove'], 'marque.delete');

        // $this->router->get('/update/{id:[\d]+}', [$carAction, 'update'], 'car.update');

        // $this->router->get('/update/{id:[\d]+}', [$carAction, 'update'], 'car.update');

        $this->router->get('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'updateMarque'], 'marque.updateMarque');

        $this->router->post('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'updateMarque']);
    }





}

?>