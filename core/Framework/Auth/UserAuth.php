<?php
namespace Core\Framework\Auth;

use Core\Framework\Router\RedirectTrait;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Core\toaster\Toaster;
use Model\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class UserAuth{
    use RedirectTrait;

    private ContainerInterface $container;
    private EntityManager $manager;
    private Toaster $toaster;
    private Router $router;
    private SessionInterface $session;

    public function __construct(ContainerInterface $container){

        $this->container=$container;
        $this->manager=$container->get(EntityManager::class);
        $this->toaster=$container->get(Toaster::class);
        $this->router=$container->get(Router::class);
        $this->session=$container->get(SessionInterface::class);
    }

    public function signIn(array $data){
        $user=new User();
        $hash=password_hash($data['mdp'], PASSWORD_BCRYPT);
        $user->hydrate($data)
            ->setPassword($hash);

        try{
            $this->manager->persist($user);
            $this->manager->flush();
            return true;
        }catch(\Exception $e){
            $this->toaster->makeToast("Une erreur est survenue", Toaster::ERROR);
            return $this->redirect(('user.login'));
        }
    }

    public function isLogged():bool{
        // verif si user connecté
        return $this->session->has('auth');
    }

    public function isUser():bool{
        if($this->isLogged()){
            // instance of permet savoir si instance de le entité user
            return $this->session->get('auth') instanceof User;
        }
        return false;
    }
    // use de la meme clé dans la session permet de ne pas pouvoir se co en meme temps en tant que user et admin
    // ++++sécu
}