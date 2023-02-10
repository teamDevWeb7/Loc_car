<?php
namespace App\Car\Action;

use Model\Entity\Marque;
use Core\toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class CarAction{
    // déclaration
    private RendererInterface $renderer;
    private EntityManager $manager;
    private Toaster $toaster;
    private $marqueRepository;
    private $repository;

    // injection de dépendences
    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster)
    {
        // assignation valeurs
        $this->renderer=$renderer;
        $this->manager=$manager;
        $this->toaster=$toaster;
        $this->marqueRepository=$manager->getRepository(Marque::class);
        $this->repository=$manager->getRepository(Vehicule::class);
    }

    /**
     * Methode ajoutant un véhicule en BDD
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function addCar(ServerRequestInterface $request){
        $method= $request->getMethod();

        if($method === 'POST'){
            $data=$request->getParsedBody();
            $validator=new Validator ($data);
            $errors=$validator->required('modele', 'couleur', 'marque')->getErrors();
            if($errors){
                foreach($errors as $error){
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return(new Response())
                                    ->withHeader('Location', '/addVehicule');
            }

            $voitures=$this->repository->findAll();
            $marque=$this->marqueRepository->find($data['marque']);
            if ($marque){
                foreach($voitures as $voiture){
                    if($voiture->getModel()===$data['modele']
                    &&$voiture->getMarque()===$marque
                    &&$voiture->getColor()===$data['couleur']){
                        $this->toaster->makeToast('Cette voiture existe déjà', Toaster::ERROR);
                        // return $this->renderer->render('@Car/list',[
                        //     "voitures"=>$voitures
                        // ]);
                        return (new Response)
                        ->withHeader('Location', '/listCar');

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


    /**
     * Retourne la liste des véhicules en BDD
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    // avoir liste voitures ajoutées
    public function listCar(ServerRequestInterface $request): string{
        $voitures= $this->repository->findAll();
        return $this->renderer->render('@Car/list', [
            "voitures"=>$voitures
        ]);
    }
    

    /**
     * Retourne les infos d'un véhicule en particulier
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function infoCar(ServerRequestInterface $request):string{
    
        $id=$request->getAttribute('id');
    
        $voiture= $this->repository->find($id);
    
        return $this->renderer->render('@Car/infoCar', ["voiture"=>$voiture]);
    }


    /**
     * permet la modification d'une voiture selon l'id 
     *
     * @param ServerRequestInterface $request
     * @return void
     */
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
                $this->toaster->makeToast('Voiture modifiée avec succès', Toaster::SUCCESS);
                return (new Response)
                    ->withHeader('Location', '/listCar');
            }
    
            $marques=$this->marqueRepository->findAll();
    
            return $this->renderer->render('@Car/update', ["voiture"=>$vehicule, 'marques'=>$marques]);
    }
    
    
    /**
     * permet la suppression d'un enregistrement d'un voiture
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function remove(ServerRequestInterface $request){
        $id=$request->getAttribute('id');
        $vehicule= $this->repository->find($id);
    
        $this->manager->remove($vehicule);
    
        $this->manager->flush();
    
        $this->toaster->makeToast('Voiture supprimée avec succès', Toaster::SUCCESS);
    
        return (new Response)
                ->withHeader('Location', '/listCar');
    }
}