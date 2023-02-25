<?php
namespace App\Car\Action;

use Model\Entity\Marque;
use Core\toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class CarAction{
    // use d'un trait à l'inté de la class
    use RedirectTrait;
    // déclaration
    private RendererInterface $renderer;
    private EntityManager $manager;
    private Toaster $toaster;
    private $marqueRepository;
    private $repository;
    private ContainerInterface $container;
    private Router $router;


    // injection de dépendences
    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster, ContainerInterface $container)
    {
        // assignation valeurs
        $this->renderer=$renderer;
        $this->container=$container;
        $this->router=$container->get(Router::class);
        $this->manager=$manager;
        $this->toaster=$toaster;
        // repository servent à manipuler les marques en BDD
        $this->marqueRepository=$manager->getRepository(Marque::class);
        // manipuler les vehicules en BDD
        $this->repository=$manager->getRepository(Vehicule::class);
    }

    /**
     * Methode ajoutant un véhicule en BDD
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function addCar(ServerRequestInterface $request){
        // recup method used pr requete
        $method= $request->getMethod();

        // post -> formulaire soumis
        if($method === 'POST'){
            // recup contenu $_POST(value ds input)
            $data=$request->getParsedBody();
            // recup contenu $_FILES a index img 
            $file=$request->getUploadedFiles()['img'];

            // instancie validator en lui donnant le tab des données à valider
            $validator=new Validator ($data);
            // on fixe des rules et recup erreurs s'il y en a ou null
            $errors=$validator->required('modele', 'couleur', 'marque')->getErrors();
            // si erreurs on crée un toast par erreur et redirige le user
            if($errors){
                foreach($errors as $error){
                    // créa toast
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                // redirection
                return $this->redirect('Car.add');
            }

            $voitures=$this->repository->findAll();

                // 2eme partie img
                // check img conforme (cf comms methode)
                $error=$this->fileGuard($file);
                // si erreur return toast qui a deja ete generé par fileGuard
                if($error !== true){
                    return $error;
                }
                // si ok, on recup le nom
                $fileName=$file->getClientFileName();
                // assemble nom defichier avec le chemin du dossier ou il sera enregistré
                $imgPath=$this->container->get('img.basePath').$fileName;
                // try to déplacer au chemin voulu
                $file->moveTo($imgPath);
                // si deplacement n est pas possible on crée un toast et on redirige
                if(!$file->isMoved()){
                    // on check si a bougé car que 
                    $this->toaster->makeToast("Une erreur s'est produite", Toaster::ERROR);
                    return $this->redirect('Car.add');
                }

                // si tout s est bien passé on instancie
                $vehicule = new Vehicule();

                // recup objet represente marque choisie
                $marque=$this->marqueRepository->find($data['marque']);
                // si marque recuperee complete infos
                if ($marque){
                    foreach($voitures as $voiture){
                        if($voiture->getModel()===$data['modele']
                        &&$voiture->getMarque()===$marque
                        &&$voiture->getColor()===$data['couleur']){
                            $this->toaster->makeToast('Cette voiture existe déjà', Toaster::ERROR);
                            // return $this->renderer->render('@Car/list',[
                            //     "voitures"=>$voitures
                            // ]);
                            return $this->redirect('Car.list');
    
                        }
                    }
                // complétion infos
                $vehicule->setModel($data['modele'])
                ->setMarque($marque)
                ->setColor($data['couleur'])
                ->setImgPath($fileName);


                // enregistrer ds bdd = prepare
                $this->manager->persist($vehicule);

                // flush = execute
                $this->manager->flush();
            }

            $this->toaster->makeToast('Voiture créée avec succès', Toaster::SUCCESS);
            return (new Response)
                ->withHeader('Location', '/admin/listCar');
        }
        // on recup les marques
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
    public function listCar(ServerRequestInterface $request){
        // recup
        $voitures= $this->repository->findAll();
        // rend vue avec vehicules


        // si admin
        return $this->renderer->render('@Car/list', [
            "voitures"=>$voitures
        ]);
    }

    public function listeCar(ServerRequestInterface $request){
        // recup
        $voitures= $this->repository->findAll();
        // rend vue avec vehicules

        // pb faire une condition si pas admin
        return $this->renderer->render('@Car/Car_List', [
            "voitures"=>$voitures
        ]);
        // faudrait passer l info que c un user pour avoir le bon layout

    }
    

    /**
     * Retourne les infos d'un véhicule en particulier
     *
     * @param ServerRequestInterface $request
     * @return string | Response
     */
    public function infoCar(ServerRequestInterface $request):string{
    
        // recup id ds request
        $id=$request->getAttribute('id');
    
        // associe id à la voiture
        $voiture= $this->repository->find($id);

        // ds le cas ou manuellement le user met un vehicule qui existe pas
        if(!$voiture){
            return new Response(404,[], 'Aucun véhicule ne correspond');
        }
    
        // rend vue avec infos du bon vehicule
        return $this->renderer->render('@Car/infoCar', ["voiture"=>$voiture]);
    }

    public function infoCarAdmin(ServerRequestInterface $request):string{
    
        // recup id ds request
        $id=$request->getAttribute('id');
    
        // associe id à la voiture
        $voiture= $this->repository->find($id);

        // ds le cas ou manuellement le user met un vehicule qui existe pas
        if(!$voiture){
            return new Response(404,[], 'Aucun véhicule ne correspond');
        }
    
        // rend vue avec infos du bon vehicule
        return $this->renderer->render('@Car/infoCarAdmin', ["voiture"=>$voiture]);
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
                // recup fichiers si en a 
                $file=$request->getUploadedFiles();
          
                // check si fichier a ete chargé et pas erreur chargement
                if(sizeof($file)>0 && $file['img']->getError() !==4){

                    // recup nom ancienne img
                    $vieillePhoto=$vehicule->getImgPath();
                    // recup new img
                    $newImg=$file['img'];
                    $fileName=$newImg->getClientFileName();
                    // ajoute nom img au chemin du dossier
                    $imgPathNew=dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'public'. DIRECTORY_SEPARATOR. 'assets' . DIRECTORY_SEPARATOR.'imgs'. DIRECTORY_SEPARATOR.$fileName;
                    // check pb avec new img
                    $error=$this->fileGuard($newImg);
                    // si pb 
                    if($error){
                        return $error;
                    }
                    // deplace
                    $newImg->moveTo($imgPathNew);
                    // si img a été déplacée
                    if($newImg->isMoved()){
                        // lir nv img avec voiture
                        $vehicule->setImgPath($fileName);
                        // suppression old img
                        $oldPath=$this->container->get('img.basePath').$vieillePhoto;
                        // la methode get du container fonctionne avec php DI
                        // on use la clé pr remplacer le chemin

                        unlink($oldPath);
                    }
                }


                
                $vehicule->setModel($data['model'])
                            ->setMarque($marque)
                            ->setColor($data['couleur']);
    
    
    
                // flush = execute
                $this->manager->flush();
                $this->toaster->makeToast('Voiture modifiée avec succès', Toaster::SUCCESS);
                return $this->redirect('Car.list');
            }
    
            // si method get
            $marques=$this->marqueRepository->findAll();
    
            // rend la vue avec marques et vehicule en BDD
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

        // suppr photos
        // si plusieurs aurait dû boucler
        $vieillePhoto=$vehicule->getImgPath();
        $oldPath=$this->container->get('img.basePath').$vieillePhoto;
        unlink($oldPath);
    
        $this->toaster->makeToast('Voiture supprimée avec succès', Toaster::SUCCESS);
    
        return $this->redirect('Car.list');
    }


    /**
     * check si une img est conforme aux restrictions du serveur (mes rules)
     *
     * @param UploadedFile $file
     * @return void
     */
    private function fileGuard(UploadedFile $file){
        // gere erreur du serveur
        if($file->getError()===4){
            $this->toaster->makeToast("Une erreur est survenue lors du chargement", Toaster::ERROR);
            return $this->redirect('Car.add');
        }
        // list decompose contenu tab afin den extraire les values et de les stocker dans des var
        // recup type et format du fichier
        list($type, $format)=explode('/', $file->getClientMediaType());//getClientMediaType renvoie le type mim
        // gere erreur de format
        if(!in_array($type, ['image']) or !in_array($format, ['jpg', 'jpeg', 'png'])){
            $this->toaster->makeToast("Le format de l'image n'est pas accepté, seuls les .png, .jpeg, et .png sont acceptés.", Toaster::ERROR);
            return $this->redirect('Car.add');
        }
        // check taille fichier < 2MO
        if($file->getSize()>2047674){
            $this->toaster->makeToast("La taille de l'image doit etre inférieure à 2MO", Toaster::ERROR);
            return $this->redirect('Car.add');
        }
        return true;
    }
}