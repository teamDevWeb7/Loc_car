<?php
namespace Core\Framework\Auth;

use Model\Entity\Admin;
use Doctrine\ORM\EntityManager;
use Core\Session\SessionInterface;

class AdminAuth{

    private EntityManager $manager;

    private SessionInterface $session;


    public function __construct(EntityManager $manager, SessionInterface $session){
        $this->manager=$manager;
        $this->session=$session;
    }

    public function login(string $email, string $password): bool{
        $admin=$this->manager->getRepository(Admin::class)
            ->findOneBy(["mail"=>$email]);


        // si objet admin pas null et password correspond a celui bdd on ouvre une session admin et on renvoie true
        if($admin && password_verify($password, $admin->getPassword())){
            $this->session->set('auth', $admin);
            return true;
        }
        return false;
    }

    public function logout():void{
        $this->session->delete('auth');
    }

    public function isLogged():bool{
        // verif si user connecté
        return $this->session->has('auth');
    }

    public function isAdmin():bool{
        if($this->isLogged()){
            // instance of permet savoir si instance de le entité admin
            return $this->session->get('auth') instanceof Admin;
        }
        return false;
    }
}