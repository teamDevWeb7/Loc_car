<?php

namespace App\User\action;

use Core\Framework\Auth\UserAuth;
use Core\toaster\Toaster;
use Core\Framework\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\RedirectTrait;

class UserAction{

    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Toaster $toaster;
    private Router $router;

    public function __construct(ContainerInterface $container){
        $this->container=$container;
        $this->renderer=$container->get(RendererInterface::class);
        $this->toaster=$container->get(Toaster::class);
        $this->router=$container->get(Router::class);
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
                            ->strSize('ins-mdp', 12, 50)
                            ->confirme('mdp')
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
            
        ];
        $result=$auth->signIn($form);
        if($result !== true){
            return $result;
        }
        $this->toaster->makeToast("Inscription reussie, vous pouvez vous connecter", Toaster::SUCCESS);
        return $this->redirect('user.login');
    }
}