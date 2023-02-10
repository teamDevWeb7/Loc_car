<?php
namespace App\Car\Action;

use Model\Entity\Marque;
use Core\toaster\Toaster;
// use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class MarqueAction{

    private RendererInterface $renderer;
    private EntityManager $manager;
    private Toaster $toaster;
    private $marqueRepository;
    // private $repository;

    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster){

        $this->renderer=$renderer;
        $this->manager=$manager;
        $this->toaster=$toaster;
        $this->marqueRepository=$manager->getRepository(Marque::class);
        // $this->repository=$manager->getRepository(Vehicule::class);
    }


    /**
     * ajouter une marque, vérif pas de doublons
     *
     * @param ServerRequestInterface $request
     * @return void
     */
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

    /**
     * afficher la liste des marques
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function marqueList(ServerRequestInterface $request){
        $marques=$this->marqueRepository->findAll();
        return $this->renderer->render('@Car/listMarque', ['marques'=>$marques]);
    }

    /**
     * mettre à jour un nom de marque pas finie
     *
     * @param ServerRequestInterface $request
     * @return void
     */
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


    /**
     * supprimer une marque
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function remove(ServerRequestInterface $request){
        $id=$request->getAttribute('id');
        $marque= $this->marqueRepository->find($id);
    
        $this->manager->remove($marque);
    
        $this->manager->flush();
    
        $this->toaster->makeToast('Marque supprimée avec succès', Toaster::SUCCESS);
    
        return (new Response)
                ->withHeader('Location', '/marqueList');
    }
}