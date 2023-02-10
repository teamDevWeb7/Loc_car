<?php
namespace App\Car;

use Model\Entity\Vehicule;
use Model\Entity\Marque;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Session\SessionInterface;
use Core\toaster\Toaster;
use GuzzleHttp\Psr7\Response;

class CarModule extends AbstractModule{
    private Router $router;
    private RendererInterface $renderer;
    private $repository;
    private EntityManager $manager;
    public const DEFINTIONS =__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    private $marqueRepository;
    private Toaster $toaster;
    private SessionInterface $session;

    public function __construct(
        Router $router, 
        RendererInterface $renderer, 
        EntityManager $manager,
        SessionInterface $session,
        Toaster $toaster)
    {
        // manager class qui gere entities, manager va dire à autre class de faire action
        $this->router=$router;
        $this->renderer=$renderer;
        $this->session=$session;
        $this->toaster=$toaster;

        // getRepo -> va chercher entité (schéma represente table) repo = class qui contient requete possible sur modele
        $this->repository= $manager->getRepository(Vehicule::class);

        // enregistrer new on use le manager
        $this->manager=$manager;

        $this->marqueRepository=$manager->getRepository(Marque::class);



        $this->renderer->addPath('Car', __DIR__.DIRECTORY_SEPARATOR.'view');

        $this->router->get('/addVehicule', [$this, 'addCar'], 'Car.add');

        $this->router->get('/listCar', [$this, 'listCar'], 'Car.list');

        $this->router->post('/listCar', [$this, 'listCar']);

        $this->router->post('/addVehicule', [$this, 'addCar']);

        $this->router->get('/infoCar/{id:[\d]+}', [$this, 'infoCar'], 'Car.info');

        // {:id} dire qu'on a var dynamique, d pr chiffre, + pr nbr
        $this->router->get('/update/{id:[\d]+}', [$this, 'update'], 'car.update');

        $this->router->post('/update/{id:[\d]+}', [$this, 'update']);

        $this->router->get('/delete/{id:[\d]+}', [$this, 'remove'], 'car.delete');

        $this->router->get('/addMarque', [$this, 'addMarque'], 'marque.add');

        $this->router->post('/addMarque', [$this, 'addMarque']);

        $this->router->get('/marqueList', [$this, 'marqueList'], 'marque.list');

        // $this->router->get('/updateMarque/{id:[\d]+}', [$this, 'updateMarque'], 'marque.update');

        // $this->router->post('/updateMarque/{id:[\d]+}', [$this, 'updateMarque']);
    }

    public function addCar(ServerRequestInterface $request){
        $method= $request->getMethod();

        if($method === 'POST'){
            $data=$request->getParsedBody();
            $voitures=$this->repository->findAll();
            $marque=$this->marqueRepository->find($data['marque']);
            if ($marque){
                foreach($voitures as $voiture){
                    if($voiture->getModel()===$data['modele']
                    &&$voiture->getMarque()===$marque
                    &&$voiture->getColor()===$data['couleur']){
                        $this->toaster->makeToast('Cette voiture existe déjà', Toaster::ERROR);
                        return $this->renderer->render('@Car/list',[
                            "voitures"=>$voitures
                        ]);
                    }
                }
                $vehicule = new Vehicule();
                $vehicule->setModel($data['modele'])
                ->setMarque($marque)
                ->setColor($data['couleur']);


                // enregistrer ds bdd = prepare
                $this->manager->persist($vehicule);

                // flush = execute
                $this->manager->flush();
            }

            $this->toaster->makeToast('Voiture créée avec succès', Toaster::SUCCESS);
            return (new Response)
                ->withHeader('Location', '/listCar');
        }
        $marques=$this->marqueRepository->findAll();
        // pr manipuler tab dans la vue on passe en params ds render
        return $this->renderer->render('@Car/addVehicule', ['marques'=>$marques]);
    }


    // avoir liste voitures ajoutées
    public function listCar(ServerRequestInterface $request): string{
        $voitures= $this->repository->findAll();
        return $this->renderer->render('@Car/list', [
            "voitures"=>$voitures
        ]);
    }

    public function infoCar(ServerRequestInterface $request):string{

        $id=$request->getAttribute('id');

        $voiture= $this->repository->find($id);

        return $this->renderer->render('@Car/infoCar', ["voiture"=>$voiture]);
    }

    public function update(ServerRequestInterface $request){

        $method= $request->getMethod();
        $id=$request->getAttribute('id');
        $vehicule= $this->repository->find($id);

        if($method === 'POST'){
            // recup tout ce qui a ete envoyé par methode post
            $data=$request->getParsedBody();
            $marque=$this->marqueRepository->find($data['marque']);
            $vehicule->setModel($data['model'])
                        ->setMarque($marque)
                        ->setColor($data['couleur']);



            // flush = execute
            $this->manager->flush();

            return (new Response)
                ->withHeader('Location', '/listCar');
        }

        $marques=$this->marqueRepository->findAll();

        return $this->renderer->render('@Car/update', ["voiture"=>$vehicule, 'marques'=>$marques]);
    }


    public function remove(ServerRequestInterface $request){
        $id=$request->getAttribute('id');
        $vehicule= $this->repository->find($id);

        $this->manager->remove($vehicule);

        $this->manager->flush();

        $this->toaster->makeToast('Voiture supprimée avec succès', Toaster::SUCCESS);

        return (new Response)
                ->withHeader('Location', '/listCar');
    }


    public function addMarque(ServerRequestInterface $request){
        $method= $request->getMethod();

        if($method === 'POST'){
            $data=$request->getParsedBody();

            $marques= $this->marqueRepository->findAll();

            foreach($marques as $marque){
                if($marque->getName()===$data['name']){
                    $this->toaster->makeToast('Cette marque existe déjà', Toaster::ERROR);
                    return $this->renderer->render('@Car/addMarque');
                }
            }

            $marque = new Marque();
            $marque->setName($data['name']);


            // enregistrer ds bdd = prepare
            $this->manager->persist($marque);

            // flush = execute
            $this->manager->flush();

            $this->toaster->makeToast('Marque créée avec succès', Toaster::SUCCESS);

            return (new Response)
                ->withHeader('Location', '/listCar');
        }
        return $this->renderer->render('@Car/addMarque');
    }


    public function marqueList(ServerRequestInterface $request){
        $marques=$this->marqueRepository->findAll();
        return $this->renderer->render('@Car/listMarque', ['marques'=>$marques]);
    }


    // public function updateMarque(ServerRequestInterface $request){
        // $method= $request->getMethod();
        // $id=$request->getAttribute('id');
        // $vehicule= $this->repository->find($id);

        // if($method === 'POST'){
        //     // recup tout ce qui a ete envoyé par methode post
        //     $data=$request->getParsedBody();
        //     $marque=$this->marqueRepository->find($data['marque']);
        //     $vehicule->setModel($data['model'])
        //                 ->setMarque($marque)
        //                 ->setColor($data['couleur']);



        //     // flush = execute
        //     $this->manager->flush();

        //     return (new Response)
        //         ->withHeader('Location', '/listCar');
        // }

        // $marques=$this->marqueRepository->findAll();

        // return $this->renderer->render('@Car/update', ["voiture"=>$vehicule, 'marques'=>$marques]);


    // }


}

?>