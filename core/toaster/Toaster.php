<?php
namespace Core\toaster;

use Core\toaster\Toast;
use Core\Session\SessionInterface;

// gere toast en s'appuant sur session
// toast parce qu'on veut qu'il na'apparaisse qu'une seule fois
// si session apparait toujours, on aurait pu mettre des conditions et tout mais ça aurait surchargé l'objet alors on en a fait un autre pr ces cas particuliers

// creer ext twig pr l'afficher sans que ce soit chiant

class Toaster{
    private const SESSION_KEY='toast';
    const ERROR=0;
    const SUCCESS=2;
    const WARNING=1;
    private SessionInterface $session;
    private Toast $toast;

    public function __construct(SessionInterface $session){
        $this->session=$session;
        $this->toast=new Toast();
    }

    // enregistre le toast
    public function makeToast(string $message, int $etat): void{
        switch($etat){
            case 0:
                $this->session->set(self::SESSION_KEY, $this->toast->error($message));
                break;
            case 1:
                $this->session->set(self::SESSION_KEY, $this->toast->warning($message));
                break;
            case 2:
                $this->session->set(self::SESSION_KEY, $this->toast->success($message));
                break;
        }
    }

    public function renderToast():?string{
        $toast=$this->session->get(self::SESSION_KEY);
        $this->session->delete(self::SESSION_KEY);
        return $toast;
    }

    public function hasToast():bool{
        return $this->session->has(self::SESSION_KEY);
    }
}





?>