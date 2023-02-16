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
use GuzzleHttp\Psr7\UploadedFile;

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
            // recup img
            $file=$request->getUploadedFiles()['img'];

            $validator=new Validator ($data);
            $errors=$validator->required('modele', 'couleur', 'marque')->getErrors();
            if($errors){
                foreach($errors as $error){
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return(new Response())
                                    ->withHeader('Location', '/admin/addVehicule');
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
                        ->withHeader('Location', '/admin/listCar');

                    }
                }
                // 2eme partie img
                $this->fileGuard($file);
                $fileName=$file->getClientFileName();
                $imgPath=dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'public'. DIRECTORY_SEPARATOR. 'assets' . DIRECTORY_SEPARATOR.'imgs'. DIRECTORY_SEPARATOR.$fileName;
                $file->moveTo($imgPath);
                if(!$file->isMoved()){
                    // on check si a bougé car que 
                    $this->toaster->makeToast("Une erreur s'est produite", Toaster::ERROR);
                    return(new Response())
                    ->withHeader('Location', '/admin/addVehicule');
                }


                $vehicule = new Vehicule();
                $vehicule->setModel($data['modele'])
                ->setMarque($marque)
                ->setColor($data['couleur'])
                ->setImgPath($imgPath);


                // enregistrer ds bdd = prepare
                $this->manager->persist($vehicule);

                // flush = execute
                $this->manager->flush();
            }

            $this->toaster->makeToast('Voiture créée avec succès', Toaster::SUCCESS);
            return (new Response)
                ->withHeader('Location', '/admin/listCar');
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
                    ->withHeader('Location', '/admin/listCar');
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
                ->withHeader('Location', '/admin/listCar');
    }

    private function fileGuard(UploadedFile $file){
        // gere erreur du serveur
        if($file->getError()===4){
            $this->toaster->makeToast("Une erreur est survenue lors du chargement", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addVehicule');
        }
        // list permet de recup tt ce qui a ete gere apres = et de les ranger dans les var entre parenthèses
        list($type, $format)=explode('/', $file->getClientMediaType());
        // gere erreur de format
        if(!in_array($type, ['image']) or !in_array($format, ['jpg', 'jpeg', 'png'])){
            $this->toaster->makeToast("Le format de l'image n'est pas accepté, seuls les .png, .jpeg, et .png sont acceptés.", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addVehicule');
        }
        // check taille fichier
        if($file->getSize()>2047674){
            $this->toaster->makeToast("La taille de l'image doit etre inférieure à 2MO", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addVehicule');
        }
        return true;
    }
}