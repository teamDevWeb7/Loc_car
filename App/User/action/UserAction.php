<?php

namespace App\User\action;

use Model\Entity\User;
use Core\toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Renderer\RendererInterface;

class UserAction{

    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Toaster $toaster;
    private Router $router;
    private EntityRepository $repository;
    private SessionInterface $session;
    private EntityManager $manager;

    public function __construct(ContainerInterface $container){
        $this->container=$container;
        $this->renderer=$container->get(RendererInterface::class);
        $this->toaster=$container->get(Toaster::class);
        $this->router=$container->get(Router::class);
        $this->repository=$container->get(EntityManager::class)->getRepository(User::class);
        $this->session=$container->get(SessionInterface::class);
        $this->manager=$container->get(EntityManager::class);

        // enregistrer une globale -> use partout trkl
        // cf layout.html pour la vue
        // condition vue dynamique
        $user=$this->session->get('auth');
        if($user){
            $this->renderer->addGlobale('user', $user);
        }
    }

    public function logView(ServerRequest $request){
        return $this->renderer->render('@user/forms');
    }

    public function signin(ServerRequest $request){
        $auth=$this->container->get(UserAuth::class);
        $data=$request->getParsedBody();
        $validator=new Validator($data);
        $errors=$validator->required('ins_nom', 'ins_prenom','ins_email', 'ins_mdp', 'ins_mdp_confirme')
                            ->email('ins_email')
                            ->strSize('ins_mdp', 12, 50)
                            ->confirme('ins_mdp')
                            // premier mail nom clé mais dernier mail est nom ds la BDD 
                            ->isUnique('ins_email', $this->repository, 'mail')
                            ->getErrors();
        if($errors){
            foreach($errors as $error){
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }
        $form=[
            'nom'=>$data['ins_nom'],
            'prenom'=>$data['ins_prenom'],
            'mail'=>$data['ins_email'],
            'mdp' => $data['ins_mdp']
        ];
        $result=$auth->signIn($form);
        if($result !== true){
            return $result;
        }
        $this->toaster->makeToast("Inscription reussie, vous pouvez vous connecter", Toaster::SUCCESS);
        return $this->redirect('user.login');
    }

    public function update(ServerRequest $request){
        $user=$this->session->get('auth');

        $method=$request->getMethod();

        if($method === 'POST'){
            // retrouver l id du user pour faire les modifs en BDD
            $user = $this->repository->find($user->getId());
            $data=$request->getParsedBody();
            $validator=new Validator($data);
            $errors=$validator
                        ->required('mdp_confirm')
                        ->getErrors();

            if($errors){
                foreach($errors as $error){
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return $this->redirect('user.update');
            }


            if(password_verify($data['mdp_confirm'], $user->getPassword())){
                // je rentre bien ds ma condition
                
                if(!empty($data['mdp'])|| !empty($data['mdp_confirme'])){
                    if(empty($data['mdp']) || empty($data['mdp_confirme'])){
                        $this->toaster->makeToast('Tu remplis les deux ou aucun', Toaster::ERROR);
                        return $this->redirect('user.update');
                    }else{
                        // cas 2 remplis
                        $validator=new Validator($data);
                        $errors=$validator->required('mdp', 'mdp_confirme')
                            ->strSize('mdp', 12, 50)
                            ->confirme('mdp')
                            ->getErrors();
                        if($errors){
                            foreach($errors as $error){
                                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                            }
                            return $this->redirect('user.update');
                        }else{

                        $hash=password_hash($data['mdp'], PASSWORD_BCRYPT);
                        $user->setPassword($hash);
                        }
                    }
                }

                if(($data['nom']===$user->getNom()) && ($data['prenom']===$user->getPrenom())){
                    $this->toaster->makeToast('Faut changer un truc avant d envoyer', Toaster::ERROR);
                        return $this->redirect('user.update');

                }

                $user->setNom($data['nom'])
                        ->setPrenom($data['prenom']);

                $this->manager->flush();
                // mettre à jour la session sur les infos bdd va avec le find('id');
                $this->session->set('auth', $user);


                $this->toaster->makeToast('Les modifications sont prises en compte', Toaster::SUCCESS);
                return $this->redirect('user.update');
                // pas pris en compte ds BDD
                // et pb hearder qd pas co qd meme header user

            }else{
                $this->toaster->makeToast('Déso, tu t\'es planté sur ton mot de passe tête de noeuds ', Toaster::ERROR);
                return $this->redirect('user.update');
            }

        }else{
            return $this->renderer->render('@user/update', [
                'user'=>$user
            ]);
        }





    }

    public function login(ServerRequest $request){
        $data=$request->getParsedBody();
        $validator=new Validator($data);
        $errors=$validator
                        ->required('mail', 'mdp')
                        ->email('mail')
                        ->getErrors();

        if($errors){
            foreach($errors as $error){
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }
        $auth=$this->container->get(UserAuth::class);
        $res=$auth->login($data['mail'], $data['mdp']);
        if($res== true){
            $this->toaster->makeToast('Connexion reussie', Toaster::SUCCESS);
            return $this->redirect('user.home');
        }
        $this->toaster->makeToast("Connexion échouée, vérifiez vos informations", Toaster::ERROR);
        return $this->redirect('user.login');
    }

    public function home(ServerRequest $request){
        // render une vue !== rediriger
        $user=$this->session->get('auth');
        return $this->renderer->render('@user/home', [
            'user'=>$user
        ]);
    }

    public function logout(){
        $auth=$this->container->get(UserAuth::class);
        $auth->logout();
        $this->toaster->makeToast('Déconnexion réussie', Toaster::SUCCESS);
        return (new Response())
            ->withHeader('Location', '/user/login');
    }
}