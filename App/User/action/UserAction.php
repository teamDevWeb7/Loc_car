<?php

namespace App\User\action;

use Model\Entity\User;
use Core\toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Renderer\RendererInterface;
use Core\Session\SessionInterface;

class UserAction{

    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Toaster $toaster;
    private Router $router;
    private EntityRepository $repository;
    private SessionInterface $session;

    public function __construct(ContainerInterface $container){
        $this->container=$container;
        $this->renderer=$container->get(RendererInterface::class);
        $this->toaster=$container->get(Toaster::class);
        $this->router=$container->get(Router::class);
        $this->repository=$container->get(EntityManager::class)->getRepository(User::class);
        $this->session=$container->get(SessionInterface::class);
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
}